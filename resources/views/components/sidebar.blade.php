@props([])

<div class="flex">
    <!-- Sidebar -->
    <aside
        class="fixed left-0 top-0 w-64 h-screen bg-gradient-to-b from-gray-50 to-gray-100 border-r border-gray-300 shadow-xl">
        <!-- Logo Section -->
        <div class="p-6 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-br-2xl shadow-lg">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <x-application-logo class="h-12 w-12 rounded-full border-2 border-white shadow-md object-cover" />
                <div>
                    <span class="text-2xl font-bold block">TopCell</span>
                    <span class="text-xs text-red-100">CRM</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-8 px-3 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest px-4 mb-4">Menu</p>
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-700 shadow-md border-l-4 border-red-600' : 'text-gray-700 hover:bg-white hover:shadow-md transition' }} font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V3" />
                </svg>
                <span>Dashboard</span>
            </a>
        </nav>

        <!-- User Section -->
        <div
            class="absolute bottom-0 left-0 right-0 border-t border-gray-300 p-4 w-64 bg-gradient-to-t from-gray-100 to-white">
            <div class="bg-white rounded-xl p-4 shadow-md mb-4 border border-gray-200">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center flex-shrink-0 shadow-md">
                        <span
                            class="text-white font-bold text-lg">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 text-sm transition font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 text-sm transition font-medium bg-transparent border-0 cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="ml-64 w-full">
        {{ $slot }}
    </div>
</div>