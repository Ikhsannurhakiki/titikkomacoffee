<x-guest-layout>
    <div class="min-h-screen bg-[#f8f9fa] flex flex-col items-center justify-center p-6 font-sans antialiased">

        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-amber-600/5 blur-[120px] rounded-full"></div>
        </div>

        <div class="text-center mb-10 relative z-10 animate-fadeIn">
            <div class="flex items-center justify-center space-x-3 mb-2">
                <span class="h-px w-8 bg-amber-900/20"></span>
                <p class="text-[11px] font-black text-amber-900/60 uppercase tracking-[0.4em]">
                    {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                </p>
                <span class="h-px w-8 bg-amber-900/20"></span>
            </div>
        </div>

        <div class="w-full max-w-md relative">
            <div
                class="bg-white rounded-[3.5rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden animate-slideUp">

                <div class="p-6 md:p-6">
                    <div class="text-center mb-10">
                        <img src="{{ asset('images/logo-text-v2.png') }}" class="h-50 mx-auto object-contain"
                            alt="Logo">
                        <h2 class="text-md font-black text-gray-400 tracking-[0.3em] uppercase">Hello Admin!
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <div class="relative">
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                @csrf
                                <div>
                                    <label for="email"
                                        class="block text-xs font-black text-gray-700 uppercase tracking-widest mb-1 ml-1">Email
                                        Address</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <input id="email"
                                            class="block w-full pl-10 pr-3 py-3 bg-gray-50 border-gray-200 border-2 rounded-2xl text-sm focus:border-secondary focus:ring-0 transition-all duration-200"
                                            type="email" name="email" :value="old('email')" required autofocus />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold" />
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1 ml-1">
                                        <label for="password"
                                            class="block text-xs font-black text-gray-700 uppercase tracking-widest">Password</label>
                                        @if (Route::has('password.request'))
                                            <a class="text-[10px] font-bold text-primary hover:text-secondary uppercase tracking-tighter"
                                                href="{{ route('password.request') }}">
                                                Forgot?
                                            </a>
                                        @endif
                                    </div>
                                    <div class="relative" x-data="{ show: false }">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <input id="password" :type="show ? 'text' : 'password'"
                                            class="block w-full pl-10 pr-10 py-3 bg-gray-50 border-gray-200 border-2 rounded-2xl text-sm focus:border-secondary focus:ring-0 transition-all duration-200"
                                            name="password" required />

                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-secondary">
                                            <svg class="h-5 w-5" fill="none" x-show="!show" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                    stroke-width="2" />
                                            </svg>
                                            <svg class="h-5 w-5" fill="none" x-show="show" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold" />
                                </div>

                                <div class="flex items-center">
                                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                        <input id="remember_me" type="checkbox"
                                            class="w-4 h-4 rounded-md border-gray-300 text-secondary shadow-sm focus:ring-0"
                                            name="remember">
                                        <span
                                            class="ms-2 text-xs font-bold text-gray-600 uppercase tracking-tight">{{ __('Remember me') }}</span>
                                    </label>
                                </div>

                                <button type="submit"
                                    class="w-full bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-secondary active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                                    Login Admin
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </form>
                            <div class=" border-t border-gray-100 text-center">
                                <p class="text-xs my-3 text-secondary">Bukan Admin? Masuk sebagai Staff</p>

                                <a href="{{ route('role-login') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-xl font-bold text-[10px] text-white uppercase tracking-widest hover:bg-secondary active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-1.116-13.59c1.973-1.85 4.59-3.033 7.469-3.033 4.418 0 8 3.582 8 8 0 2.828-1.465 5.313-3.692 6.746" />
                                    </svg>
                                    Staff Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
