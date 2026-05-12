<aside class="fixed left-0 top-0 w-64 h-screen bg-gray-900 text-white shadow-lg">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <x-application-logo class="h-10 w-10 rounded-full border border-gray-300 object-cover" />
            <span class="text-xl font-bold">{{ config('app.name', 'Laravel') }}</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-3 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-600' : 'hover:bg-gray-800' }} transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V3" />
            </svg>
            <span>Dashboard</span>
        </a>
    </nav>

    <!-- User Section -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-gray-700 p-4">
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
            <a href="{{ route('profile.edit') }}" class="block w-full px-3 py-2 rounded-lg hover:bg-gray-800 text-sm transition">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-800 text-sm transition bg-transparent border-0 cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main Content Area -->
<div class="ml-64">
    {{ $slot }}
</div>
