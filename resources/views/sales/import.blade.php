<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <strong>Terjadi Error:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('sales.importStore') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                File Excel Penjualan (.xlsx, .xls, .csv)
                            </label>
                            <input 
                                type="file" 
                                id="file" 
                                name="file" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                required
                            >
                            <p class="mt-2 text-sm text-gray-500">
                                Format: No. Faktur, Tanggal, Unit Usaha, Departement, Gudang, Kontak, No. Telfon Kontak, Sales, Pembayaran, Status
                            </p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                                Batal
                            </a>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">Instruksi Import</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                        <li>Format file harus Excel (.xlsx atau .xls)</li>
                        <li>Baris pertama harus berisi header kolom</li>
                        <li>Kolom minimal yang diperlukan: No. Faktur, Tanggal, Kontak, No. Telfon Kontak</li>
                        <li>Data penjualan akan otomatis membuat aftercare records (h+1, h+7, h+1bulan)</li>
                        <li>Data duplikat (berdasarkan No. Faktur) akan di-update, bukan ditambah baru</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
