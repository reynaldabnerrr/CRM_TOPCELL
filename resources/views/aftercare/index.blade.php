<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aftercare & Follow-up Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter By Date -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Pilih Tanggal (sebagai "hari ini"):</h3>
                <div class="flex gap-2 items-center">
                    <input type="date" id="dateFilter" value="{{ $referenceDate->toDateString() }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <button onclick="filterByDate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('aftercare.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                        Reset Tanggal
                    </a>
                </div>
            </div>

            <script>
                function filterByDate() {
                    const date = document.getElementById('dateFilter').value;
                    if (date) {
                        window.location.href = "{{ route('aftercare.index') }}?date=" + date + "&type={{ $type }}&status={{ $status }}";
                    }
                }
            </script>

            <!-- Filter By Type -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Jenis Follow-up:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('aftercare.index', ['date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ !request('type') || request('type') === '' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Tipe
                    </a>
                    @foreach ($types as $t)
                        <a href="{{ route('aftercare.index', ['type' => $t, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ $type === $t ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ $t }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filter By Status -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Status:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('aftercare.index', ['type' => $type, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ !request('status') || request('status') === '' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Status
                    </a>
                    @foreach ($statuses as $s)
                        <a href="{{ route('aftercare.index', ['type' => $type, 'status' => $s, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ $status === $s ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ ucfirst($s) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Penjualan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Faktur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $sale)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale->invoice_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sale->phone_number) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $sale->phone_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <ul class="list-disc list-inside">
                                            @forelse($sale->items as $item)
                                                <li>{{ $item->item_name }} ({{ $item->quantity }} {{ $item->unit }})</li>
                                            @empty
                                                <span class="text-gray-400">-</span>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @php
                                            $statusColumn = match($type) {
                                                'Aftercare h+1' => 'followup_h1_status',
                                                'Followup h+7' => 'followup_h7_status',
                                                'Followup h+1bulan' => 'followup_1month_status',
                                                default => 'followup_h1_status',
                                            };
                                            $saleStatus = $sale->$statusColumn;
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $saleStatus === 'completed' ? 'bg-green-100 text-green-800' : ($saleStatus === 'skipped' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($saleStatus) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                                        @if ($saleStatus === 'pending')
                                            <form action="{{ route('aftercare.complete', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Tandai sebagai selesai?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">
                                                    ✓ Selesai
                                                </button>
                                            </form>
                                            <form action="{{ route('aftercare.skip', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Skip follow-up ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                                    Skip
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada records untuk tanggal ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $records->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
