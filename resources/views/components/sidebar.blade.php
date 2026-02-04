<div>
    <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
        type="button"
        class="inline-flex items-center p-2 mt-2 ml-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
        </svg>
    </button>

    <aside id="default-sidebar"
        class="fixed top-0 left-0 z-40 w-45 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.15)]"
        aria-label="Sidenav">
        <div class="flex items-center justify-center p-1 h-15 shrink-0">
            <img src="{{ asset('images/logo-text-v2.png') }}" alt="Logo"
                class="h-30 w-auto object-contain opacity-90 hover:opacity-100 transition-opacity">
        </div>
        <div class="pt-2 mt-2 space-y-2 border-t border-gray-200 dark:border-gray-700 "></div>
        <div
            class="overflow-y-auto py-5 px-3 h-full bg-grey-200 border-r border-gray-200 dark:bg-white dark:border-bluex`-700">
            <ul class="space-y-2">
                <li>
                    <div class="flex justify-center gap-4">
                        {{-- Menu Dashboard --}}
                        <x-navlink href="{{ route('dashboard') }}" title="Dashboard" :active="request()->routeIs('dashboard')">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                        </x-navlink>
                    </div>


                </li>
                {{-- <li>
                    <div class="flex justify-center gap-4">
                        {{-- Menu Users --}}
                {{-- <x-navlink href="/users" title="Users">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </x-navlink>
                    </div>
                </li> --}}
                {{-- ROLE: ADMIN, CASHIER ONLY --}}
                @if (in_array(data_get(session('current_staff'), 'position'), ['cashier', 'admin']))
                    <li>
                        <div class="flex justify-center gap-4">
                            {{-- Menu Cashier --}}
                            <x-navlink href="{{ route('pos') }}" :active="request()->routeIs('pos')" wire:navigate title="Cashier">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </x-navlink>
                        </div>
                    </li>
                @endif
                <li>
                    <div class="flex justify-center gap-4">
                        {{-- Menu Orders --}}
                        <x-navlink href="/order-list" title="Order List">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd"
                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </x-navlink>
                    </div>
                </li>
                <li>
                    <div class="flex justify-center gap-4">
                        <x-navlink href="/customers" title="Customers" :active="request()->is('customers*')">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                </path>
                            </svg>
                        </x-navlink>
                    </div>
                </li>
            </ul>
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <div class="flex justify-center gap-4">
                        {{-- Menu Products --}}
                        <x-navlink href="{{ route('product-manager') }}" :active="request()->routeIs('product-manager')" wire:navigate
                            title="Menu Items">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 8h1a4 4 0 010 8h-1M2 8a2 2 0 012-2h11a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8z" />
                            </svg>
                        </x-navlink>
                    </div>
                </li>
                <li>
                    <x-navlink href="staff-manager" title="Staffs" :active="request()->is('staff-manager')">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z">
                            </path>
                        </svg>
                    </x-navlink>
                </li>
                <li>
                    <div class="flex justify-center gap-4">
                        <x-navlink href="/inventory" title="Inventory" :active="request()->is('inventory*')">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                <path fill-rule="evenodd"
                                    d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </x-navlink>
                    </div>
                </li>
            </ul>
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <div class="flex justify-center gap-4">
                        <x-navlink href="/attendance" title="Attendance" :active="request()->is('attendance*')">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.5 2a1 1 0 011-1h5a1 1 0 110 2h-5a1 1 0 01-1-1zm1-4a1 1 0 100 2h4a1 1 0 100-2h-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-navlink>
                    </div>
                </li>
            </ul>
            <div class="pt-5 mt-5 border-t border-gray-200 dark:border-gray-700">
                @if ($currentStaff || auth()->check())
                    <div class="space-y-4">
                        {{-- Info User (Visual Only) --}}
                        <div class="flex items-center p-2">
                            @if ($currentStaff)
                                <div
                                    class="w-10 h-10 bg-amber-900 rounded-xl flex items-center justify-center text-white font-black shadow-sm shrink-0">
                                    {{ substr($currentStaff->name, 0, 1) }}
                                </div>
                                <div class="ml-3 overflow-hidden">
                                    <p class="text-[11px] font-black text-gray-900 truncate uppercase tracking-tighter">
                                        {{ $currentStaff->name }}
                                    </p>
                                    <p class="text-[9px] text-amber-700 font-bold uppercase">
                                        {{ $currentStaff->position }}
                                    </p>
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 bg-indigo-700 rounded-xl flex items-center justify-center text-white font-black shadow-sm shrink-0">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="ml-3 overflow-hidden">
                                    <p
                                        class="text-[11px] font-black text-gray-900 truncate uppercase tracking-tighter">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-[9px] text-indigo-600 font-bold uppercase">Administrator</p>
                                </div>
                            @endif
                        </div>

                        {{-- Link Menu - Dibuat List Terbuka (Fixed) --}}
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-[10px] font-black {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-colors uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Edit Profile
                            </a>

                            @if ($currentStaff)
                                <a href="{{ route('role-login') }}"
                                    class="flex items-center px-4 py-2 text-[10px] font-black text-red-600 hover:bg-red-50 rounded-xl transition-colors uppercase tracking-widest">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Clock Out Staff
                                </a>
                            @else
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2 text-[10px] font-black text-red-600 hover:bg-red-50 rounded-xl transition-colors uppercase tracking-widest">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Logout Admin
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            {{-- <div class="flex justify-center gap-4">
                    <x-navlink href="/profile" title="Profile" :active="request()->is('profile*')">
                        <div class="w-7 h-7 rounded-full border-2 border-primary/20 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=User+Name&background=31221A&color=fff"
                                alt="Profile" class="w-full h-full object-cover">
                        </div>
                    </x-navlink>
                </div>
                </li> --}}
            {{-- <li>
                <div class="flex justify-center gap-4">
                    <x-navlink href="{{ route('logout') }}" title="Logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </x-navlink>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li> --}}
            </ul>
        </div>
    </aside>
</div>
