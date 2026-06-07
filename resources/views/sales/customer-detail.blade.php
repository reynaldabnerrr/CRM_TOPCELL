<x-app-layout>
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('sales.dashboard') }}" class="text-blue-600 hover:underline flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Customer Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-8 text-white mb-8">
            <h1 class="text-4xl font-bold mb-2">{{ $customer['name'] }}</h1>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div>
                    <p class="text-blue-200 text-sm">No. Telpon</p>
                    <p class="text-xl font-semibold">{{ $customer['phone_number'] }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-sm">Total Pembelian</p>
                    <p class="text-xl font-semibold">{{ $customer['total_purchases'] }} x</p>
                </div>
                <div>
                    <p class="text-blue-200 text-sm">Pembelian Pertama</p>
                    <p class="text-xl font-semibold">{{ $customer['first_purchase']->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-sm">Pembelian Terakhir</p>
                    <p class="text-xl font-semibold">{{ $customer['last_purchase']->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Followup Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Followup h+1 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Aftercare h+1</h3>
                    <div class="text-2xl">📅</div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-bold text-yellow-600">{{ $followupSummary['h1']['pending'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Done</span>
                        <span class="font-bold text-green-600">{{ $followupSummary['h1']['done'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Skipped</span>
                        <span class="font-bold text-gray-600">{{ $followupSummary['h1']['skipped'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Followup h+7 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Followup h+7</h3>
                    <div class="text-2xl">📈</div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-bold text-orange-600">{{ $followupSummary['h7']['pending'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Done</span>
                        <span class="font-bold text-green-600">{{ $followupSummary['h7']['done'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Skipped</span>
                        <span class="font-bold text-gray-600">{{ $followupSummary['h7']['skipped'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Followup h+1bulan -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Followup h+1bulan</h3>
                    <div class="text-2xl">🚀</div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-bold text-red-600">{{ $followupSummary['1month']['pending'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Done</span>
                        <span class="font-bold text-green-600">{{ $followupSummary['1month']['done'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Skipped</span>
                        <span class="font-bold text-gray-600">{{ $followupSummary['1month']['skipped'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Riwayat Pembelian ({{ $purchases->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">No. Faktur</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Unit/Dept</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Sales Person</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Followup Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($purchases as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $sale->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ $sale->unit_name }}<br>
                                <span class="text-gray-600">{{ $sale->department }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $sale->sales_person ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold 
                                    @if($sale->status === 'Lunas') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $sale->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        @if($sale->followup_h1_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($sale->followup_h1_status === 'done') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        h+1: {{ substr($sale->followup_h1_status, 0, 1) }}
                                    </span>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        @if($sale->followup_h7_status === 'pending') bg-orange-100 text-orange-800
                                        @elseif($sale->followup_h7_status === 'done') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        h+7: {{ substr($sale->followup_h7_status, 0, 1) }}
                                    </span>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        @if($sale->followup_1month_status === 'pending') bg-red-100 text-red-800
                                        @elseif($sale->followup_1month_status === 'done') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        1m: {{ substr($sale->followup_1month_status, 0, 1) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button class="text-blue-600 hover:underline" onclick="openFollowupModal({{ $sale->id }})">
                                    Update
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pembelian</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Followup Update Modal -->
<div id="followupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Update Followup Status</h3>
        
        <form id="followupForm" method="POST" onsubmit="submitFollowupForm(event)">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Tipe Followup</label>
                <select name="followup_type" id="followup_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Tipe</option>
                    <option value="h1">Aftercare h+1</option>
                    <option value="h7">Followup h+7</option>
                    <option value="1month">Followup h+1bulan</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Status</option>
                    <option value="done">Done</option>
                    <option value="pending">Pending</option>
                    <option value="skipped">Skipped</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
                <textarea name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeFollowupModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSaleId = null;

function openFollowupModal(saleId) {
    currentSaleId = saleId;
    document.getElementById('followupModal').classList.remove('hidden');
}

function closeFollowupModal() {
    document.getElementById('followupModal').classList.add('hidden');
    currentSaleId = null;
}

function submitFollowupForm(event) {
    event.preventDefault();
    const form = document.getElementById('followupForm');
    const formData = new FormData(form);
    
    fetch(`{{ url('/sales') }}/${currentSaleId}/update-followup`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.text())
    .then(html => {
        closeFollowupModal();
        location.reload();
    })
    .catch(error => console.error('Error:', error));
}

// Close modal when clicking outside
document.getElementById('followupModal').addEventListener('click', function(e) {
    if (e.target === this) closeFollowupModal();
});
</script>
</x-app-layout>
