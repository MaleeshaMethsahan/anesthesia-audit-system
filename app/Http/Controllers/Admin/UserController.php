<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store the new user in the database.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:system_admin,doctor,data_entry'],
        ]);

        DB::transaction(function () use ($request) {
            // 2. Create User
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // 3. If the new user is a doctor, ensure a matching staff record exists.
            if ($request->role === 'doctor') {
                Staff::firstOrCreate([
                    'name' => $request->name,
                ]);
            }
        });

        // 4. Redirect back with success message
        return redirect()->route('admin.dashboard')->with('status', 'New user created successfully!');
    }
}
