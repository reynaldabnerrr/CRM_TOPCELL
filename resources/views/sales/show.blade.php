<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('sales.index') }}" class="text-blue-600 hover:text-blue-900">← Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Penjualan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Sales Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Informasi Penjualan</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">No. Invoice</p>
                            <p class="font-semibold">{{ $sale->invoice_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal</p>
                            <p class="font-semibold">{{ $sale->invoice_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nama Customer</p>
                            <p class="font-semibold">{{ $sale->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">No. Telepon</p>
                            <p class="font-semibold">{{ $sale->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Unit Usaha</p>
                            <p class="font-semibold">{{ $sale->unit_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Departement</p>
                            <p class="font-semibold">{{ $sale->department ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Gudang</p>
                            <p class="font-semibold">{{ $sale->warehouse ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-semibold">{{ $sale->status }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sales Person</p>
                            <p class="font-semibold">{{ $sale->sales_person ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Metode Pembayaran</p>
                            <p class="font-semibold">{{ $sale->payment_method ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aftercare Records -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Aftercare & Followup</h3>
                    
                    @if ($aftercareRecords->count() > 0)
                        <div class="space-y-4">
                            @foreach ($aftercareRecords as $record)
                                <div class="border rounded-lg p-4 {{ $record->status === 'completed' ? 'bg-green-50 border-green-200' : ($record->status === 'skipped' ? 'bg-gray-50 border-gray-200' : 'bg-yellow-50 border-yellow-200') }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold">{{ $record->type }}</p>
                                            <p class="text-sm text-gray-600">Terjadwal: {{ $record->scheduled_date->format('d M Y') }}</p>
                                        </div>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $record->status === 'completed' ? 'bg-green-100 text-green-800' : ($record->status === 'skipped' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </div>
                                    @if ($record->done_date)
                                        <p class="text-sm text-gray-600">Selesai: {{ $record->done_date->format('d M Y') }}</p>
                                    @endif
                                    @if ($record->notes)
                                        <p class="text-sm mt-2 text-gray-700">{{ $record->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada aftercare records</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
