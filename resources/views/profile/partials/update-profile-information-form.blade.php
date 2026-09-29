<section>
    <div class="flex items-center gap-3 pb-5 border-b border-slate-100 dark:border-slate-700/60">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ __('Profile Information') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ __("Update your account's display name and primary email address.") }}
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <!-- Full Name -->
        <div>
            <label for="name" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('Full Name') }} <span class="text-rose-500">*</span>
            </label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   class="block w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm py-2.5 px-3.5 transition" 
                   value="{{ old('name', $user->name) }}" 
                   required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
            @if($user->role === 'doctor')
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                    Note: Updating your name here updates your clinical roster profile while historical audit logs preserve your recorded name at time of surgery.
                </p>
            @endif
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('Email Address') }} <span class="text-rose-500">*</span>
            </label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   class="block w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm py-2.5 px-3.5 transition" 
                   value="{{ old('email', $user->email) }}" 
                   required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300">
                    <p>
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline font-bold text-amber-900 dark:text-amber-200 hover:text-amber-700 ml-1">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-emerald-600 dark:text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Account Role (Read-only Info) -->
        <div>
            <label class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('Account Role & Access Level') }}
            </label>
            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-100/70 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 text-xs">
                <div class="flex items-center gap-2.5">
                    @php
                        $roleBadgeColor = match($user->role) {
                            'system_admin' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                            'doctor'       => 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                            'data_entry'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                            default        => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $roleBadgeColor }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                    </span>
                    <span class="text-slate-500 dark:text-slate-400">
                        {{ $user->role === 'system_admin' ? 'Full administrative access' : ($user->role === 'doctor' ? 'Clinical surgeon and audit management' : 'Data entry operator') }}
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 italic">Managed by Admin</span>
            </div>
        </div>

        <!-- Submit Button & Feedback -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Profile information updated successfully.') }}
                </div>
            @endif
        </div>
    </form>
</section>
