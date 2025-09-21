<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class ChatController extends Controller
{
    // Từ khóa phân loại ý định người dùng
    private const INTENT_PATTERNS = [
        'product_search' => [
            'primary' => ['tìm', 'có', 'bán', 'mua', 'cần', 'muốn mua', 'sản phẩm', 'loại', 'dùng', 'để'],
            'secondary' => ['giá', 'bao nhiều', 'chi tiết', 'thông tin', 'mô tả', 'xem', 'về']
        ],
        'greeting' => ['xin chào', 'chào', 'hello', 'hi', 'hế lô', 'chào bạn', 'hey'],
        'policy' => ['chính sách', 'bảo hành', 'đổi trả', 'vận chuyển', 'thanh toán', 'ưu đãi', 'khuyến mãi'],
        'general_chat' => ['cảm ơn', 'tạm biệt', 'bye', 'ok', 'được rồi', 'cám ơn', 'tốt', 'hay']
    ];
    
    // Danh mục sản phẩm và từ khóa liên quan - Updated với từ khóa chính xác hơn
    private const PRODUCT_CATEGORIES = [
        'cleaning' => [
            'keywords' => ['nước', 'rửa', 'tẩy', 'làm sạch', 'chén', 'bát', 'đa năng', 'sinh học', 'dọn dẹp', 'tay', 'dishwashing', 'cleaner'],
            'description' => 'sản phẩm làm sạch sinh học'
        ],
        'bags' => [
            'keywords' => ['túi', 'xách', 'canvas', 'vải', 'tote', 'mang', 'đựng đồ', 'shopping', 'laptop', 'dệt', 'nilon', 'nylon', 'balo', 'banner', 'mini'],
            'description' => 'túi xách thân thiện môi trường'
        ],
        'containers' => [
            'keywords' => ['hộp', 'đựng', 'bảo quản', 'chứa', 'container', 'lưu trữ'],
            'description' => 'hộp đựng tái chế'
        ],
        'kitchen' => [
            'keywords' => ['nhà bếp', 'nấu ăn', 'bếp', 'chén', 'bát', 'thìa', 'kitchen'],
            'description' => 'đồ dùng nhà bếp xanh'
        ],
        'personal_care' => [
            'keywords' => ['chăm sóc', 'cá nhân', 'làm đẹp', 'sức khỏe', 'bàn chải', 'răng', 'nobott'],
            'description' => 'sản phẩm chăm sóc cá nhân'
        ]
    ];

    public function geminiChat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userMessage = $request->input('message');
        $geminiApiKey = env('GEMINI_API_KEY');

        if (!$geminiApiKey) {
            return response()->json(['error' => 'Gemini API Key not configured on server.'], 500);
        }

        // Bước 1: Phân tích ý định thông minh hơn
        $intent = $this->analyzeUserIntentAdvanced($userMessage);
        Log::info('Advanced User Intent: ' . $intent . ' for message: ' . $userMessage);

        // Bước 2: Tìm sản phẩm thông minh
        $suggestedProducts = [];
        $availableProducts = $this->getAllAvailableProducts(); // Lấy tất cả sản phẩm có sẵn
        
        Log::info("Available products count: " . count($availableProducts));
        Log::info("Sample products: " . json_encode(array_slice(array_column($availableProducts, 'name'), 0, 3)));
        
        if ($intent === 'product_search' || $this->containsProductKeywords($userMessage)) {
            $suggestedProducts = $this->findRelevantProductsAdvanced($userMessage, $availableProducts);
            Log::info("Suggested products after search: " . json_encode(array_column($suggestedProducts, 'name')));
        }

        // Bước 3: Xây dựng context chi tiết cho AI
        $systemContext = $this->buildAdvancedContext($availableProducts, $suggestedProducts, $intent);
        
        // Bước 4: Tạo prompt thông minh dựa trên context thực tế
        $promptForGemini = $this->buildIntelligentPrompt($userMessage, $intent, $systemContext, $suggestedProducts);

        // Gọi Gemini API
        $geminiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $geminiApiKey,
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $promptForGemini],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.2, // Rất thấp để có câu trả lời chính xác
                'topK' => 20,
                'topP' => 0.7,
                'maxOutputTokens' => 100,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
            ],
        ]);

        $geminiResponseData = $geminiResponse->json();

        if (isset($geminiResponseData['error'])) {
            Log::error('Gemini API Error: ' . json_encode($geminiResponseData));
            return response()->json(['error' => 'Lỗi từ dịch vụ AI: ' . ($geminiResponseData['error']['message'] ?? 'Unknown error')], 500);
        }

        $aiTextResponse = $geminiResponseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể xử lý yêu cầu này lúc này.';
        
        // Log để debug
        Log::info('User Message: ' . $userMessage);
        Log::info('Detected Intent: ' . $intent);
        Log::info('Found Products: ' . count($suggestedProducts));
        Log::info('AI Response: ' . $aiTextResponse);

        return response()->json([
            'ai_response' => $aiTextResponse,
            'suggested_products' => $suggestedProducts,
        ]);
    }

    /**
     * Phân tích ý định người dùng thông minh hơn
     */
    private function analyzeUserIntentAdvanced($message)
    {
        $lowerMessage = strtolower(trim($message));
        
        // Loại bỏ dấu câu hỏi và chuẩn hóa
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $lowerMessage);
        $cleanMessage = preg_replace('/\s+/', ' ', $cleanMessage);
        
        // Kiểm tra greeting trước
        if ($this->matchesPattern($cleanMessage, self::INTENT_PATTERNS['greeting'])) {
            return 'greeting';
        }
        
        // Kiểm tra product search với trọng số
        $productScore = 0;
        
        // Kiểm tra từ khóa chính
        foreach (self::INTENT_PATTERNS['product_search']['primary'] as $keyword) {
            if (str_contains($cleanMessage, $keyword)) {
                $productScore += 3;
            }
        }
        
        // Kiểm tra từ khóa phụ
        foreach (self::INTENT_PATTERNS['product_search']['secondary'] as $keyword) {
            if (str_contains($cleanMessage, $keyword)) {
                $productScore += 2;
            }
        }
        
        // Kiểm tra tên sản phẩm trực tiếp
        if ($this->containsProductNames($cleanMessage)) {
            $productScore += 5;
        }
        
        // Kiểm tra từ khóa danh mục sản phẩm
        if ($this->containsProductKeywords($cleanMessage)) {
            $productScore += 4;
        }
        
        Log::info("Product search score: $productScore for message: $cleanMessage");
        
        if ($productScore >= 3) {
            return 'product_search';
        }
        
        // Kiểm tra policy
        if ($this->matchesPattern($cleanMessage, self::INTENT_PATTERNS['policy'])) {
            return 'policy';
        }
        
        // Kiểm tra general chat
        if ($this->matchesPattern($cleanMessage, self::INTENT_PATTERNS['general_chat'])) {
            return 'general_chat';
        }
        
        // Mặc định là product_search nếu không rõ ràng
        return strlen($cleanMessage) > 10 ? 'product_search' : 'general_chat';
    }

    /**
     * Kiểm tra pattern matching
     */
    private function matchesPattern($message, $patterns)
    {
        foreach ($patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kiểm tra có chứa từ khóa sản phẩm không
     */
    private function containsProductKeywords($message)
    {
        $lowerMessage = strtolower($message);
        foreach (self::PRODUCT_CATEGORIES as $category => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Kiểm tra có chứa tên sản phẩm thực tế không
     */
    private function containsProductNames($message)
    {
        $productNames = Product::where('status', 1)
            ->where('quantity', '>', 0)
            ->get(['name'])
            ->pluck('name')
            ->map(function ($name) {
                return strtolower($name);
            })
            ->toArray();

        foreach ($productNames as $productName) {
            // Kiểm tra cả tên đầy đủ và các từ khóa trong tên
            $nameWords = explode(' ', $productName);
            foreach ($nameWords as $word) {
                if (strlen($word) > 3 && str_contains($message, $word)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Lấy tất cả sản phẩm có sẵn
     */
    private function getAllAvailableProducts()
    {
        return Product::select('id', 'name', 'slug', 'sort_des', 'description', 'quantity', 'status', 'image', 'view')
            ->where('status', 1)
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'view' => $product->view ?? 0,
                    'description' => $product->sort_des ?? $product->description ?? '',
                    'keywords' => strtolower($product->name . ' ' . $product->description)
                ];
            })
            ->toArray();
    }

    /**
     * Tìm sản phẩm liên quan thông minh - Fixed version
     */
    private function findRelevantProductsAdvanced($message, $availableProducts)
    {
        $lowerMessage = strtolower($message);
        $matchedProducts = [];
        
        // Bước 1: Tìm kiếm trực tiếp theo từ khóa
        $directMatches = $this->findDirectMatches($lowerMessage, $availableProducts);
        if (!empty($directMatches)) {
            Log::info("Found direct matches: " . json_encode(array_column($directMatches, 'name')));
            return $directMatches;
        }
        
        // Bước 2: Tìm kiếm theo danh mục
        $categoryMatches = $this->findCategoryMatches($lowerMessage, $availableProducts);
        if (!empty($categoryMatches)) {
            Log::info("Found category matches: " . json_encode(array_column($categoryMatches, 'name')));
            return $categoryMatches;
        }
        
        // Bước 3: Tìm kiếm mờ
        $fuzzyMatches = $this->findFuzzyMatches($lowerMessage, $availableProducts);
        Log::info("Found fuzzy matches: " . json_encode(array_column($fuzzyMatches, 'name')));
        return $fuzzyMatches;
    }
    
    /**
     * Tìm kiếm trực tiếp theo tên sản phẩm
     */
    private function findDirectMatches($message, $availableProducts)
    {
        $results = [];
        $messageWords = array_filter(explode(' ', $message), function($word) {
            return strlen(trim($word)) > 2;
        });
        
        foreach ($availableProducts as $product) {
            $productName = strtolower($product['name']);
            $score = 0;
            
            foreach ($messageWords as $word) {
                $cleanWord = trim($word);
                if (str_contains($productName, $cleanWord)) {
                    $score += strlen($cleanWord) * 2; // Từ dài hơn = điểm cao hơn
                }
            }
            
            if ($score >= 4) { // Threshold cho direct match
                $results[] = [
                    'product' => $product,
                    'score' => $score
                ];
            }
        }
        
        return $this->sortAndFormatResults($results, 3);
    }
    
    /**
     * Tìm kiếm theo danh mục sản phẩm
     */
    private function findCategoryMatches($message, $availableProducts)
    {
        $results = [];
        
        foreach (self::PRODUCT_CATEGORIES as $category => $data) {
            $categoryScore = 0;
            
            // Kiểm tra message có chứa từ khóa danh mục không
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($message, $keyword)) {
                    $categoryScore += strlen($keyword);
                }
            }
            
            if ($categoryScore > 0) {
                // Tìm sản phẩm thuộc danh mục này
                foreach ($availableProducts as $product) {
                    $productKeywords = strtolower($product['name'] . ' ' . $product['description']);
                    $productScore = 0;
                    
                    foreach ($data['keywords'] as $keyword) {
                        if (str_contains($productKeywords, $keyword)) {
                            $productScore += $categoryScore + strlen($keyword);
                        }
                    }
                    
                    if ($productScore > 0) {
                        $results[] = [
                            'product' => $product,
                            'score' => $productScore
                        ];
                    }
                }
                break; // Chỉ lấy danh mục đầu tiên match
            }
        }
        
        return $this->sortAndFormatResults($results, 3);
    }
    
    /**
     * Tìm kiếm mờ
     */
    private function findFuzzyMatches($message, $availableProducts)
    {
        $results = [];
        $messageWords = array_filter(explode(' ', $message), function($word) {
            $commonWords = ['tôi', 'bạn', 'có', 'không', 'muốn', 'xem', 'mua', 'cần'];
            return strlen(trim($word)) > 2 && !in_array(trim($word), $commonWords);
        });
        
        foreach ($availableProducts as $product) {
            $productKeywords = strtolower($product['name'] . ' ' . $product['description']);
            $score = 0;
            
            foreach ($messageWords as $word) {
                $cleanWord = trim($word);
                if (str_contains($productKeywords, $cleanWord)) {
                    $score += 3;
                }
            }
            
            if ($score > 0) {
                $results[] = [
                    'product' => $product,
                    'score' => $score
                ];
            }
        }
        
        return $this->sortAndFormatResults($results, 2);
    }
    
    /**
     * Sắp xếp và format kết quả
     */
    private function sortAndFormatResults($results, $limit)
    {
        if (empty($results)) {
            return [];
        }
        
        // Sắp xếp theo điểm giảm dần
        usort($results, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        $formatted = [];
        foreach (array_slice($results, 0, $limit) as $match) {
            $product = $match['product'];
            $formatted[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'image' => $product['image'],
                'view' => $product['view'],
                'description' => $product['description']
            ];
            
            Log::info("Selected product: <b>{$product['name']}</b>  with score: {$match['score']}");
        }
        
        return $formatted;
    }

    /**
     * Xây dựng context chi tiết cho AI
     */
    private function buildAdvancedContext($availableProducts, $suggestedProducts, $intent)
    {
        $context = "=== THÔNG TIN HỆ THỐNG GREEN HOME ===\n";
        $context .= "Cửa hàng: Green Home - Chuyên sản phẩm xanh, thân thiện môi trường\n";
        $context .= "Tổng số sản phẩm có sẵn: " . count($availableProducts) . "\n\n";
        
        if (!empty($availableProducts)) {
            $context .= "=== DANH SÁCH TẤT CẢ SẢN PHẨM CÓ SẴN ===\n";
            foreach ($availableProducts as $product) {
                $context .= "- <b>{$product['name']}</b> \n";
            }
            $context .= "\n";
        }
        
        if (!empty($suggestedProducts)) {
            $context .= "=== SẢN PHẨM PHÙHỢP VỚI YÊU CẦU ===\n";
            foreach ($suggestedProducts as $product) {
                $context .= "- <b>{$product['name']}</b> \n";
                if ($product['description']) {
                    $context .= "  Mô tả: {$product['description']}\n";
                }
            }
            $context .= "\n";
        } else if ($intent === 'product_search') {
            $context .= "=== KHÔNG TÌM THẤY SẢN PHẨM PHÙ HỢP ===\n";
            $context .= "Không có sản phẩm nào trong hệ thống phù hợp với yêu cầu của khách hàng.\n\n";
        }
        
        return $context;
    }

    /**
     * Tạo prompt thông minh cho AI
     */
    private function buildIntelligentPrompt($userMessage, $intent, $systemContext, $suggestedProducts)
    {
        $basePrompt = "Bạn là trợ lý AI chuyên nghiệp của Green Home - cửa hàng sản phẩm xanh.

NGUYÊN TẮC HOẠT ĐỘNG NGHIÊM NGẶT:
1. CHỈ đề cập đến sản phẩm CÓ TRONG HỆ THỐNG (có ID cụ thể)
2. TUYỆT ĐỐI KHÔNG bịa đặt hoặc gợi ý sản phẩm không tồn tại
3. Trả lời NGẮN GỌN (1-2 câu), CHÍNH XÁC
4. Luôn nhiệt tình và chuyên nghiệp

$systemContext";

        switch ($intent) {
            case 'greeting':
                $prompt = $basePrompt . "
TÌNH HUỐNG: Khách hàng chào hỏi
YÊU CẦU: Chào hỏi thân thiện, giới thiệu ngắn gọn Green Home và hỏi nhu cầu cụ thể.
TUYỆT ĐỐI KHÔNG được gợi ý sản phẩm cụ thể trong lời chào.";
                break;

            case 'product_search':
                if (!empty($suggestedProducts)) {
                    $prompt = $basePrompt . "
TÌNH HUỐNG: Khách hàng tìm sản phẩm - CÓ sản phẩm phù hợp
YÊU CẦU: 
- Trả lời về các sản phẩm CÓ TRONG DANH SÁCH PHÙHỢP ở trên
- Nhấn mạnh đặc điểm thân thiện môi trường
- Mời khách xem chi tiết sản phẩm hiển thị bên dưới
- PHẢI nói rõ tên sản phẩm và ID";
                } else {
                    $prompt = $basePrompt . "
TÌNH HUỐNG: Khách hàng tìm sản phẩm - KHÔNG tìm thấy sản phẩm phù hợp
YÊU CẦU:
- Xin lỗi không tìm thấy sản phẩm cụ thể khách hàng yêu cầu
- Gợi ý khách hàng mô tả rõ hơn nhu cầu
- Có thể giới thiệu về các danh mục sản phẩm có sẵn TỔNG QUÁT
- KHÔNG được nói tên sản phẩm cụ thể nếu không có trong hệ thống";
                }
                break;

            case 'policy':
                $prompt = $basePrompt . "
TÌNH HUỐNG: Khách hàng hỏi chính sách
YÊU CẦU: Trả lời chung về chính sách của Green Home và hướng dẫn liên hệ để biết chi tiết.";
                break;

            default:
                $prompt = $basePrompt . "
TÌNH HUỐNG: Trò chuyện chung
YÊU CẦU: Trả lời lịch sự và hướng cuộc trò chuyện về sản phẩm xanh của Green Home.";
                break;
        }

        $prompt .= "\n\nTin nhắn khách hàng: \"$userMessage\"\n\nHãy trả lời theo đúng nguyên tắc:";

        Log::info("Generated prompt for intent '$intent': " . substr($prompt, 0, 200) . "...");
        return $prompt;
    }
}