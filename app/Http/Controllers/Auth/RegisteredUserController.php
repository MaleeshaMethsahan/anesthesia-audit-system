<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View; 

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $canCreateAdmin = User::canCreateAdmin();
        return view('auth.register', compact('canCreateAdmin'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Validate the role
            'role' => ['required', 'in:system_admin,doctor,data_entry'], 
        ]);

        if ($request->role === 'system_admin' && !User::canCreateAdmin()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'role' => ['The maximum limit of ' . User::MAX_ADMINS . ' System Administrator accounts has been reached.'],
            ]);
        }

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role, // Save the role
            ]);

            // If the new user is a doctor, ensure a matching staff record exists for the audit form
            if ($user->role === 'doctor') {
                Staff::firstOrCreate([
                    'name' => $user->name,
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        return redirect($this->redirectPath($user));
    }

    // Helper function to decide where to go
    protected function redirectPath($user)
    {
        switch ($user->role) {
            case 'system_admin':
                return route('admin.dashboard');
            case 'doctor':
                return route('doctor.dashboard');
            case 'data_entry':
                return route('data_entry.dashboard');
            default:
                return route('dashboard');
        }
    }
}
