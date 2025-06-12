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
use Database\Factories\ProductFactory;
use Faker\Factory as Faker;



class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // $faker = Faker::create();
        // $categories = Category::pluck('id')->toArray();
        // $brands = Brand::pluck('id')->toArray();

        // for ($i = 0; $i < 20; $i++) {
        //     Product::create([
        //         'category_id' => $faker->randomElement($categories),
        //         'brand_id' => $faker->randomElement($brands),
        //         'name' => $faker->words(3, true),
        //         'slug' => $faker->slug,
        //         'description' => $faker->paragraph,
        //         'quantity' => $faker->numberBetween(1, 100),
        //         'date_of_entry' => $faker->date(),
        //         'status' => $faker->boolean,
        //         'image' => $faker->imageUrl(),
        //         'view' => $faker->numberBetween(0, 1000),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'deleted_at' => null,
        //     ]);
        // }

    }
}
