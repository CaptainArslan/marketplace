<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\User>
 */
class UserFactory extends Factory
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
            'firstname' => $faker->name,
            'lastname' => $faker->name,
            'username' => $faker->userName,
            'level_id' => 1,
            'top_author' => 1,
            'country_code' => 'PK',
            'email' => $faker->unique()->safeEmail,
            'mobile' => 923000440559,
            'ref_by' => 0,
            'balance' => 0.00000000,
            'earning' => 0.00000000,
            'total_response' => 0,
            'total_rating' => 0,
            'avg_rating' => 0,
            'image' => '63eb86651205a1676379749.jpg',
            'cover_image' => null,
            'description' => null,
            'ev' => 1,
            'sv' =>1,
            'ver_code' => null,
            'ver_code_send_at' => null,
            'ts' => 0,
            'tv' => 0,
            'tsc' => null,
            'provider' => null,
            'provider_id' => null,
            'remember_token' => null,
            'address' => '{"address":"Chungi Number 1 New Airport Road  Multan","state":"Pakistan","zip":"60000","city":"Multan","country":"Pakistan"}',
            'seller' => 1,
            'company_logo' => '63eb89abe71ba1676380587.png',
            'password' => '$2y$10$ZmVRiDBatPQY0rgOSrDGWOJqoOKFVX3IdIxIT99noWhtykKj.aiQK', // password
            'created_at' =>  date('Y-m-d', strtotime('+' . mt_rand(1, 5) . ' days')),
            'updated_at' =>  date('Y-m-d', strtotime('+' . mt_rand(1, 5) . ' days'))
        ];
    }
}
