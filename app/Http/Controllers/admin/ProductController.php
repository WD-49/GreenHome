<?php
namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

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

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        } elseif ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // if ($request->filled('ngay_nhap')) {
        //     $query->whereDate('date_of_entry', $request->ngay_nhap);
        // }

        $products = $query->orderByDesc('id')->paginate(4)->appends($request->except('page'));


        // dd($products);

        return view('admin.products.index', compact('title', 'products', 'categories', 'brands'));
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

    // public function show($id)
    // {
    //     // dd($id);
    //     // lay ra du lieu chi tiet theo id

    //     $product = Product::with('category')->findOrFail($id);
    //     // dd($product);
    //     // do du lieu thong tin chi tiet ra giao dien
    //     return view('admin.products.show', compact('product'));

    // }

    public function create()
    {
        $title = "Thêm sản phẩm";
        $categories = Category::get();
        $brands = Brand::get();
        // dd($categories);

        return view('admin.products.create', compact('categories', 'brands', 'title'));
    }

    public function store(Request $request)
    {
        $dataValidate = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0|max:99999999',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:1',
            'date_of_entry' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
            $dataValidate['image'] = $imagePath;
        }

        Product::create($dataValidate);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
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