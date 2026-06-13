<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admindoa@jaffna.org',
        ], [
            'name' => 'DOA Admin',
            'password' => 'admin123@doa',
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);
    }
}
