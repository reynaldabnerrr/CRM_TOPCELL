<div x-data="{ sidebarOpen: false }" class="relative">

    <!-- Mobile overlay -->
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
        style="display: none;"
    ></div>

    <!-- Mobile top bar -->
    <div class="md:hidden fixed top-0 left-0 right-0 h-14 bg-gray-900 text-white flex items-center px-4 z-10 shadow-lg">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{'hidden': sidebarOpen, 'inline-flex': !sidebarOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !sidebarOpen, 'inline-flex': sidebarOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="ml-3 flex items-center space-x-2">
            <x-application-logo class="h-8 w-8 rounded-full border border-gray-300 object-cover" />
            <span class="text-lg font-bold">{{ config('app.name', 'TOP CELL') }}</span>
        </a>
    </div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed left-0 top-0 w-64 h-screen bg-gray-900 text-white shadow-lg z-30 transition-transform duration-200 ease-in-out md:translate-x-0 flex flex-col"
    >
    <!-- Logo Section -->
    <div class="flex-shrink-0 p-6 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <x-application-logo class="h-10 w-10 rounded-full border border-gray-300 object-cover" />
            <span class="text-xl font-bold">{{ config('app.name', 'TOP CELL') }}</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto mt-6 px-3 space-y-2 pb-4">
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V3" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Sales Menu -->
        <div class="pt-4 border-t border-gray-700">
            <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Sales Management</p>

            <a href="{{ route('sales.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('sales.dashboard') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Dashboard Followup</span>
            </a>

            <a href="{{ route('sales.index') }}"
                class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('sales.index') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Data Penjualan</span>
            </a>
        </div>

        <!-- Followup Menu -->
        <div class="pt-4 border-t border-gray-700">
            <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Followup Management</p>

            <a href="{{ route('pending-customers.index') }}"
                class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('pending-customers.*') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Calon Customer</span>
            </a>
        </div>

        <!-- Aftercare Menu -->
        <div class="pt-4 border-t border-gray-700">
            <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Aftercare Management</p>

            <a href="{{ route('aftercare.index') }}"
                class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('aftercare.*') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Aftercare</span>
            </a>
        </div>
    </nav>

    <!-- User Section -->
    <div class="flex-shrink-0 border-t border-gray-700 p-4">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center">
                <span class="text-white font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <div class="space-y-2">
            <a href="{{ route('profile.edit') }}"
                class="block w-full px-3 py-2 rounded-lg hover:bg-gray-800 text-sm transition">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-800 text-sm transition bg-transparent border-0 cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </div>
    </aside>

    <!-- Main Content Area -->
    <div class="md:ml-64 pt-14 md:pt-0 min-h-screen">
        {{ $slot }}
    </div>

</div>