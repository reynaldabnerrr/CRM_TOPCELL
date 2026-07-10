<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aftercare & Follow-up Pelanggan') }}
        </h2>
    </x-slot>

@php $settings = \App\Models\QontakSetting::getSettings(); @endphp
    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter By Date -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Pilih Tanggal (sebagai "hari ini"):</h3>
                <div class="flex flex-wrap gap-2 items-center">
                    <input type="date" id="dateFilter" value="{{ $referenceDate->toDateString() }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <button onclick="filterByDate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('aftercare.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 mr-2">
                        Reset Tanggal
                    </a>

                    @if ($status === 'pending')
                        <form action="{{ route('aftercare.broadcast-all') }}" method="POST" class="inline sm:ml-auto" onsubmit="return confirm('Apakah Anda yakin ingin mengirimkan broadcast WhatsApp ke semua pelanggan pending yang muncul di daftar saat ini?')">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="date" value="{{ $referenceDate->toDateString() }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition shadow-sm text-sm">
                                🚀 Broadcast Semua Hari Ini
                            </button>
                        </form>
                    @endif
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
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Jenis Aftercare:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('aftercare.index', ['type' => 'all', 'status' => $status, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ $type === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Tipe
                    </a>
                    @foreach ($types as $t)
                        <a href="{{ route('aftercare.index', ['type' => $t, 'status' => $status, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ $type === $t ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ $t }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filter By Status -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Status:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($statuses as $s)
                        <a href="{{ route('aftercare.index', ['type' => $type, 'status' => $s, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 rounded {{ $status === $s ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ ucfirst($s) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <form method="GET" action="{{ route('aftercare.index') }}" class="flex flex-wrap gap-2 items-end">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="hidden" name="date" value="{{ $referenceDate->toDateString() }}">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Cari berdasarkan:</label>
                        <select name="search_by" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="customer_name" {{ $searchBy === 'customer_name' ? 'selected' : '' }}>Nama Customer</option>
                            <option value="invoice_number" {{ $searchBy === 'invoice_number' ? 'selected' : '' }}>No. Faktur</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Kata kunci:</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik untuk mencari..." class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Cari</button>
                    @if($search)
                    <a href="{{ route('aftercare.index', ['type' => $type, 'status' => $status, 'date' => $referenceDate->toDateString()]) }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 text-sm font-medium">Reset</a>
                    @endif
                </form>
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
                                            // Tentukan tipe followup untuk baris ini
                                            if ($type === 'all') {
                                                $invoiceDateStr = $sale->invoice_date->toDateString();
                                                $rowType = match(true) {
                                                    $invoiceDateStr === $referenceDate->copy()->subDays(1)->toDateString()  => 'Aftercare h+1',
                                                    $invoiceDateStr === $referenceDate->copy()->subDays(7)->toDateString()  => 'Aftercare h+7',
                                                    $invoiceDateStr === $referenceDate->copy()->subDays(30)->toDateString() => 'Aftercare h+1bulan',
                                                    default => 'Aftercare h+1',
                                                };
                                            } else {
                                                $rowType = $type;
                                            }
                                            $statusColumn = match($rowType) {
                                                'Aftercare h+1'      => 'followup_h1_status',
                                                'Aftercare h+7'      => 'followup_h7_status',
                                                'Aftercare h+1bulan' => 'followup_1month_status',
                                                default              => 'followup_h1_status',
                                            };
                                            $saleStatus = $sale->$statusColumn;
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-50 text-indigo-700 font-medium block mb-1">{{ $rowType }}</span>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $saleStatus === 'completed' ? 'bg-green-100 text-green-800' : ($saleStatus === 'skipped' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($saleStatus) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($saleStatus === 'pending')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('aftercare.send-wa', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Kirim WhatsApp {{ $rowType }} ke {{ addslashes($sale->customer_name) }}?')">
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{ $rowType }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-xs font-semibold transition" title="Kirim template via WhatsApp Business Qontak">
                                                        💬 Kirim WA
                                                    </button>
                                                </form>
                                                <form action="{{ route('aftercare.complete', $sale) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="type" value="{{ $rowType }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-semibold transition">
                                                        ✓ Selesai
                                                    </button>
                                                </form>
                                                <form action="{{ route('aftercare.skip', $sale) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="type" value="{{ $rowType }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-xs font-semibold transition">
                                                        Skip
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif ($saleStatus === 'skipped')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('aftercare.complete', $sale) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="type" value="{{ $rowType }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-semibold transition">
                                                        ✓ Selesai
                                                    </button>
                                                </form>
                                                <form action="{{ route('aftercare.pending', $sale) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="type" value="{{ $rowType }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg text-xs font-semibold transition">
                                                        ↩ Pending
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif ($saleStatus === 'completed')
                                            <form action="{{ route('aftercare.pending', $sale) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="{{ $rowType }}">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg text-xs font-semibold transition">
                                                    ↩ Pending
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

        <!-- Alpine.js Customization Modal -->
        </div>
    </div>
</x-app-layout>
