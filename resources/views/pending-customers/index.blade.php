<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Calon Customer') }}
            </h2>
            <a href="{{ route('pending-customers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                + Tambah Calon Customer
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
                    <form method="GET" action="{{ route('pending-customers.index') }}" class="flex flex-wrap gap-2 items-end">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Cari berdasarkan:</label>
                            <select name="search_by" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="name"         {{ ($searchBy ?? 'name') === 'name'         ? 'selected' : '' }}>Nama</option>
                                <option value="phone_number" {{ ($searchBy ?? '') === 'phone_number' ? 'selected' : '' }}>No. HP</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[220px]">
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Kata kunci:</label>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Ketik untuk mencari..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Status:</label>
                            <select name="status_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $s)
                                    <option value="{{ $s->id }}" {{ request('status_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Cari</button>
                        @if(!empty($search) || request('status_id'))
                        <a href="{{ route('pending-customers.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 text-sm font-medium">Reset</a>
                        @endif
                    </form>
                    @if(!empty($search))
                    <p class="mt-2 text-xs text-gray-500">
                        Hasil pencarian <span class="font-semibold text-gray-700">"{{ $search }}"</span>
                        — {{ $customers->total() }} data ditemukan
                    </p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal Follow-up</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone_number) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $customer->phone_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $customer->entry_date ? $customer->entry_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $customer->status->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-[180px]">
                                        {{ Str::limit($customer->notes, 50) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="space-y-1 text-xs">
                                            <div class="flex gap-1 items-center">
                                                <span class="text-gray-400 w-14">H+1:</span>
                                                @if($customer->followup_h1_last_date)
                                                    <span class="text-green-600 font-medium">✓ {{ $customer->followup_h1_last_date->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-yellow-600">{{ $customer->followup_h1_date ? $customer->followup_h1_date->format('d M Y') : '-' }}</span>
                                                @endif
                                            </div>
                                            <div class="flex gap-1 items-center">
                                                <span class="text-gray-400 w-14">H+7:</span>
                                                @if($customer->followup_h7_last_date)
                                                    <span class="text-green-600 font-medium">✓ {{ $customer->followup_h7_last_date->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-yellow-600">{{ $customer->followup_h7_date ? $customer->followup_h7_date->format('d M Y') : '-' }}</span>
                                                @endif
                                            </div>
                                            <div class="flex gap-1 items-center">
                                                <span class="text-gray-400 w-14">H+1bln:</span>
                                                @if($customer->followup_h1month_last_date)
                                                    <span class="text-green-600 font-medium">✓ {{ $customer->followup_h1month_last_date->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-yellow-600">{{ $customer->followup_h1month_date ? $customer->followup_h1month_date->format('d M Y') : '-' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                                        <a href="{{ route('pending-customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900 text-xs font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('pending-customers.destroy', $customer) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium" onclick="return confirm('Hapus data ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data calon customer.
                                        <a href="{{ route('pending-customers.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
