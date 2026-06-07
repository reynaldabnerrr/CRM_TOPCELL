<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('aftercare.index') }}" class="text-blue-600 hover:text-blue-900">← Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Aftercare') }}
            </h2>
        </div>
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

                    <!-- Display Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Jenis Aftercare</p>
                        <p class="font-semibold text-lg">{{ $aftercare->type }}</p>
                        <p class="text-sm text-gray-600 mt-2">Customer</p>
                        <p class="font-semibold">{{ $aftercare->customer_name }} ({{ $aftercare->phone_number }})</p>
                        <p class="text-sm text-gray-600 mt-2">Terjadwal</p>
                        <p class="font-semibold">{{ $aftercare->scheduled_date->format('d M Y') }}</p>
                    </div>

                    <form action="{{ route('aftercare.update', $aftercare) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="pending" {{ old('status', $aftercare->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $aftercare->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="skipped" {{ old('status', $aftercare->status) === 'skipped' ? 'selected' : '' }}>Skip</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan / Hasil Followup
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('notes', $aftercare->notes) }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">
                                Contoh: "Customer tidak ada, akan difollow up besok" atau "Customer interested, akan arrange meeting minggu depan"
                            </p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('aftercare.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                                Batal
                            </a>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
