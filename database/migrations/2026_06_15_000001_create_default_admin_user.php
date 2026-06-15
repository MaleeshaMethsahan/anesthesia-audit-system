<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->updateOrInsert([
            'email' => 'admindoa@jaffna.org',
        ], [
            'name' => 'DOA Admin',
            'password' => Hash::make('admin123@doa'),
            'role' => 'system_admin',
            'email_verified_at' => now(),
            'updated_at' => now(),
            'created_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admindoa@jaffna.org')
            ->delete();
    }
};