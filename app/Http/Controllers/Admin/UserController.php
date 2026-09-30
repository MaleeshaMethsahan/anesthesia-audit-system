<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of user accounts.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('role', '');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, ['system_admin', 'doctor', 'data_entry'])) {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $adminCount = User::adminCount();
        $maxAdmins = User::MAX_ADMINS;

        return view('admin.users.index', compact('users', 'search', 'roleFilter', 'adminCount', 'maxAdmins'));
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        $canCreateAdmin = User::canCreateAdmin();
        $adminCount = User::adminCount();
        $maxAdmins = User::MAX_ADMINS;

        return view('admin.users.create', compact('canCreateAdmin', 'adminCount', 'maxAdmins'));
    }

    /**
     * Store the new user in the database.
     */
    public function store(Request $request)
    {
        if (! $request->filled('username') && $request->filled('email')) {
            $base = \Illuminate\Support\Str::slug(explode('@', (string) $request->email)[0], '_') ?: 'user';
            $candidate = \Illuminate\Support\Str::lower(substr($base, 0, 45));
            $counter = 1;
            while (User::where('username', $candidate)->exists()) {
                $candidate = \Illuminate\Support\Str::lower(substr($base, 0, 40)) . '_' . $counter++;
            }
            $request->merge(['username' => $candidate]);
        }

        // 1. Validate
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'min:3', 'max:50', 'unique:'.User::class],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:system_admin,doctor,data_entry'],
        ]);

        // Enforce maximum 2 system admin accounts
        if ($request->role === 'system_admin' && !User::canCreateAdmin()) {
            throw ValidationException::withMessages([
                'role' => ['The maximum limit of ' . User::MAX_ADMINS . ' System Administrator accounts has been reached.'],
            ]);
        }

        DB::transaction(function () use ($request) {
            // 2. Create User
            User::create([
                'name'     => $request->name,
                'username' => strtolower($request->username),
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // 3. If the new user is a doctor, ensure a matching staff record exists for audits.
            if ($request->role === 'doctor') {
                Staff::firstOrCreate([
                    'name' => $request->name,
                ]);
            }
        });

        // 4. Redirect to users list with success message
        return redirect()->route('admin.users.index')->with('status', 'New user registered successfully!');
    }

    /**
     * Show the form for editing the specified user account.
     */
    public function edit(User $user)
    {
        $canCreateAdmin = User::canCreateAdmin();
        $adminCount = User::adminCount();
        $maxAdmins = User::MAX_ADMINS;

        return view('admin.users.edit', compact('user', 'canCreateAdmin', 'adminCount', 'maxAdmins'));
    }

    /**
     * Update the specified user account in storage.
     */
    public function update(Request $request, User $user)
    {
        if (! $request->filled('username')) {
            $request->merge(['username' => $user->username]);
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'min:3', 'max:50', Rule::unique(User::class)->ignore($user->id)],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role'     => ['required', 'in:system_admin,doctor,data_entry'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Enforce maximum 2 system admin accounts if changing role to system_admin
        if ($validated['role'] === 'system_admin' && $user->role !== 'system_admin' && !User::canCreateAdmin()) {
            throw ValidationException::withMessages([
                'role' => ['The maximum limit of ' . User::MAX_ADMINS . ' System Administrator accounts has been reached.'],
            ]);
        }

        DB::transaction(function () use ($validated, $user) {
            $user->name     = $validated['name'];
            $user->username = strtolower($validated['username']);
            $user->email    = $validated['email'];
            $user->role     = $validated['role'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // HISTORICAL AUDIT INTEGRITY:
            // If the user is a doctor, ensure a matching Staff record exists for their current name.
            // Notice: We intentionally do NOT rename the previous Staff record.
            // This ensures all historical audit records created under the doctor's previous name
            // remain intact and accurately reflect the doctor's name at the time of surgery,
            // while all newly created audits will use the updated name.
            if ($user->role === 'doctor') {
                Staff::firstOrCreate([
                    'name' => $user->name,
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('status', 'User account updated successfully!');
    }

    /**
     * Remove the specified user account from storage.
     *
     * IMPORTANT: Clinical Audit Preservation
     * Deleting a doctor or staff user account ONLY deletes the authentication credentials
     * from the `users` table (and associated login logs). It MUST NOT delete, cascade to,
     * or modify any anesthesia audit records, surgeries, patient profiles, or historical
     * staff records.
     */
    public function destroy(User $user)
    {
        // Prevent System Admin from deleting their own currently logged-in account
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;
        $userRole = $user->role;

        // Perform user account deletion
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "Account for '{$userName}' ({$userRole}) deleted successfully. All historical clinical audit records remain intact.");
    }
}
