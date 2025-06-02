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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Quản lý sản phẩm';
        $categories = Category::get();
        $brands = Brand::get();

        $query = Product::with(['category', 'brand'])
            ->whereHas('category', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->whereHas('brand', function ($q) {
                $q->whereNull('deleted_at');
            });


        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == 1 ? 1 : 0);
        }

        if ($request->filled('min_date') && $request->filled('max_date')) {
            $query->whereBetween('date_of_entry', [$request->min_date, $request->max_date]);
        } elseif ($request->filled('min_date')) {
            $query->where('date_of_entry', '>=', $request->min_date);
        } elseif ($request->filled('max_date')) {
            $query->where('date_of_entry', '<=', $request->max_date);
        }


        // if ($request->filled('ngay_nhap')) {
        //     $query->whereDate('date_of_entry', $request->ngay_nhap);
        // }

        $products = $query->orderByDesc('id')->paginate(4)->appends($request->except('page'));
        $productAll = Product::whereNull('deleted_at')->get();
        $productTrashed = Product::onlyTrashed()->get();


        // dd($productAll);

        return view('admin.products.index', compact('title', 'products', 'productAll', 'productTrashed', 'categories', 'brands'));
    }

    public function trashed(Request $request)
    {
        $title = 'Thùng rác';
        $categories = Category::get();
        $brands = Brand::get();

        $query = Product::onlyTrashed()->with([
            'category' => function ($query) {
                $query->withTrashed();
            },
            'brand' => function ($query) {
                $query->withTrashed();
            },
        ]);



        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == 1 ? 1 : 0);
        }

        if ($request->filled('min_date') && $request->filled('max_date')) {
            $query->whereBetween('date_of_entry', [$request->min_date, $request->max_date]);
        } elseif ($request->filled('min_date')) {
            $query->where('date_of_entry', '>=', $request->min_date);
        } elseif ($request->filled('max_date')) {
            $query->where('date_of_entry', '<=', $request->max_date);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        } elseif ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->orderByDesc('id')->paginate(4)->appends($request->except('page'));
        // dd($products);

        return view('admin.products.trashed', compact('title', 'products', 'categories', 'brands'));
    }

    public function show($id, Request $request)
    {
        $product = Product::with(['category', 'brand'])
            ->findOrFail($id);

        $comments = $product->comments()
            ->with('user')
            ->when($request->name, function ($query, $name) {
                $query->whereHas('user', function ($q) use ($name) {
                    $q->where('name', 'like', "%$name%");
                });
            })
            ->latest()
            ->paginate(5)
            ->appends($request->only('name')); // giữ lại query string khi phân trang

        $variants = $product->productVariants()
            ->with('productVariantValues.attributeValue.attribute')
            ->where('deleted_at', null)
            ->paginate(5);
        // dd($variants);
        // do du lieu thong tin chi tiet ra giao dien
        return view('admin.products.show', compact('product', 'comments', 'variants'));

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
        // dd($request);
        // dd($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'status' => 'required|in:0,1',
            'date_of_entry' => 'required|date',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_variant' => 'required|boolean',

            // Nếu có biến thể
            'variants' => 'required_if:is_variant,1|array',
            'variants.*.values' => [
                'required_if:is_variant,1',
                'string',
                function ($attribute, $value, $fail) {
                    $valueIds = explode(',', $value);
                    foreach ($valueIds as $valueId) {
                        if (!empty($valueId) && !AttributeValue::where('id', $valueId)->exists()) {
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
            // 'simple_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // if (!$request->boolean('is_variant')) {
        //     $rules['simple_price'] = 'required|numeric';
        //     $rules['simple_quantity'] = 'required|integer';
        //     $rules['simple_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        // }

        // $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
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
                    throw new \Exception("Phải có ít nhất một biến thể cho sản phẩm có biến thể.");
                }

                // Nếu là sản phẩm có biến thể
                if ($request->boolean('is_variant')) {
                    $variants = $request->input('variants', []);
                    // dd($variants);
                    $seenCombinations = [];

                    foreach ($variants as $index => $variant) {
                        // dd($variant['values']);
                        $values = explode(',', $variant['values']);
                        // dd($values);
                        $attributeCombination = $values ?? [];
                        ksort($attributeCombination);
                        // dd(ksort($attributeCombination));

                        $combinationKey = implode('-', array_map(function ($attrId, $valueId) {
                            return $attrId . ':' . $valueId;
                        }, array_keys($attributeCombination), $attributeCombination));

                        if (in_array($combinationKey, $seenCombinations)) {
                            throw new \Exception("Biến thể thứ " . ($index + 1) . " bị trùng tổ hợp thuộc tính.");
                        }
                        $seenCombinations[] = $combinationKey;

                        $newVariant = new ProductVariant();
                        $newVariant->product_id = $product->id;
                        $newVariant->sku = ProductVariant::generateUniqueSku($product->name);
                        $newVariant->price = $variant['price'];
                        $newVariant->quantity = $variant['quantity'];
                        $newVariant->status = true;

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
                    $newVariant->save();
                }
            });

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được thêm thành công.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'price' => 'required|numeric|min:0|max:99999999',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:1',
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

}