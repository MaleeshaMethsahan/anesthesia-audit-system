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
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition">
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

                        <!-- Username -->
                        <div>
                            <x-input-label for="username" :value="__('Username')" class="font-bold text-slate-700 dark:text-slate-300" />
                            <x-text-input id="username" 
                                          class="block mt-1.5 w-full rounded-xl dark:bg-slate-900 dark:border-slate-700" 
                                          type="text" 
                                          name="username" 
                                          :value="old('username', $user->username)" 
                                          required autocomplete="off" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Used for login. Letters, numbers, dashes, and underscores only.
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

                        <!-- Role Selection (Sliding Segmented Control) -->
                        <div x-data="{ 
                            role: '{{ old('role', $user->role) }}',
                            canAdmin: {{ ($user->role === 'system_admin' || ($canCreateAdmin ?? \App\Models\User::canCreateAdmin())) ? 'true' : 'false' }},
                            selectRole(r) {
                                if (r === 'system_admin' && !this.canAdmin) return;
                                this.role = r;
                            }
                        }" class="space-y-2.5">
                            <input type="hidden" name="role" :value="role" id="role">

                            <div class="flex items-center justify-between">
                                <label class="font-bold text-slate-700 dark:text-slate-300 text-sm">
                                    {{ __('Account Role') }}
                                </label>
                                <span class="text-[0.7rem] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full transition-colors"
                                      :class="{
                                          'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800': role === 'doctor',
                                          'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': role === 'data_entry',
                                          'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800': role === 'system_admin'
                                      }"
                                      x-text="role === 'doctor' ? 'Doctor Access' : (role === 'data_entry' ? 'Data Entry Access' : 'System Admin Access')">
                                </span>
                            </div>

                            <!-- Sliding Segmented Container -->
                            <div class="relative p-1.5 bg-slate-100 dark:bg-slate-900/90 rounded-2xl border border-slate-200/90 dark:border-slate-800 select-none shadow-inner">
                                <!-- Animated Sliding Background Pill -->
                                <div class="absolute top-1.5 bottom-1.5 rounded-xl transition-all duration-300 ease-out shadow-sm pointer-events-none"
                                     :style="{
                                         width: 'calc((100% - 12px) / 3)',
                                         left: role === 'doctor' 
                                             ? '6px' 
                                             : (role === 'data_entry' 
                                                 ? 'calc(6px + (100% - 12px) / 3)' 
                                                 : 'calc(6px + ((100% - 12px) / 3) * 2)')
                                     }"
                                     :class="{
                                         'bg-white dark:bg-slate-800 border border-blue-200 dark:border-blue-900/60 shadow-blue-500/10': role === 'doctor',
                                         'bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-900/60 shadow-emerald-500/10': role === 'data_entry',
                                         'bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/60 shadow-rose-500/10': role === 'system_admin'
                                     }">
                                </div>

                                <!-- 3 Buttons Grid -->
                                <div class="relative grid grid-cols-3 gap-1 z-10">
                                    <!-- Doctor Button -->
                                    <button type="button" 
                                            @click="selectRole('doctor')" 
                                            class="py-2.5 px-2 sm:px-4 rounded-xl flex items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer focus:outline-none"
                                            :class="role === 'doctor' ? 'text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="truncate">Doctor</span>
                                    </button>

                                    <!-- Data Entry Button -->
                                    <button type="button" 
                                            @click="selectRole('data_entry')" 
                                            class="py-2.5 px-2 sm:px-4 rounded-xl flex items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer focus:outline-none"
                                            :class="role === 'data_entry' ? 'text-emerald-600 dark:text-emerald-400 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="truncate">Data Entry</span>
                                    </button>

                                    <!-- System Admin Button -->
                                    <button type="button" 
                                            @click="selectRole('system_admin')" 
                                            :disabled="!canAdmin"
                                            class="py-2.5 px-2 sm:px-4 rounded-xl flex items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-bold transition-all duration-200 focus:outline-none"
                                            :class="{
                                                'text-rose-600 dark:text-rose-400 font-extrabold': role === 'system_admin',
                                                'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 cursor-pointer': canAdmin && role !== 'system_admin',
                                                'text-slate-400 dark:text-slate-600 opacity-60 cursor-not-allowed': !canAdmin
                                            }">
                                        <template x-if="canAdmin">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!canAdmin">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </template>
                                        <span class="truncate">Admin</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Role Context Information Box -->
                            <div class="p-3 rounded-xl border text-xs transition-colors duration-200"
                                 :class="{
                                     'bg-blue-50/70 dark:bg-blue-950/20 border-blue-200/70 dark:border-blue-900/50 text-blue-900 dark:text-blue-300': role === 'doctor',
                                     'bg-emerald-50/70 dark:bg-emerald-950/20 border-emerald-200/70 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-300': role === 'data_entry',
                                     'bg-rose-50/70 dark:bg-rose-950/20 border-rose-200/70 dark:border-rose-900/50 text-rose-900 dark:text-rose-300': role === 'system_admin'
                                 }">
                                <p x-show="role === 'doctor'" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong>Doctor:</strong> Clinical access, anesthesia audit entry & review, patient directory, and automatic staff roster linking.</span>
                                </p>
                                <p x-show="role === 'data_entry'" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong>Data Entry:</strong> Dedicated access to create and enter surgery audit records for clinical staff.</span>
                                </p>
                                <p x-show="role === 'system_admin'" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong>System Admin:</strong> Full administrative rights, user management, security audit logs, and system settings.</span>
                                </p>
                            </div>

                            <x-input-error :messages="$errors->get('role')" class="mt-2" />

                            @if($user->role !== 'system_admin' && !($canCreateAdmin ?? \App\Models\User::canCreateAdmin()))
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5 font-medium flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    System Administrator limit reached (maximum 2 accounts allowed).
                                </p>
                            @endif
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
