<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 100) as $index) {
            DB::table('feedbacks')->insert([
                'store_id' => $faker->randomElement(array_diff(range(1, 21), [4])),
                'email' => hash('sha256', $faker->unique()->safeEmail),
                'phone' => hash('sha256', $faker->phoneNumber),
                'order_identifier' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'is_received' => $faker->boolean,
                'created_at' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'updated_at' => now(),
            ]);
        }
    }
}
