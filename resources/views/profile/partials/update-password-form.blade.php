<section>
    <div class="flex items-center gap-3 pb-5 border-b border-slate-100 dark:border-slate-700/60">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ __('Update Password') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ __('Ensure your account is using a strong, randomized password to protect system clinical records.') }}
            </p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label for="update_password_current_password" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('Current Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="block w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm py-2.5 px-3.5 transition" 
                   autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('New Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="block w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm py-2.5 px-3.5 transition" 
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <p class="text-[11px] text-slate-400 mt-1">
                Must be at least 8 characters. We recommend a mix of uppercase letters, numbers, and symbols.
            </p>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                {{ __('Confirm New Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="block w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm py-2.5 px-3.5 transition" 
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button & Feedback -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-amber-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Password updated successfully.') }}
                </div>
            @endif
        </div>
    </form>
</section>
