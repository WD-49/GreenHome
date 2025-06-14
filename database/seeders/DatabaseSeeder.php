<?php

namespace Database\Seeders;

use Carbon\Carbon;

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
use Faker\Factory as Faker;
use App\Models\DiscountUsage;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\DiscountProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Hash;
use Database\Factories\ProductFactory;



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
        $userIds = \App\Models\User::pluck('id')->toArray();

        $data = [];

        // 10 reviews với product_variant_id liên quan đến product_id = 4 (giả sử bạn đã có các variant_id)
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'product_variant_id' => 4, // hoặc thay bằng id variant thực tế của product_id = 4
                'rating' => rand(3, 5),
                'title' => 'Review sản phẩm 4 #' . $i,
                'content' => 'Nội dung đánh giá sản phẩm 4 số ' . $i,
                'status' => 'approved',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ];
        }

        // 10 reviews cho các product_variant_id khác
        for ($i = 11; $i <= 20; $i++) {
            $data[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'product_variant_id' => rand(5, 10), // giả sử các variant_id khác từ 5-10
                'rating' => rand(1, 5),
                'title' => 'Review sản phẩm khác #' . $i,
                'content' => 'Nội dung đánh giá sản phẩm khác số ' . $i,
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('reviews')->insert($data);

    }
}
