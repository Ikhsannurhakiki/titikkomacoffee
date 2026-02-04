<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-2xl mb-4 shadow-sm">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Admin Portal</h2>
        <p class="text-sm text-gray-500 mt-1">Silakan masuk untuk mengelola sistem</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email"
                class="block text-xs font-black text-gray-700 uppercase tracking-widest mb-1 ml-1">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <input id="email"
                    class="block w-full pl-10 pr-3 py-3 bg-gray-50 border-gray-200 border-2 rounded-2xl text-sm focus:border-indigo-500 focus:ring-0 transition-all duration-200"
                    type="email" name="email" :value="old('email')" required autofocus />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1 ml-1">
                <label for="password"
                    class="block text-xs font-black text-gray-700 uppercase tracking-widest">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-indigo-600 hover:text-indigo-500 uppercase tracking-tighter"
                        href="{{ route('password.request') }}">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="relative" x-data="{ show: false }">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <input id="password" :type="show ? 'text' : 'password'"
                    class="block w-full pl-10 pr-10 py-3 bg-gray-50 border-gray-200 border-2 rounded-2xl text-sm focus:border-indigo-500 focus:ring-0 transition-all duration-200"
                    name="password" required />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-500">
                    <svg class="h-5 w-5" fill="none" x-show="!show" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                        <path
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            stroke-width="2" />
                    </svg>
                    <svg class="h-5 w-5" fill="none" x-show="show" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="w-4 h-4 rounded-md border-gray-300 text-indigo-600 shadow-sm focus:ring-0" name="remember">
                <span
                    class="ms-2 text-xs font-bold text-gray-600 uppercase tracking-tight">{{ __('Remember me') }}</span>
            </label>
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
            Masuk Ke Dashboard
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>

        <div class="text-center mt-6">
            <a href="{{ route('role-login') }}"
                class="text-[10px] font-black text-gray-400 hover:text-amber-600 uppercase tracking-widest transition-colors">
                ← Kembali ke Login Staff
            </a>
        </div>
    </form>
</x-guest-layout>
