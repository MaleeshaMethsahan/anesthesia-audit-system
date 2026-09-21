<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                    {{ __('Edit User Account') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Update user details, credentials, or role for {{ $user->name }}.
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="p-6 sm:p-8">
                    
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" class="font-bold text-slate-700 dark:text-slate-300" />
                            <x-text-input id="name" 
                                          class="block mt-1.5 w-full rounded-xl dark:bg-slate-900 dark:border-slate-700" 
                                          type="text" 
                                          name="name" 
                                          :value="old('name', $user->name)" 
                                          required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Note: For doctors, existing clinical audit records will preserve the original name under which they were created.
                            </p>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="font-bold text-slate-700 dark:text-slate-300" />
                            <x-text-input id="email" 
                                          class="block mt-1.5 w-full rounded-xl dark:bg-slate-900 dark:border-slate-700" 
                                          type="email" 
                                          name="email" 
                                          :value="old('email', $user->email)" 
                                          required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <x-input-label for="role" :value="__('Account Role')" class="font-bold text-slate-700 dark:text-slate-300" />
                            <select id="role" 
                                    name="role" 
                                    class="block mt-1.5 w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm">
                                <option value="doctor" {{ old('role', $user->role) === 'doctor' ? 'selected' : '' }}>Doctor (Clinical Access & Audit Logging)</option>
                                <option value="data_entry" {{ old('role', $user->role) === 'data_entry' ? 'selected' : '' }}>Data Entry (Audit Records Data Entry)</option>
                                <option value="system_admin" {{ old('role', $user->role) === 'system_admin' ? 'selected' : '' }}>System Admin (System Administration)</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700/80 pt-6">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                                Change Password (Optional)
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                Leave blank if you do not want to change the user's password.
                            </p>

                            <!-- Password -->
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="password" :value="__('New Password')" class="text-xs font-semibold text-slate-600 dark:text-slate-400" />
                                    <x-text-input id="password" 
                                                  class="block mt-1.5 w-full rounded-xl dark:bg-slate-900 dark:border-slate-700"
                                                  type="password"
                                                  name="password"
                                                  autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-xs font-semibold text-slate-600 dark:text-slate-400" />
                                    <x-text-input id="password_confirmation" 
                                                  class="block mt-1.5 w-full rounded-xl dark:bg-slate-900 dark:border-slate-700"
                                                  type="password"
                                                  name="password_confirmation" 
                                                  autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700/80">
                            <a href="{{ route('admin.users.index') }}" 
                               class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transition">
                                Save Changes
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
