<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Template WhatsApp Qontak') }}
            </h2>
            <a href="{{ route('qontak-settings.edit') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                Kembali ke Pengaturan
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Template Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">
                        Berikut adalah daftar template WhatsApp yang terdaftar dan disetujui di dashboard Mekari Qontak Anda. 
                        Salin **ID Template (UUID)** dan tempelkan ke kolom template yang sesuai di halaman Pengaturan Qontak.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-600">Nama Template</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-600">Bahasa</th>
                                    <th class="px-6 py-3 text-center font-semibold text-gray-600">Status</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-600">UUID / ID Template</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($templates as $t)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $t['name'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 mt-1 max-w-md font-mono whitespace-pre-line">{{ $t['body'] ?? $t['message'] ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ strtoupper($t['language'] ?? 'id') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $status = strtolower($t['status'] ?? 'approved');
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold 
                                                {{ $status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ strtoupper($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <input type="text" readonly value="{{ $t['id'] ?? '' }}" 
                                                    id="template-id-{{ $loop->index }}"
                                                    class="px-2 py-1 text-xs border border-gray-300 rounded bg-gray-50 font-mono select-all w-72">
                                                <button onclick="copyToClipboard('{{ $t['id'] ?? '' }}', this)" 
                                                    class="px-2 py-1 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 text-xs font-semibold rounded transition">
                                                    Salin
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada template yang ditemukan atau Access Token tidak valid.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(function() {
                const originalText = button.innerText;
                button.innerText = 'Tersalin!';
                button.classList.remove('bg-indigo-100', 'text-indigo-700');
                button.classList.add('bg-green-600', 'text-white');
                setTimeout(function() {
                    button.innerText = originalText;
                    button.classList.remove('bg-green-600', 'text-white');
                    button.classList.add('bg-indigo-100', 'text-indigo-700');
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</x-app-layout>
