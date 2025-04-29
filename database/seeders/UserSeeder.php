<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

require_once app_path('Common/Constants.php');
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            // Initialize Faker
            $faker = Faker::create();

            // Generate 10 fake users
            foreach (range(1, 100) as $index) {
                DB::table('users')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt(USER_DEFAULT_PASSWORD),
                    'mobile' => $faker->unique()->phoneNumber,
                    'role' => $faker->randomElement(['user']),
                    'status' => $faker->randomElement([1, 0, -1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
