<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\DiscountProduct;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Review;
use App\Models\WishList;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $products = Product::inRandomOrder()->take(2)->get();

        foreach ($products as $product) {
            // mỗi product sẽ có vài variant (2-3 mỗi cái)
            ProductVariant::factory()->count(2)->create([
                'product_id' => $product->id
            ]);
        }
    }
}
