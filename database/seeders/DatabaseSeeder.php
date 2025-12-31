<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'balance' => 10000,
            'email_verified_at' => now(),
            'email' => 'user@example.com',
            'password' => bcrypt('password'),

        ])->each(function ($user) {
            Asset::factory(2)->create(['user_id' => $user->id]);
        });

        User::factory(5)->create()->each(function ($user) {
            Asset::factory(2)->create(['user_id' => $user->id]);
        });
    }
}
