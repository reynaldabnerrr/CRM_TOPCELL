<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Followup</h1>
            <p class="text-gray-600">Kelola dan tracking followup customer berdasarkan tahap pembelian</p>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Penjualan</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_sales'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending h+1</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_h1'] }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending h+7</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_h7'] }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending h+1bulan</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['pending_1month'] }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['today_followups'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Followup Hari Ini -->
        @if($followupToday->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">🔔 Followup Hari Ini ({{ $followupToday->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">No. Faktur</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal Beli</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stage</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($followupToday as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.customer-detail', $sale->phone_number) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $sale->customer_name }}
                                </a>
                                <div class="text-sm text-gray-600">{{ $sale->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $sale->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($sale->followup_h1_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h1_date)->isToday())
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Aftercare h+1</span>
                                @elseif($sale->followup_h7_status === 'pending' && \Carbon\Carbon::parse($sale->followup_h7_date)->isToday())
                                    <span class="inline-block bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">Followup h+7</span>
                                @elseif($sale->followup_1month_status === 'pending' && \Carbon\Carbon::parse($sale->followup_1month_date)->isToday())
                                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Followup h+1bulan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('sales.customer-detail', $sale->phone_number) }}" class="text-blue-600 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Semua Pending Followups -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Semua Pending Followup</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">No. Faktur</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal Beli</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">h+1</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">h+7</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">h+1bulan</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($pendingFollowups as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.customer-detail', $sale->phone_number) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $sale->customer_name }}
                                </a>
                                <div class="text-sm text-gray-600">{{ $sale->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $sale->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($sale->followup_h1_status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">{{ $sale->followup_h1_date->format('d M') }}</span>
                                @elseif($sale->followup_h1_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Skipped</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($sale->followup_h7_status === 'pending')
                                    <span class="inline-block bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-semibold">{{ $sale->followup_h7_date->format('d M') }}</span>
                                @elseif($sale->followup_h7_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Skipped</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($sale->followup_1month_status === 'pending')
                                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">{{ $sale->followup_1month_date->format('d M') }}</span>
                                @elseif($sale->followup_1month_status === 'done')
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">✓ Done</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Skipped</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('sales.customer-detail', $sale->phone_number) }}" class="text-blue-600 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pending followup</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pendingFollowups->links() }}
            </div>
        </div>
    </div>
</div>
</x-app-layout>
