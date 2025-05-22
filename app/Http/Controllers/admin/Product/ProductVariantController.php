<?php
namespace App\Http\Controllers\admin\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    public function index(Request $request, Product $product)
    {
        $title = 'Quản lý biến thể';

        $variants = $product->productVariants()
            ->with(['product', 'productVariantValues.attributeValue.attribute']) // Load cả value & attribute
            ->orderByDesc('id')
            ->paginate(4)
            ->appends($request->except('page'));
        // dd($variants);

        return view('admin.products.variants.index', compact('title', 'variants', 'product'));
    }


    // public function trashed(Request $request)
    // {
    //     $title = 'Thùng rác';
    //     $categories = Category::get();
    //     $brands = Brand::get();

    //     $query = Product::onlyTrashed()->with([
    //         'category' => function ($query) {
    //             $query->withTrashed();
    //         },
    //         'brand' => function ($query) {
    //             $query->withTrashed();
    //         },
    //     ]);



    //     if ($request->filled('name')) {
    //         $query->where('name', 'LIKE', '%' . $request->name . '%');
    //     }

    //     if ($request->filled('category_id')) {
    //         $query->where('category_id', $request->category_id);
    //     }

    //     if ($request->filled('brand_id')) {
    //         $query->where('brand_id', $request->brand_id);
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status == 1 ? 1 : 0);
    //     }

    //     if ($request->filled('min_date') && $request->filled('max_date')) {
    //         $query->whereBetween('date_of_entry', [$request->min_date, $request->max_date]);
    //     } elseif ($request->filled('min_date')) {
    //         $query->where('date_of_entry', '>=', $request->min_date);
    //     } elseif ($request->filled('max_date')) {
    //         $query->where('date_of_entry', '<=', $request->max_date);
    //     }

    //     if ($request->filled('min_price') && $request->filled('max_price')) {
    //         $query->whereBetween('price', [$request->min_price, $request->max_price]);
    //     } elseif ($request->filled('min_price')) {
    //         $query->where('price', '>=', $request->min_price);
    //     } elseif ($request->filled('max_price')) {
    //         $query->where('price', '<=', $request->max_price);
    //     }

    //     $products = $query->orderByDesc('id')->paginate(4)->appends($request->except('page'));
    //     // dd($products);

    //     return view('admin.products.trashed', compact('title', 'products', 'categories', 'brands'));
    // }

    // // public function show($id)
    // // {
    // //     // dd($id);
    // //     // lay ra du lieu chi tiet theo id

    // //     $product = Product::with('category')->findOrFail($id);
    // //     // dd($product);
    // //     // do du lieu thong tin chi tiet ra giao dien
    // //     return view('admin.products.show', compact('product'));

    // // }

    public function create(Product $product)
    {
        $title = "Thêm biến thể";
        // $categories = Category::get();
        // $brands = Brand::get();
        // dd($categories);
        $attributes = Attribute::with('attributeValues')->get();
        // dd($attributes);

        return view('admin.products.variants.create', compact('title', 'attributes', 'product'));
    }

    public function store(Request $request, Product $product)
    {

        // dd($request->image);
        $request->validate([
            // 'sku' => 'required|string|max:255|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'status' => 'required|boolean',
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:attributes,id',
            'attribute_values' => 'nullable|array',
            'attribute_values.*' => 'nullable|integer|max:255',
        ]);
        // dd('check');

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
        }
        // dd($request->image);

        $submitted = collect($request->attribute_values)->sort()->implode(',');

        // dd($submitted);
        $sku = ProductVariant::generateUniqueSku($product->name);
        // dd($sku);

        $exists = $product->productVariants()
            ->with('productVariantValues')
            ->get()
            ->contains(function ($variant) use ($submitted) {
                return $variant->productVariantValues->pluck('attribute_value_id')->sort()->implode(',') === $submitted;
            });

        if ($exists) {
            return back()->withErrors(['attributes' => 'Biến thể với tổ hợp thuộc tính này đã tồn tại.'])->withInput();
        }


        // Tạo biến thể mới
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $sku,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imagePath,
            'status' => $request->status,
        ]);
        if ($request->attribute_values) {
            foreach ($request->attribute_values as $index => $attributeId) {
                // dd($attribute_values);
                // dd('check');
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $request->attribute_values[$index],
                ]);
            }
        }

        // Lưu các giá trị thuộc tính được chọn



        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Thêm biến thể của sản phẩm thành công!');
    }

    public function edit(Product $product, ProductVariant $productVariant)
    {
        $title = "Cập nhật sản phẩm";
        $attributes = Attribute::with('attributeValues')->get();
        $productVariant->load('productVariantValues.attributeValue.attribute');

        // dd($productVariant->productVariantValues);


        return view('admin.products.variants.edit', compact('title', 'productVariant', 'product', 'attributes'));
    }
    public function update(Request $request, Product $product, ProductVariant $productVariant)
    {
        // dd($request);
        // dd($productVariant);
        $request->validate([
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'status' => 'required|boolean',
            'attributes' => 'required|array',
            'attributes.*' => 'exists:attributes,id',
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'required|integer|max:255',
        ]);

        // Xử lý hình ảnh nếu có upload mới
        $imagePath = $productVariant->image;
        if ($request->hasFile('image')) {
            if ($productVariant->image && Storage::disk('public')->exists($productVariant->image)) {
                Storage::disk('public')->delete($productVariant->image);
            }
            $imagePath = $request->file('image')->store('images/products', 'public');
        }

        // Tổ hợp mới từ request
        $submitted = collect($request->attribute_values)->sort()->values()->implode(',');
        // dd($submitted);


        // Tổ hợp hiện tại trong DB
        $current = $productVariant->productVariantValues->pluck('attribute_value_id')->sort()->values()->implode(',');
        // dd($current);

        // Nếu tổ hợp có thay đổi thì mới kiểm tra trùng
        if ($submitted !== $current) {
            $exists = $product->productVariants()
                ->where('id', '!=', $productVariant->id)
                ->with('productVariantValues')
                ->get()
                ->contains(function ($otherVariant) use ($submitted) {
                    return $otherVariant->productVariantValues->pluck('attribute_value_id')->sort()->values()->implode(',') === $submitted;
                });

            if ($exists) {
                return back()->withErrors(['attributes' => 'Biến thể với tổ hợp thuộc tính này đã tồn tại.'])->withInput();
            }

            // Nếu khác => cập nhật lại tổ hợp
            // Xoá những cái không còn trong tổ hợp mới
            $productVariant->productVariantValues()->forceDelete();

            foreach ($request->attribute_values as $attributeValueId) {
                ProductVariantValue::withTrashed()->updateOrCreate(
                    [
                        'product_variant_id' => $productVariant->id,
                        'attribute_value_id' => $attributeValueId,
                    ],
                );
            }


        }

        // Cập nhật các trường cơ bản
        $productVariant->update([
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Cập nhật biến thể thành công!');
    }




    // public function destroy($id)
    // {
    //     $product = Product::findOrFail($id);

    //     $product->delete();
    //     return redirect()->route('admin.products.index')->with('success', value: 'Sản phẩm đã được chuyển vào thùng rác!');

    // }

    // public function restore($id)
    // {
    //     $product = Product::withTrashed()->findOrFail($id);
    //     if ($product) {
    //         $product->restore();
    //         ProductVariant::where('product_id', $id)->restore(); // Khôi phục sản phẩm
    //     }

    //     return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được khôi phục thành công');
    // }

}