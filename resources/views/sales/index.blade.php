<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Penjualan') }}
            </h2>
            <a href="{{ route('sales.import') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                + Import Excel
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <!-- Search Bar -->
                <div class="px-6 py-4 border-b bg-gray-50">
                    <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap gap-2 items-end">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Cari berdasarkan:</label>
                            <select name="search_by" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="customer_name" {{ ($searchBy ?? 'customer_name') === 'customer_name' ? 'selected' : '' }}>Nama Customer</option>
                                <option value="phone_number"  {{ ($searchBy ?? '') === 'phone_number'  ? 'selected' : '' }}>No. HP</option>
                                <option value="invoice_number"{{ ($searchBy ?? '') === 'invoice_number'? 'selected' : '' }}>No. Faktur</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[220px]">
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Kata kunci:</label>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Ketik untuk mencari..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Cari
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 text-sm font-medium">
                            Reset
                        </a>
                        @endif
                    </form>
                    @if(!empty($search))
                    <p class="mt-2 text-xs text-gray-500">
                        Menampilkan hasil pencarian untuk <span class="font-semibold text-gray-700">"{{ $search }}"</span>
                        — {{ $sales->total() }} data ditemukan
                    </p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $sale->invoice_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $sale->phone_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($sale->items->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($sale->items as $item)
                                                    <div class="text-xs">
                                                        • {{ $item->item_name }} 
                                                        <span class="text-gray-500">({{ $item->quantity }} {{ $item->unit }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data penjualan. <a href="{{ route('sales.import') }}" class="text-blue-600 hover:underline">Import Excel sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
