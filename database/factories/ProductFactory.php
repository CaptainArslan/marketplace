<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = FakerFactory::create();
        return [
            'update_status' => 0,
            'user_id'=> \App\User::factory(),
            'category_id' => rand(17,26),
            'sub_category_id' => rand(50, 59) ,
            'server' => 0,
            'status' => 1,
            'featured' => 0,
            'total_sell' => $faker->numberBetween(1, 9),
            'regular_price' =>  $faker->numberBetween(1, 999),
            'total_response' => 0,
            'total_rating' => 0,
            'avg_rating' => 0,
            'support' => 0,
            'support_discount' => 0.00,
            'support_charge' => 0.00,
            'name' => $faker->name(),
            'code' => null,
            'shareable_link' => null,
            'description' => "sjghvrnklejtuil grheuivtyerhvtuiw5y",
            'image' => '63ce6dfb198b81674472955.jpg',
            'category_details' =>[],
            'soft_reject' => '',
            'hard_reject' => '',
            'update_reject' => '',
            'tag'=> ["Sabir","Shah","Jee"],
            'file' => 'https://unsplash.com/photos/Ar-iTL4QKl4',
            'message'=> 'this is the revieweser check message',
            'demo_link' => 'https://themeforest.net/item/multimail-responsive-email-set-mailbuild-online/full_screen_preview/12650481?_ga=2.226392029.695415009.1674472484-1517051926.1674238740',
            'screenshot' => ['63ce6dfb198b81674472955.jpg', '63ce6dfb198b81674472955.jpg'],
            'extended_price' =>  $faker->numberBetween(99, 1111),

            'created_at' =>  date('Y-m-d', strtotime('+' . mt_rand(0, 15) . ' days')),
            'updated_at' =>  date('Y-m-d', strtotime('+' . mt_rand(16, 30) . ' days')),
        ];
    }
}
