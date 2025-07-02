<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function showEligibleProducts($code, Request $request)
    {
        $voucher = Discount::where('code', $code)->firstOrFail();

       $products = Product::whereHas('productVariants', function ($query) use ($voucher) {
    $query->where('price', '>=', $voucher->min_order_value);
});

        if ($request->has('category')) {
            $products->where('category_id', $request->category);
        }

        if ($request->has('keyword')) {
            $products->where('name', 'like', '%' . $request->keyword . '%');
        }

        return view('client.pages.voucher', [
            'voucher' => $voucher,
            'products' => $products->paginate(12),
            'categories' => Category::all(),
        ]);
    }
}
