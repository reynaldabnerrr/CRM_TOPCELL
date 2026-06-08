<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('pending-customers.index') }}" class="text-blue-600 hover:text-blue-900">← Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Calon Customer') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-2xl mx-auto px-3 sm:px-0">
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

                    <form action="{{ route('pending-customers.update', $pendingCustomer) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Customer <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $pendingCustomer->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                No. HP/Telepon <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="phone_number" 
                                name="phone_number" 
                                value="{{ old('phone_number', $pendingCustomer->phone_number) }}"
                                placeholder="6285xxxxxxxxx"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select 
                                    id="status_id" 
                                    name="status_id"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    {{ old('create_new_status') ? 'disabled' : '' }}
                                >
                                    @foreach ($statuses as $s)
                                        <option value="{{ $s->id }}" {{ old('status_id', $pendingCustomer->status_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="flex items-center px-3 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input 
                                        type="checkbox" 
                                        id="create_new_status" 
                                        name="create_new_status"
                                        class="rounded"
                                        onchange="toggleStatusInput()"
                                        {{ old('create_new_status') ? 'checked' : '' }}
                                    >
                                    <span class="ml-2 text-sm font-medium text-gray-700">Baru</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6" id="new_status_field" style="display: {{ old('create_new_status') ? 'block' : 'none' }};">
                            <label for="new_status_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Status Baru <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="new_status_name" 
                                name="new_status_name" 
                                value="{{ old('new_status_name') }}"
                                placeholder="Misal: Menunggu Konfirmasi"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <div class="mb-6">
                            <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Masuk <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="entry_date" 
                                name="entry_date" 
                                value="{{ old('entry_date', $pendingCustomer->entry_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('notes', $pendingCustomer->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('pending-customers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
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

    <script>
    function toggleStatusInput() {
        const checkbox = document.getElementById('create_new_status');
        const statusSelect = document.getElementById('status_id');
        const newStatusField = document.getElementById('new_status_field');
        const newStatusInput = document.getElementById('new_status_name');
        
        if (checkbox.checked) {
            statusSelect.disabled = true;
            statusSelect.value = '';
            newStatusField.style.display = 'block';
            newStatusInput.required = true;
        } else {
            statusSelect.disabled = false;
            newStatusField.style.display = 'none';
            newStatusInput.required = false;
            newStatusInput.value = '';
        }
    }
    </script>
</x-app-layout>
