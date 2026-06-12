<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Follow-up Calon Customer') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter Tanggal -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Pilih Tanggal (sebagai "hari ini"):</h3>
                <div class="flex flex-wrap gap-2 items-center">
                    <input type="date" id="dateFilter" value="{{ $referenceDate->toDateString() }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <button onclick="filterByDate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('pending-customers.followup') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                        Reset Tanggal
                    </a>
                </div>
            </div>

            <script>
                function filterByDate() {
                    const date = document.getElementById('dateFilter').value;
                    const type = "{{ $type ?? '' }}";
                    let url = "{{ route('pending-customers.followup') }}?date=" + date;
                    if (type) url += "&type=" + encodeURIComponent(type);
                    window.location.href = url;
                }
            </script>

            <!-- Filter Tipe Follow-up -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Jenis Follow-up:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pending-customers.followup', ['date' => $referenceDate->toDateString()]) }}"
                        class="px-4 py-2 rounded {{ !$type ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Jatuh Tempo
                    </a>
                    @foreach (['h+1' => 'H+1', 'h+7' => 'H+7', 'h+1month' => 'H+1 Bulan'] as $val => $label)
                        <a href="{{ route('pending-customers.followup', ['date' => $referenceDate->toDateString(), 'type' => $val]) }}"
                            class="px-4 py-2 rounded {{ $type === $val ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Filter Status -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Status Calon Customer:</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pending-customers.followup', ['date' => $referenceDate->toDateString(), 'type' => $type]) }}"
                        class="px-4 py-2 rounded {{ !request('status_id') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Semua Status
                    </a>
                    @foreach ($statuses as $s)
                        <a href="{{ route('pending-customers.followup', ['date' => $referenceDate->toDateString(), 'type' => $type, 'status_id' => $s->id]) }}"
                            class="px-4 py-2 rounded {{ request('status_id') == $s->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
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
                                @php
                                    // Tentukan jenis followup yang jatuh tempo hari ini untuk baris ini
                                    $referenceDateStr = $referenceDate->toDateString();
                                    if ($type) {
                                        $dueTypes = [$type];
                                    } else {
                                        $dueTypes = [];
                                        if ($customer->followup_h1_date && $customer->followup_h1_date->toDateString() === $referenceDateStr) $dueTypes[] = 'h+1';
                                        if ($customer->followup_h7_date && $customer->followup_h7_date->toDateString() === $referenceDateStr) $dueTypes[] = 'h+7';
                                        if ($customer->followup_h1month_date && $customer->followup_h1month_date->toDateString() === $referenceDateStr) $dueTypes[] = 'h+1month';
                                    }
                                    $typeLabels = ['h+1' => 'H+1', 'h+7' => 'H+7', 'h+1month' => 'H+1 Bulan'];
                                    $lastDateFields = ['h+1' => 'followup_h1_last_date', 'h+7' => 'followup_h7_last_date', 'h+1month' => 'followup_h1month_last_date'];
                                @endphp
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
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-[160px]">
                                        {{ Str::limit($customer->notes, 40) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="space-y-1 text-xs">
                                            @foreach($dueTypes as $dt)
                                                @php $lastField = $lastDateFields[$dt] ?? null; @endphp
                                                @if($lastField && $customer->{$lastField})
                                                    <span class="text-green-600 font-medium block">
                                                        {{ $typeLabels[$dt] }}: ✓ {{ $customer->{$lastField}->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-yellow-600 block">{{ $typeLabels[$dt] ?? $dt }}: Belum di-follow-up</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <div class="flex flex-col gap-1 items-center">
                                            @foreach($dueTypes as $dt)
                                                <form action="{{ route('pending-customers.update-followup-checkpoint', $customer) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{ $dt }}">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-semibold transition">
                                                        ✓ Catat {{ $typeLabels[$dt] ?? $dt }}
                                                    </button>
                                                </form>
                                            @endforeach
                                            <a href="{{ route('pending-customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900 text-xs font-medium mt-1">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada follow-up yang jatuh tempo pada tanggal ini.
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
