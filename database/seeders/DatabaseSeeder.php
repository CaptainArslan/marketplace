<?php

use App\Category;
use App\Product;
use App\SubCategory;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 users
        $users = User::factory()->count(10)->create();
        $users->each(
            function ($user){
                $products = Product::factory()->count(10)->create([
                    'user_id' => $user->id,
                ]);
            });
    }
}
