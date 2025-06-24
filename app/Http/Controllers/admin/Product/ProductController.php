<?php

namespace App\Http\Controllers\admin\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\attributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Quản lý sản phẩm';
        $categories = Category::get();
        $brands = Brand::get();

        $perPage = $request->input('per_page', 10);

        $products = Product::with([
            'category' => fn($q) => $q->withTrashed(),
            'brand' => fn($q) => $q->withTrashed(),
        ])
            ->filter($request)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->except('page'));

        $productAll = Product::whereNull('deleted_at')->get();
        $productTrashed = Product::onlyTrashed()->get();

        if ($request->ajax()) {
            return view('admin.products.table', compact('products'));
        }

        return view('admin.products.index', compact(
            'title',
            'products',
            'productAll',
            'productTrashed',
            'categories',
            'brands'
        ));
    }

    public function trashed(Request $request)
    {
        $title = 'Thùng rác';
        $categories = Category::get();
        $brands = Brand::get();

        $products = Product::onlyTrashed()
            ->with([
                'category' => fn($q) => $q->withTrashed(),
                'brand' => fn($q) => $q->withTrashed(),
            ])
            ->filter($request) // dùng lại scopeFilter
            ->orderByDesc('id')
            ->paginate(4)
            ->appends($request->except('page'));
        if ($request->ajax()) {
            return view('admin.products.table', compact('products'))->render();
        }

        return view('admin.products.trashed', compact('title', 'products', 'categories', 'brands'));
    }

    public function show($id, Request $request)
    {
        $product = Product::with(['category', 'brand'])
            ->findOrFail($id);

        // Dùng cho tất cả trường hợp
        $commentsQuery = $product->comments()
            ->with('user')
            ->when($request->name, function ($query, $name) {
                $query->whereHas('user', function ($q) use ($name) {
                    $q->where('name', 'like', "%$name%");
                });
            });

        $comments = $commentsQuery->latest()->paginate(5)->appends($request->only('name'));

        $variants = $product->productVariants()
            ->with('productVariantValues.attributeValue.attribute')
            ->whereNull('deleted_at')
            ->paginate(5);

        $variantIds = $product->productVariants()
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        $reviews = Review::with(['user', 'productVariant.product'])
            ->whereIn('product_variant_id', $variantIds)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(5);

        // Nếu là AJAX request cho một trong các bảng con
        if ($request->ajax()) {
            $tab = $request->get('tab');

            if ($tab === 'comments') {
                return view('admin.products.partials.comment-table', compact('comments'));
            }

            if ($tab === 'variants') {
                return view('admin.products.partials.variant-table', compact('variants'));
            }

            if ($tab === 'reviews') {
                return view('admin.products.partials.review-table', compact('reviews'));
            }

            // Trường hợp fallback (nếu cần)
            return response()->json(['error' => 'Invalid tab'], 400);
        }

        // Trả về giao diện chính nếu không phải AJAX
        return view('admin.products.show', compact('product', 'comments', 'variants', 'reviews'));
    }


    public function create()
    {
        $title = "Thêm sản phẩm";
        $categories = Category::get();
        $brands = Brand::get();
        $attributes = Attribute::with('attributeValues')->get();
        // dd($attributes);
        $attributeData = $attributes->map(function ($attribute) {
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'values' => $attribute->attributeValues->map(function ($value) {
                    return ['id' => $value->id, 'value' => $value->value];
                })->toArray(),
            ];
        })->toArray();
        // dd($attributeData)

        return view('admin.products.create', compact('categories', 'attributes', 'attributeData', 'brands', 'title'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'status' => 'required|in:0,1',
            'date_of_entry' => 'required|date',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_variant' => 'required|boolean',

            // Nếu có biến thể
            'variants' => 'required_if:is_variant,1|array',
            'variants.*.values' => [
                'required_if:is_variant,1',
                'string',
                function ($attribute, $value, $fail) {
                    $valueIds = explode(',', $value);
                    foreach ($valueIds as $valueId) {
                        if (!empty($valueId) && !\App\Models\AttributeValue::where('id', $valueId)->exists()) {
                            $fail("Giá trị thuộc tính $valueId trong $attribute không tồn tại.");
                        }
                    }
                }
            ],
            'variants.*.price' => 'required_if:is_variant,1|numeric',
            'variants.*.quantity' => 'required_if:is_variant,1|integer',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Nếu là sản phẩm đơn
            'simple_price' => 'required_unless:is_variant,1|numeric',
            'simple_quantity' => 'required_unless:is_variant,1|integer',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.required' => 'Vui lòng chọn thương hiệu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'date_of_entry.required' => 'Vui lòng nhập ngày nhập kho.',
            'image.image' => 'Ảnh sản phẩm phải là tệp hình ảnh.',
            'image.mimes' => 'Ảnh sản phẩm phải có định dạng jpeg, png, jpg, webp.',
            'image.max' => 'Ảnh sản phẩm không được vượt quá 2MB.',
            'is_variant.required' => 'Vui lòng chọn loại sản phẩm.',
            'variants.required_if' => 'Vui lòng nhập ít nhất một biến thể.',
            'variants.*.values.required_if' => 'Vui lòng chọn thuộc tính cho biến thể.',
            'variants.*.price.required_if' => 'Vui lòng nhập giá cho biến thể.',
            'variants.*.quantity.required_if' => 'Vui lòng nhập số lượng cho biến thể.',
            'variants.*.image.image' => 'Ảnh biến thể phải là tệp hình ảnh.',
            'variants.*.image.mimes' => 'Ảnh biến thể phải có định dạng jpeg, png, jpg.',
            'variants.*.image.max' => 'Ảnh biến thể không được vượt quá 2MB.',
            'simple_price.required_unless' => 'Vui lòng nhập giá cho sản phẩm.',
            'simple_quantity.required_unless' => 'Vui lòng nhập số lượng cho sản phẩm.',
        ]);

        $errors = [];

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()
                ->back()
                ->withErrors($validator)
                ->with('all_errors', $errors)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, &$errors) {
                // Upload ảnh sản phẩm
                $productImagePath = null;
                if ($request->hasFile('image')) {
                    $productImagePath = $request->file('image')->store('images/products', 'public');
                }

                // Tạo sản phẩm
                $product = new Product();
                $product->name = $request->input('name');
                $product->slug = Str::slug($request->input('name'));
                $product->category_id = $request->input('category_id');
                $product->brand_id = $request->input('brand_id');
                $product->status = $request->input('status');
                $product->date_of_entry = $request->input('date_of_entry');
                $product->description = $request->input('description');
                $product->image = $productImagePath;
                $product->quantity = 0;
                $product->save();

                if ($request->boolean('is_variant') && empty($request->input('variants', []))) {
                    $errors[] = "Phải có ít nhất một biến thể cho sản phẩm có biến thể.";
                    throw new \Exception("Phải có ít nhất một biến thể cho sản phẩm có biến thể.");
                }

                // Nếu là sản phẩm có biến thể
                if ($request->boolean('is_variant')) {
                    $variants = $request->input('variants', []);
                    $seenCombinations = [];

                    foreach ($variants as $index => $variant) {
                        $values = explode(',', $variant['values']);
                        $attributeCombination = $values ?? [];
                        ksort($attributeCombination);

                        // Lấy tên thuộc tính (ví dụ: "M-Red")
                        $attributeNames = [];
                        foreach ($attributeCombination as $valueId) {
                            $attrValue = \App\Models\AttributeValue::find($valueId);
                            if ($attrValue) {
                                $attributeNames[] = $attrValue->value;
                            }
                        }
                        $attributeNameString = implode('-', $attributeNames);

                        $combinationKey = implode('-', array_map(function ($attrId, $valueId) {
                            return $attrId . ':' . $valueId;
                        }, array_keys($attributeCombination), $attributeCombination));

                        if (in_array($combinationKey, $seenCombinations)) {
                            $errors[] = "Biến thể thứ " . ($index + 1) . " bị trùng tổ hợp thuộc tính.";
                            throw new \Exception("Biến thể thứ " . ($index + 1) . " bị trùng tổ hợp thuộc tính.");
                        }
                        $seenCombinations[] = $combinationKey;

                        $newVariant = new ProductVariant();
                        $newVariant->product_id = $product->id;
                        $newVariant->sku = ProductVariant::generateUniqueSku($product->name);
                        $newVariant->price = $variant['price'];
                        $newVariant->quantity = $variant['quantity'];
                        $newVariant->status = true;
                        $newVariant->attribute_name = $attributeNameString;

                        if ($request->hasFile("variants.$index.image")) {
                            $variantImage = $request->file("variants.$index.image");
                            $newVariant->image = $variantImage->store('images/products/variants', 'public');
                        }

                        $newVariant->save();

                        // Gán thuộc tính cho biến thể
                        if (!empty($variant['values'])) {
                            $attributeValues = explode(',', $variant['values']);
                            $variantValues = [];
                            foreach ($attributeValues as $valueId) {
                                if (!empty($valueId)) {
                                    $variantValues[] = [
                                        'product_variant_id' => $newVariant->id,
                                        'attribute_value_id' => $valueId,
                                    ];
                                }
                            }
                            if (!empty($variantValues)) {
                                ProductVariantValue::insert($variantValues);
                            }
                        }
                    }

                    $product->save();
                } else {
                    // Sản phẩm đơn
                    $newVariant = new ProductVariant();
                    if (!$request->boolean('is_variant') && $request->hasFile('image')) {
                        $newVariant->image = $request->file('image')->store('images/products/variants', 'public');
                    }
                    $newVariant->product_id = $product->id;
                    $newVariant->sku = ProductVariant::generateUniqueSku($product->name);
                    $newVariant->price = $request->input('simple_price');
                    $newVariant->quantity = $request->input('simple_quantity');
                    $newVariant->status = true;
                    $newVariant->attribute_name = null;
                    $newVariant->save();
                }
            });

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được thêm thành công.');
        } catch (\Exception $e) {
            // Gộp lỗi validate và lỗi ngoại lệ
            $allErrors = $errors;
            $allErrors[] = $e->getMessage();

            return redirect()
                ->back()
                ->withErrors($allErrors)
                ->with('all_errors', $allErrors)
                ->withInput();
        }
    }



    public function edit($id)
    {
        $title = "Cập nhật sản phẩm";
        $brands = Brand::get();
        $categories = Category::get();

        // dd($id);
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('categories', 'brands', 'title', 'product'));
    }
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $dataValidate = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'date_of_entry' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        // Xử lý hình ảnh nếu có upload mới
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
            $dataValidate['image'] = $imagePath;

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        }

        $product->slug = Str::slug($request->input('name'));
        $product->update($dataValidate);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', value: 'Sản phẩm đã được chuyển vào thùng rác!');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if ($product) {
            $product->restore();
            ProductVariant::where('product_id', $id)->restore(); // Khôi phục sản phẩm
        }

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được khôi phục thành công');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if ($product) {
            // Xóa vĩnh viễn các biến thể liên quan
            ProductVariant::where('product_id', $id)->forceDelete();
            // Xóa vĩnh viễn sản phẩm
            $product->forceDelete();
        }

        return redirect()->route('admin.products.trashed')->with('success', 'Sản phẩm đã được xóa vĩnh viễn');
    }
}
