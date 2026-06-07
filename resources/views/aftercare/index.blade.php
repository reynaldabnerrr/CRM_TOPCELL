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

            <!-- Filter By Type -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Jenis Aftercare:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('aftercare.index') }}" class="px-4 py-2 rounded {{ !request('type') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Tipe
                    </a>
                    @foreach ($types as $t)
                        <a href="{{ route('aftercare.index', ['type' => $t]) }}" class="px-4 py-2 rounded {{ request('type') === $t ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ $t }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filter By Status -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Status:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('aftercare.index', ['type' => request('type')]) }}" class="px-4 py-2 rounded {{ !request('status') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Status
                    </a>
                    @foreach ($statuses as $s)
                        <a href="{{ route('aftercare.index', ['type' => request('type'), 'status' => $s]) }}" class="px-4 py-2 rounded {{ request('status') === $s ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Terjadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr class="border-b hover:bg-gray-50 {{ $record->status === 'pending' && $record->scheduled_date <= now()->toDateString() ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $record->type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $record->phone_number) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $record->phone_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="{{ $record->status === 'pending' && $record->scheduled_date <= now()->toDateString() ? 'bg-red-100 text-red-800 font-semibold' : 'text-gray-600' }} px-3 py-1 rounded">
                                            {{ $record->scheduled_date->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $record->status === 'completed' ? 'bg-green-100 text-green-800' : ($record->status === 'skipped' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                                        @if ($record->status === 'pending')
                                            <form action="{{ route('aftercare.complete', $record) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">
                                                    ✓ Selesai
                                                </button>
                                            </form>
                                            <form action="{{ route('aftercare.skip', $record) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                                    Skip
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('aftercare.edit', $record) }}" class="text-blue-600 hover:text-blue-900">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada aftercare records
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
