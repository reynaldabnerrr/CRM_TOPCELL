<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Follow-up Calon Customer') }}
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

            <!-- Filter By Follow-up Type -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Follow-up Schedule:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pending-customers.index', array_merge(request()->query(), ['type' => null])) }}" class="px-4 py-2 rounded {{ !request('type') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua
                    </a>
                    <a href="{{ route('pending-customers.index', array_merge(request()->query(), ['type' => 'h+1'])) }}" class="px-4 py-2 rounded {{ request('type') == 'h+1' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        H+1
                    </a>
                    <a href="{{ route('pending-customers.index', array_merge(request()->query(), ['type' => 'h+7'])) }}" class="px-4 py-2 rounded {{ request('type') == 'h+7' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        H+7
                    </a>
                    <a href="{{ route('pending-customers.index', array_merge(request()->query(), ['type' => 'h+1month'])) }}" class="px-4 py-2 rounded {{ request('type') == 'h+1month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        H+1 Bulan
                    </a>
                </div>
            </div>

            <!-- Filter By Date -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Reference Date:</h3>
                <div class="flex flex-wrap gap-2 items-center">
                    <input type="date" id="dateFilter" value="{{ request('date') ?? now()->toDateString() }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <button onclick="filterByDate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('pending-customers.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                        Reset Filter
                    </a>
                </div>
            </div>

            <script>
                function filterByDate() {
                    const date = document.getElementById('dateFilter').value;
                    const type = "{{ request('type') ?? '' }}";
                    let url = "{{ route('pending-customers.index') }}?";
                    if (date) url += "date=" + date;
                    if (type) url += "&type=" + type;
                    window.location.href = url;
                }
            </script>

            <!-- Status Filter -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Status:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pending-customers.index', ['date' => request('date')]) }}" class="px-4 py-2 rounded {{ !request('status_id') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Status
                    </a>
                    @foreach ($statuses as $s)
                        <a href="{{ route('pending-customers.index', ['status_id' => $s->id, 'date' => request('date')]) }}" class="px-4 py-2 rounded {{ request('status_id') == $s->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ $s->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terakhir Follow-up</th>
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
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ Str::limit($customer->notes, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $lastDateField = 'followup_' . str_replace('+', '', request('type', 'h+1')) . '_last_date';
                                            $lastDate = $customer->{$lastDateField};
                                        @endphp
                                        @if ($lastDate)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                                {{ $lastDate->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm">Belum di-follow-up</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm space-x-2">
                                        <form action="{{ route('pending-customers.update-followup-checkpoint', $customer) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="type" value="{{ request('type', 'h+1') }}">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                                ✓ Catat Follow-up
                                            </button>
                                        </form>
                                        <a href="{{ route('pending-customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900 text-xs font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('pending-customers.destroy', $customer) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium" onclick="return confirm('Apakah Anda yakin?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada data calon customer
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
