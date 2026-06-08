<x-app-layout>
<div class="py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Followup</h1>
                <p class="text-gray-500 text-sm mt-1">Tracking followup customer berdasarkan tahap pembelian</p>
            </div>
            <a href="{{ route('sales.import') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </a>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-xs font-medium">Total Penjualan</p>
                    <div class="bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ $stats['total_sales'] }}</p>
                <p class="text-blue-200 text-xs mt-1">Data tersimpan</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-green-100 text-xs font-medium">Hari Ini</p>
                    <div class="bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ $stats['today_followups'] }}</p>
                <p class="text-green-200 text-xs mt-1">Harus dikerjakan</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-yellow-100 text-xs font-medium">Pending H+1</p>
                    <div class="bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ $stats['pending_h1'] }}</p>
                <p class="text-yellow-200 text-xs mt-1">Aftercare</p>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-orange-100 text-xs font-medium">Pending H+7</p>
                    <div class="bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ $stats['pending_h7'] }}</p>
                <p class="text-orange-200 text-xs mt-1">Followup mingguan</p>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-sm col-span-2 md:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-red-100 text-xs font-medium">Pending H+1bln</p>
                    <div class="bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ $stats['pending_1month'] }}</p>
                <p class="text-red-200 text-xs mt-1">Followup bulanan</p>
            </div>
        </div>

        {{-- Followup Hari Ini --}}
        @if($followupToday->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <h2 class="text-base font-bold text-gray-900">Followup Hari Ini</h2>
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $followupToday->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">No. Faktur</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Tgl Beli</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stage</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($followupToday as $sale)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-indigo-700 font-bold text-xs">{{ strtoupper(substr($sale->customer_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('sales.show', $sale) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $sale->customer_name }}</a>
                                        <p class="text-xs text-gray-400">{{ $sale->phone_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 hidden sm:table-cell">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4 text-gray-600 hidden md:table-cell">{{ $sale->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($sale->followup_h1_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h1_date)->isToday())
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> H+1 Aftercare
                                    </span>
                                @elseif($sale->followup_h7_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h7_date)->isToday())
                                    <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-800 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> H+7 Followup
                                    </span>
                                @elseif($sale->followup_1month_status === 'pending' && \Carbon\Carbon::parse($sale->followup_1month_date)->isToday())
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> H+1 Bulan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Semua Pending Followup --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-base font-bold text-gray-900">Semua Pending Followup</h2>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $pendingFollowups->total() }}</span>
                </div>
                {{-- Filter Tabs --}}
                <div class="flex items-center gap-1 flex-wrap">
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'h1']) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'h1' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">
                        H+1 <span class="ml-1 opacity-80">({{ $stats['pending_h1'] }})</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'h7']) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'h7' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
                        H+7 <span class="ml-1 opacity-80">({{ $stats['pending_h7'] }})</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => '1month']) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === '1month' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                        H+1bln <span class="ml-1 opacity-80">({{ $stats['pending_1month'] }})</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">No. Faktur</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Tgl Beli</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">H+1</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">H+7</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">H+1bln</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pendingFollowups as $sale)
                        @php
                            $isOverdue = (
                                ($sale->followup_h1_status === 'pending' && $sale->followup_h1_date->isPast() && !$sale->followup_h1_date->isToday()) ||
                                ($sale->followup_h7_status === 'pending' && $sale->followup_h7_date->isPast() && !$sale->followup_h7_date->isToday()) ||
                                ($sale->followup_1month_status === 'pending' && $sale->followup_1month_date->isPast() && !$sale->followup_1month_date->isToday())
                            );
                        @endphp
                        <tr class="{{ $isOverdue ? 'bg-red-50/40' : 'hover:bg-gray-50' }} transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($isOverdue)
                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-red-700 font-bold text-xs">{{ strtoupper(substr($sale->customer_name, 0, 1)) }}</span>
                                    </div>
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-gray-600 font-bold text-xs">{{ strtoupper(substr($sale->customer_name, 0, 1)) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('sales.show', $sale) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $sale->customer_name }}</a>
                                        <p class="text-xs text-gray-400">{{ $sale->phone_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 hidden sm:table-cell">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4 text-gray-600 hidden md:table-cell">{{ $sale->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($sale->followup_h1_status === 'pending')
                                    @php $isLate = $sale->followup_h1_date->isPast() && !$sale->followup_h1_date->isToday(); @endphp
                                    <span class="inline-block {{ $isLate ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }} px-2 py-0.5 rounded text-xs font-semibold">
                                        {{ $isLate ? '⚠ ' : '' }}{{ $sale->followup_h1_date->format('d M') }}
                                    </span>
                                @elseif($sale->followup_h1_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Skip</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($sale->followup_h7_status === 'pending')
                                    @php $isLate = $sale->followup_h7_date->isPast() && !$sale->followup_h7_date->isToday(); @endphp
                                    <span class="inline-block {{ $isLate ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }} px-2 py-0.5 rounded text-xs font-semibold">
                                        {{ $isLate ? '⚠ ' : '' }}{{ $sale->followup_h7_date->format('d M') }}
                                    </span>
                                @elseif($sale->followup_h7_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Skip</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($sale->followup_1month_status === 'pending')
                                    @php $isLate = $sale->followup_1month_date->isPast() && !$sale->followup_1month_date->isToday(); @endphp
                                    <span class="inline-block {{ $isLate ? 'bg-red-100 text-red-700' : 'bg-red-50 text-red-600' }} px-2 py-0.5 rounded text-xs font-semibold">
                                        {{ $isLate ? '⚠ ' : '' }}{{ $sale->followup_1month_date->format('d M') }}
                                    </span>
                                @elseif($sale->followup_1month_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Skip</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">Tidak ada pending followup</p>
                                <p class="text-gray-400 text-xs mt-1">Semua followup sudah diselesaikan!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $pendingFollowups->links() }}
            </div>
        </div>

    </div>
</div>
</x-app-layout>
