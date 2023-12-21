<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\SubCategory>
 */
class SubCategoryFactory extends Factory
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
            'name' => $faker->name(),
            'category_id' => \App\Category::factory(),
            'status' => 1,
            'created_at' =>  date('Y-m-d', strtotime('+' . mt_rand(15, 20) . ' days')),
            'updated_at' =>  date('Y-m-d', strtotime('+' . mt_rand(20, 30) . ' days')),
        ];
    }
}
