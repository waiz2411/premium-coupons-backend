<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
        ['email' => 'admin@savesnapper.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('*fJMSQw=K7f'),
        ]
        );
        // 👉 Call CountrySeeder
        $this->call(CountrySeeder::class);
    }
}
