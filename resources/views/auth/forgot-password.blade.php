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
                        <h2 class="text-md font-black text-gray-400 tracking-[0.3em] uppercase">FORGOT PASSWORD
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <div class="relative">

                            <div class="mb-4 text-sm text-gray-600">
                                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                            </div>

                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <!-- Email Address -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email')" required autofocus />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        {{ __('Email Password Reset Link') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-guest-layout>
