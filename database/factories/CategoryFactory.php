<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Category>
 */
class CategoryFactory extends Factory
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
            'name' => $faker->name,
            'buyer_fee' => 10.00000,
            'status' => 1,
            'image' => '63ce602359bb01674469411.jpg',
            'created_at' =>  date('Y-m-d', strtotime('+' . mt_rand(15, 20) . ' days')),
            'updated_at' =>  date('Y-m-d', strtotime('+' . mt_rand(20, 30) . ' days')),
        ];
    }
}
