<x-guest-layout>
    <div x-data="{ 
            showAdminNotice: false,
            showPassword: false 
         }" 
         class="w-full max-w-md sm:max-w-lg">

        <!-- Main Card Container -->
        <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-2xl shadow-indigo-950/5 dark:shadow-black/40 overflow-hidden transition-all duration-300">
            
            <!-- Top Card Header & Hospital Logo -->
            <div class="pt-8 pb-6 px-6 sm:px-10 text-center border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/30">
                <!-- Hospital Crest / Logo -->
                <div class="mb-4 inline-block">
                    <img src="{{ asset('images/hospital_logo.jpg') }}" 
                         alt="Teaching Hospital Jaffna" 
                         class="h-16 w-auto mx-auto object-contain drop-shadow-sm rounded-xl"
                         onerror="this.onerror=null; this.style.display='none'; document.getElementById('fallback-logo').classList.remove('hidden');" />
                    
                    <!-- Fallback SVG if image not found -->
                    <div id="fallback-logo" class="hidden w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-600 to-sky-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/25">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Title & Portal Pill -->
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Anaesthesia Audit System
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Teaching Hospital Jaffna &bull; Staff Authentication
                </p>

                <div class="mt-3 flex items-center justify-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[0.7rem] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Authorized Clinical Portal
                    </span>
                </div>
            </div>

            <!-- Card Body / Form Area -->
            <div class="p-6 sm:p-10 space-y-6">
                
                <!-- Session Status Message -->
                <x-auth-session-status class="mb-2" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Username or Email Address -->
                    <div>
                        <label for="login" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Username or Email') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input id="login" 
                                   type="text" 
                                   name="login" 
                                   value="{{ old('login', old('email')) }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="Username or email address..."
                                   class="block w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm font-medium transition shadow-sm" />
                        </div>
                        <x-input-error :messages="$errors->get('login') ?: $errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ __('Password') }} <span class="text-rose-500">*</span>
                            </label>
                            
                            <!-- Clickable Forgot Password Link -->
                            <button type="button" 
                                    @click="showAdminNotice = !showAdminNotice" 
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline underline-offset-2 transition focus:outline-none cursor-pointer">
                                {{ __('Forgot password?') }}
                            </button>
                        </div>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="Enter your password..."
                                   class="block w-full pl-11 pr-11 py-3 rounded-2xl bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm font-medium transition shadow-sm" />
                            
                            <!-- Toggle Password Visibility Eye Button -->
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 focus:outline-none cursor-pointer"
                                    title="Show or hide password">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-slate-900 transition cursor-pointer">
                            <span class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400">
                                {{ __('Remember this workstation') }}
                            </span>
                        </label>
                    </div>

                    <!-- Smooth Animated Contact Administrator Message Box -->
                    <div class="grid transition-all duration-300 ease-in-out"
                         :class="showAdminNotice ? 'grid-rows-[1fr] opacity-100 mt-4' : 'grid-rows-[0fr] opacity-0 mt-0 pointer-events-none'">
                        <div class="overflow-hidden min-h-0">
                            <div class="p-4 rounded-2xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/80 text-amber-900 dark:text-amber-200 shadow-sm flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center flex-shrink-0 text-amber-600 dark:text-amber-400 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 text-xs">
                                    <p class="font-extrabold text-amber-950 dark:text-amber-100 text-sm">
                                        {{ __('Password Reset Request') }}
                                    </p>
                                    <p class="mt-1 text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                                        {{ __('For clinical and patient record safety, self-service password reset is disabled. Please contact your Hospital System Administrator to reset your credentials.') }}
                                    </p>
                                </div>
                                <button type="button" 
                                        @click="showAdminNotice = false" 
                                        class="p-1 rounded-lg text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-200 hover:bg-amber-100 dark:hover:bg-amber-900/50 focus:outline-none cursor-pointer transition">
                                    <span class="sr-only">Close</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sign In Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold text-sm tracking-wide shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/35 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            <span>Sign In to Audit System</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Card Bottom Bar: Security & Compliance -->
            <!-- <div class="py-4 px-6 sm:px-10 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[0.7rem] text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>256-Bit Encrypted Audit Log</span>
                </span>
                <span class="font-medium text-slate-400 dark:text-slate-500">
                    v2.0 &bull; Clinical Edition
                </span>
            </div> -->

        </div>

    </div>
</x-guest-layout>
