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
use Illuminate\Support\Str;
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
     $this->call(CategorySeeder::class);
     $this->call(AttributeSeeder::class);

        // fake dữ liệu cho sản phẩm

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
        // $userIds = \App\Models\User::pluck('id')->toArray();

        // $data = [];

        // 10 reviews với product_variant_id liên quan đến product_id = 4 (giả sử bạn đã có các variant_id) 

        // for ($i = 1; $i <= 10; $i++) {
        //     $data[] = [
        //         'user_id' => $userIds[array_rand($userIds)],
        //         'product_variant_id' => 5, // hoặc thay bằng id variant thực tế của product_id = 4
        //         'rating' => rand(3, 5),
        //         'title' => 'Review sản phẩm 4 #' . $i,
        //         'content' => 'Nội dung đánh giá sản phẩm 4 số ' . $i,
        //         'status' => 'approved',
        //         'created_at' => Carbon::now()->subDays(rand(1, 30)),
        //         'updated_at' => Carbon::now(),
        //     ];
        // }


        // 10 reviews cho các product_variant_id khác
        // $userIds = \App\Models\User::pluck('id')->toArray();
        // $variantIds = \App\Models\ProductVariant::pluck('id')->toArray();

        // $data = [];
        // for ($i = 1; $i <= 20; $i++) {
        //     $variantId = $variantIds[array_rand($variantIds)];
        //     $data[] = [
        //         'user_id' => $userIds[array_rand($userIds)],
        //         'product_variant_id' => $variantId,
        //         'rating' => rand(1, 5),
        //         'title' => 'Review sản phẩm #' . $i,
        //         'content' => 'Nội dung đánh giá sản phẩm số ' . $i,
        //         'status' => $i <= 10 ? 'approved' : 'pending',
        //         'created_at' => Carbon::now()->subDays(rand(1, 30)),
        //         'updated_at' => Carbon::now(),
        //     ];
        // }
        // DB::table('reviews')->insert($data);


        // fake dữ liệu cho đơn hàng

        $startDate = Carbon::create(2025, 6, 22);
        $endDate = Carbon::create(2025, 6, 24);
        $orderCount = 10;
        $userIds = \App\Models\User::pluck('id')->toArray();

        $lastOrder = DB::table('orders')->orderByDesc('id')->first();
        $orderIndex = $lastOrder ? intval(preg_replace('/\D/', '', $lastOrder->sku)) : 1000;
        for ($i = 0; $i < $orderCount; $i++) {
            // Random ngày từ 16/6/2025 đến 18/6/2025
            $orderIndex++;
            $sku = 'ORDER' . $orderIndex;
            $date = Carbon::create(2025, 6, 16)->addDays(rand(0, 2));
            $userId = $userIds[array_rand($userIds)];

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'user_name' => 'User ' . $userId,
                'sku' => $sku,
                'shipping_name' => fake()->name,
                'shipping_phone' => '09' . rand(10000000, 99999999),
                'shipping_address' => fake()->city,
                'order_status' => 'Xác nhận',
                'payment_status' => 'paid',
                'payment_method_name' => 'Chuyển khoản',
                'shipping_fee' => rand(0, 2) * 10000, // 0, 10000, 20000
                'total_amount' => 0, // cập nhật sau
                'discount_value' => 0,
                'discount_amount' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $itemCount = rand(1, 3);
            $total = 0;
            for ($j = 0; $j < $itemCount; $j++) {
                $variantId = rand(1, 5);
                $qty = rand(1, 3);
                $unitPrice = rand(5, 20) * 10000; // 50,000 đến 200,000, bội số 10,000
                $totalPrice = $qty * $unitPrice;
                $total += $totalPrice;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_variant_id' => $variantId,
                    'product_name' => 'Sản phẩm ' . Str::random(1),
                    'product_variant_sku' => 'SKU-' . Str::random(1),
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            // Cập nhật lại tổng tiền cho order (số tròn chẵn)
            DB::table('orders')->where('id', $orderId)->update([
                'total_amount' => $total,
            ]);
        }

        // fake dữ liệu cho 1000 đơn hàng từ hôm nay đổ lại 12 tháng trước
        // $faker = \Faker\Factory::create();
        // $userIds = \App\Models\User::pluck('id')->toArray();
        // $variantIds = \App\Models\ProductVariant::pluck('id')->toArray();

        // $currentYear = Carbon::now()->year;
        // $currentMonth = Carbon::now()->month;

        // // Lấy số thứ tự lớn nhất hiện tại (nếu có)
        // $lastOrder = DB::table('orders')->orderByDesc('id')->first();
        // $orderIndex = $lastOrder ? intval(preg_replace('/\D/', '', $lastOrder->sku)) : 10000;

        // // ---- Fake dữ liệu cho 12 tháng năm trước, mỗi tháng 10 đơn ----
        // $lastYear = $currentYear - 1;
        // for ($month = 1; $month <= 12; $month++) {
        //     $ordersThisMonth = 10;
        //     $startOfMonth = Carbon::create($lastYear, $month, 1);
        //     $endOfMonth = $startOfMonth->copy()->endOfMonth();

        //     for ($i = 0; $i < $ordersThisMonth; $i++) {
        //         $orderIndex++;
        //         $date = $faker->dateTimeBetween($startOfMonth, $endOfMonth);
        //         $userId = $faker->randomElement($userIds);
        //         $orderId = DB::table('orders')->insertGetId([
        //             'user_id' => $userId,
        //             'user_name' => 'User ' . $userId,
        //             'sku' => 'ORDER' . $orderIndex,
        //             'shipping_name' => $faker->name,
        //             'shipping_phone' => '09' . $faker->numberBetween(10000000, 99999999),
        //             'shipping_address' => $faker->city,
        //             'order_status' => $faker->randomElement(['Xác nhận', 'Đang vận chuyển']),
        //             'payment_status' => $faker->randomElement(['paid', 'pending', 'failed']),
        //             'payment_method_name' => 'Momo',
        //             'shipping_fee' => 0,
        //             'total_amount' => 0,
        //             'discount_value' => 0,
        //             'discount_amount' => 0,
        //             'created_at' => $date,
        //             'updated_at' => $date,
        //         ]);

        //         $itemCount = rand(1, 3);
        //         $total = 0;
        //         for ($j = 0; $j < $itemCount; $j++) {
        //             $variantId = $faker->randomElement($variantIds);
        //             $qty = rand(1, 3);
        //             $unitPrice = rand(5, 20) * 10000;
        //             $totalPrice = $qty * $unitPrice;
        //             $total += $totalPrice;

        //             DB::table('order_items')->insert([
        //                 'order_id' => $orderId,
        //                 'product_variant_id' => $variantId,
        //                 'product_name' => 'Sản phẩm ' . Str::random(5),
        //                 'product_variant_sku' => 'SKU-' . Str::random(5),
        //                 'quantity' => $qty,
        //                 'unit_price' => $unitPrice,
        //                 'total_price' => $totalPrice,
        //                 'created_at' => $date,
        //                 'updated_at' => $date,
        //             ]);
        //         }

        //         DB::table('orders')->where('id', $orderId)->update([
        //             'total_amount' => $total,
        //         ]);
        //     }
        // }

        // // ---- Fake dữ liệu cho các tháng năm nay, mỗi tháng 50-100 đơn ----
        // for ($month = 1; $month <= $currentMonth; $month++) {
        //     $ordersThisMonth = rand(50, 100);
        //     $startOfMonth = Carbon::create($currentYear, $month, 1);
        //     $endOfMonth = $startOfMonth->copy()->endOfMonth();

        //     for ($i = 0; $i < $ordersThisMonth; $i++) {
        //         $orderIndex++;
        //         $date = $faker->dateTimeBetween($startOfMonth, $endOfMonth);
        //         $userId = $faker->randomElement($userIds);
        //         $orderId = DB::table('orders')->insertGetId([
        //             'user_id' => $userId,
        //             'user_name' => 'User ' . $userId,
        //             'sku' => 'ORDER' . $orderIndex,
        //             'shipping_name' => $faker->name,
        //             'shipping_phone' => '09' . $faker->numberBetween(10000000, 99999999),
        //             'shipping_address' => $faker->city,
        //             'order_status' => $faker->randomElement(['Xác nhận', 'Đang vận chuyển']),
        //             'payment_status' => $faker->randomElement(['paid', 'pending', 'failed']),
        //             'payment_method_name' => 'Momo',
        //             'shipping_fee' => 0,
        //             'total_amount' => 0,
        //             'discount_value' => 0,
        //             'discount_amount' => 0,
        //             'created_at' => $date,
        //             'updated_at' => $date,
        //         ]);

        //         $itemCount = rand(1, 3);
        //         $total = 0;
        //         for ($j = 0; $j < $itemCount; $j++) {
        //             $variantId = $faker->randomElement($variantIds);
        //             $qty = rand(1, 3);
        //             $unitPrice = rand(5, 20) * 10000;
        //             $totalPrice = $qty * $unitPrice;
        //             $total += $totalPrice;

        //             DB::table('order_items')->insert([
        //                 'order_id' => $orderId,
        //                 'product_variant_id' => $variantId,
        //                 'product_name' => 'Sản phẩm ' . Str::random(5),
        //                 'product_variant_sku' => 'SKU-' . Str::random(5),
        //                 'quantity' => $qty,
        //                 'unit_price' => $unitPrice,
        //                 'total_price' => $totalPrice,
        //                 'created_at' => $date,
        //                 'updated_at' => $date,
        //             ]);
        //         }

        //         DB::table('orders')->where('id', $orderId)->update([
        //             'total_amount' => $total,
        //         ]);
        //     }
        // }

        // fake dữ liệu cho comment
        // $faker = \Faker\Factory::create();
        // $userIds = \App\Models\User::pluck('id')->toArray();
        // $productIds = \App\Models\Product::pluck('id')->toArray();

        // $statuses = ['chưa duyệt', 'hiển thị', 'ẩn'];

        // foreach ($productIds as $productId) {
        //     for ($i = 0; $i < 6; $i++) {
        //         DB::table('comments')->insert([
        //             'user_id'    => $faker->randomElement($userIds),
        //             'product_id' => $productId,
        //             'content'    => $faker->sentence(10),
        //             'status'     => $faker->randomElement($statuses),
        //             'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // }

        // fake dữ liệu cho giỏ hàng
        // foreach ([1, 2] as $userId) {
        //     // Tạo giỏ hàng
        //     $cartId = DB::table('carts')->insertGetId([
        //         'user_id' => $userId,
        //         'total_amount' => 0,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);

        //     $variantIds = \App\Models\ProductVariant::inRandomOrder()->limit(5)->pluck('id')->toArray();
        //     $totalAmount = 0;

        //     foreach ($variantIds as $variantId) {
        //         $variant = \App\Models\ProductVariant::find($variantId);
        //         $quantity = rand(1, 5);
        //         $unitPrice = $variant->price;
        //         $totalPrice = $unitPrice * $quantity;
        //         $totalAmount += $totalPrice;

        //         DB::table('cart_items')->insert([
        //             'cart_id' => $cartId,
        //             'product_variant_id' => $variantId,
        //             'quantity' => $quantity,
        //             'unit_price' => $unitPrice,
        //             'total_price' => $totalPrice,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }

        //     // Cập nhật lại tổng tiền cho giỏ hàng
        //     DB::table('carts')->where('id', $cartId)->update([
        //         'total_amount' => $totalAmount,
        //     ]);
        // }

        // fake dữ liệu cho người dùng
        // $faker = Faker::create();
        // $genders = ['nam', 'nu', 'khac'];
        // $days = 9; // 7 ngày trước + hôm nay + 2 ngày sau
        // $usersPerDay = intdiv(30, $days);
        // $extra = 30 - ($usersPerDay * $days); // Nếu không chia hết

        // $userId = 1;
        // for ($i = 0; $i < $days; $i++) {
        //     $date = Carbon::today()->subDays(7)->addDays($i); // Bắt đầu từ 7 ngày trước
        //     $count = $usersPerDay + ($i < $extra ? 1 : 0); // Chia đều, dư thì cộng vào đầu

        //     for ($j = 0; $j < $count; $j++, $userId++) {
        //         $name = "User $userId";
        //         $email = "user{$userId}@example.com";
        //         $role = 'client';
        //         $password = Hash::make('password' . $userId);
        //         $status = 1;

        //         $user_id = DB::table('users')->insertGetId([
        //             'name' => $name,
        //             'email' => $email,
        //             'role' => $role,
        //             'password' => $password,
        //             'status' => $status,
        //             'created_at' => $date,
        //             'updated_at' => $date,
        //         ]);

        //         DB::table('user_profiles')->insert([
        //             'user_id' => $user_id,
        //             'phone' => '09' . str_pad($userId, 8, '0', STR_PAD_LEFT),
        //             'address' => 'Địa chỉ ' . $userId,
        //             'gender' => $genders[$userId % 3],
        //             'birth_date' => $faker->date('Y-m-d', '2010-01-01'),
        //             'user_image' => $faker->boolean(70) ? null : $faker->imageUrl(200, 200, 'people'),
        //             'created_at' => $date,
        //             'updated_at' => $date,
        //         ]);
        //     }
        // }

    }
}
