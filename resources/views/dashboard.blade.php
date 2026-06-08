<x-app-layout>
<div class="py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 sm:p-8 mb-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-indigo-200 text-sm font-medium mb-1">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                    <h1 class="text-2xl sm:text-3xl font-bold">
                        Selamat datang, {{ Auth::user()->name }} 👋
                    </h1>
                    <p class="text-indigo-200 mt-1 text-sm">Berikut ringkasan aktivitas CRM hari ini.</p>
                </div>
                @if($stats['today_followups'] > 0)
                <div class="bg-white/20 backdrop-blur rounded-xl px-5 py-4 text-center min-w-[130px]">
                    <p class="text-3xl font-bold">{{ $stats['today_followups'] }}</p>
                    <p class="text-indigo-100 text-xs mt-1 font-medium">Followup Hari Ini</p>
                </div>
                @else
                <div class="bg-white/20 backdrop-blur rounded-xl px-5 py-4 text-center min-w-[130px]">
                    <p class="text-3xl font-bold">✓</p>
                    <p class="text-indigo-100 text-xs mt-1 font-medium">Semua Clear!</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Alert Overdue --}}
        @if($stats['overdue'] > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mt-0.5">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ $stats['overdue'] }} followup sudah lewat jadwal!</p>
                <p class="text-xs text-red-600 mt-0.5">Segera tindaklanjuti agar tidak terlewat.</p>
            </div>
            <a href="{{ route('sales.dashboard') }}" class="ml-auto flex-shrink-0 text-xs font-semibold text-red-700 hover:text-red-900 underline">Lihat →</a>
        </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-blue-100 rounded-xl p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sales'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-green-100 rounded-xl p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Followup Hari Ini</p>
                    <p class="text-2xl font-bold {{ $stats['today_followups'] > 0 ? 'text-green-600' : 'text-gray-900' }}">{{ $stats['today_followups'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-purple-100 rounded-xl p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Calon Customer</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pending_customers'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-orange-100 rounded-xl p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Pending Followup</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_h1'] + $stats['pending_h7'] + $stats['pending_1month'] }}</p>
                </div>
            </div>
        </div>

        {{-- 2-column layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Followup Breakdown --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Breakdown Pending
                </h2>
                @php $max = max($stats['pending_h1'], $stats['pending_h7'], $stats['pending_1month'], 1); @endphp
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>H+1 Aftercare</span>
                            <span class="text-xs font-bold text-yellow-600">{{ $stats['pending_h1'] }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full transition-all" style="width: {{ ($stats['pending_h1'] / $max) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>H+7 Followup</span>
                            <span class="text-xs font-bold text-orange-600">{{ $stats['pending_h7'] }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-orange-400 h-2 rounded-full transition-all" style="width: {{ ($stats['pending_h7'] / $max) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>H+1 Bulan</span>
                            <span class="text-xs font-bold text-red-600">{{ $stats['pending_1month'] }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-red-400 h-2 rounded-full transition-all" style="width: {{ ($stats['pending_1month'] / $max) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('sales.dashboard') }}" class="mt-5 flex items-center justify-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    Lihat Detail Followup
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Followup Hari Ini --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Followup Hari Ini
                        @if($followupToday->count() > 0)
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $followupToday->count() }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('sales.dashboard') }}" class="text-xs text-indigo-600 hover:underline font-medium">Lihat semua →</a>
                </div>
                @if($followupToday->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($followupToday->take(5) as $sale)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-700 font-bold text-sm">{{ strtoupper(substr($sale->customer_name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('sales.show', $sale) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 truncate block">{{ $sale->customer_name }}</a>
                                <p class="text-xs text-gray-400">{{ $sale->phone_number }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                            @if($sale->followup_h1_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h1_date)->isToday())
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-semibold">H+1</span>
                            @elseif($sale->followup_h7_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h7_date)->isToday())
                                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-semibold">H+7</span>
                            @elseif($sale->followup_1month_status === 'pending' && \Carbon\Carbon::parse($sale->followup_1month_date)->isToday())
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-semibold">H+1bln</span>
                            @endif
                            <a href="{{ route('sales.show', $sale) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Detail</a>
                        </div>
                    </div>
                    @endforeach
                    @if($followupToday->count() > 5)
                    <div class="px-6 py-3 text-center text-xs text-gray-400">
                        +{{ $followupToday->count() - 5 }} lainnya — <a href="{{ route('sales.dashboard') }}" class="text-indigo-600 hover:underline">lihat semua</a>
                    </div>
                    @endif
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">Tidak ada followup hari ini</p>
                    <p class="text-xs mt-1">Semua sudah beres! 🎉</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('sales.dashboard') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col items-center gap-3 hover:border-indigo-300 hover:shadow-md transition group text-center">
                <div class="bg-indigo-50 group-hover:bg-indigo-100 rounded-xl p-3 transition">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700">Dashboard Followup</span>
            </a>
            <a href="{{ route('sales.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col items-center gap-3 hover:border-blue-300 hover:shadow-md transition group text-center">
                <div class="bg-blue-50 group-hover:bg-blue-100 rounded-xl p-3 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-700">Data Penjualan</span>
            </a>
            <a href="{{ route('pending-customers.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col items-center gap-3 hover:border-purple-300 hover:shadow-md transition group text-center">
                <div class="bg-purple-50 group-hover:bg-purple-100 rounded-xl p-3 transition">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-purple-700">Calon Customer</span>
            </a>
            <a href="{{ route('aftercare.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col items-center gap-3 hover:border-green-300 hover:shadow-md transition group text-center">
                <div class="bg-green-50 group-hover:bg-green-100 rounded-xl p-3 transition">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-green-700">Aftercare</span>
            </a>
        </div>

    </div>
</div>
</x-app-layout>
