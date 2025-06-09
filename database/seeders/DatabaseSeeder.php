<?php

namespace Database\Seeders;

use App\Models\Cart;

use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Review;
use App\Models\Comment;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Discount;
use App\Models\WishList;
use App\Models\Attribute;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\UserProfile;
use App\Models\DiscountUsage;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\DiscountProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('24092005'),
            'role' => 'admin',
            'status' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
