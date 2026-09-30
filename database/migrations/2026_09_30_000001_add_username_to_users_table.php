<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->after('name');
        });

        // Backfill suitable usernames for default and existing users
        $existingUsers = DB::table('users')->get();
        $usedUsernames = [];

        foreach ($existingUsers as $user) {
            $baseUsername = match ($user->email) {
                'admindoa@jaffna.org' => 'admindoa',
                'admin@gmail.com'    => 'admin',
                default              => Str::slug(explode('@', $user->email)[0], '_') ?: 'user',
            };

            $candidate = Str::lower(substr($baseUsername, 0, 45));
            $counter = 1;
            while (in_array($candidate, $usedUsernames) || DB::table('users')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                $candidate = Str::lower(substr($baseUsername, 0, 40)) . '_' . $counter;
                $counter++;
            }

            $usedUsernames[] = $candidate;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $candidate]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
