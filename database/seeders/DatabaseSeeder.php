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
        $faker = Faker::create();
        $insertedProductIds = [];

        try {
            for ($i = 0; $i < 100; $i++) {
                // Generate fake product data
                $name = $faker->word . ' Product';
                $slug = $faker->slug();
                $sortDes = $faker->sentence();
                $description = $faker->paragraph();
                $quantity = $faker->numberBetween(1, 100);
                $dateOfEntry = $faker->dateTimeThisYear();
                $image = 'image_' . $faker->uuid . '.jpg';
                $view = $faker->numberBetween(0, 1000);
                $createdAt = $faker->dateTimeThisYear();
                $updatedAt = $faker->dateTimeThisYear();
                $deletedAt = null;

                // Insert product
                $productId = DB::table('products')->insertGetId([
                    'category_id' => 7,
                    'brand_id' => 7,
                    'name' => $name,
                    'slug' => $slug,
                    'sort_des' => $sortDes,
                    'description' => $description,
                    'quantity' => $quantity,
                    'date_of_entry' => $dateOfEntry,
                    'image' => $image,
                    'view' => $view,
                    'status' => 1, // Assuming 1 means active
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => $deletedAt,
                ]);

                $insertedProductIds[] = $productId;

                // Generate fake variant data
                $attributeName = $faker->word;
                $price = $faker->randomFloat(2, 10, 1000);
                $status = $faker->numberBetween(0, 1);
                $sku = $faker->bothify('PROD-####');

                // Insert variant
                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'attribute_name' => $attributeName,
                    'image' => $image,
                    'price' => $price,
                    'quantity' => $quantity,
                    'status' => $status,
                    'sku' => $sku, // Added SKU
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => $deletedAt,
                ]);
            }
        } catch (\Exception $e) {
            // Rollback if seeding fails
            if (!empty($insertedProductIds)) {
                DB::table('product_variants')->whereIn('product_id', $insertedProductIds)->delete();
                DB::table('products')->whereIn('id', $insertedProductIds)->delete();
            }
            throw $e; // Re-throw the exception to stop the seeder and notify the user
        }
    }
}
