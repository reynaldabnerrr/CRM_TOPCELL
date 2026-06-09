<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Account Management') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Create Account Form -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Buat Akun Baru</h3>
                <form method="POST" action="{{ route('account-management.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required minlength="6"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-6 pt-5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_access_followup" value="1" checked
                                    class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Akses Follow-up</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_access_aftercare" value="1" checked
                                    class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Akses Aftercare</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            Buat Akun
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Akun</h3>
                </div>

                @if ($users->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-500">
                        Belum ada akun yang dibuat.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Akses Follow-up</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Akses Aftercare</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>

                                        <!-- Toggle Follow-up -->
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                onclick="toggleAccess({{ $user->id }}, 'followup', this)"
                                                data-active="{{ $user->can_access_followup ? 'true' : 'false' }}"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                                    {{ $user->can_access_followup ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                                <span
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                                        {{ $user->can_access_followup ? 'translate-x-6' : 'translate-x-1' }}">
                                                </span>
                                            </button>
                                        </td>

                                        <!-- Toggle Aftercare -->
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                onclick="toggleAccess({{ $user->id }}, 'aftercare', this)"
                                                data-active="{{ $user->can_access_aftercare ? 'true' : 'false' }}"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                                    {{ $user->can_access_aftercare ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                                <span
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                                        {{ $user->can_access_aftercare ? 'translate-x-6' : 'translate-x-1' }}">
                                                </span>
                                            </button>
                                        </td>

                                        <!-- Delete -->
                                        <td class="px-6 py-4 text-center">
                                            <form method="POST" action="{{ route('account-management.destroy', $user) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-100 text-red-600 text-xs font-medium rounded-lg hover:bg-red-200 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleAccess(userId, type, btn) {
            const url = `/account-management/${userId}/toggle-${type}`;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const isActive = data.value;
                    btn.dataset.active = isActive ? 'true' : 'false';

                    if (isActive) {
                        btn.classList.remove('bg-gray-300');
                        btn.classList.add('bg-indigo-600');
                        btn.querySelector('span').classList.remove('translate-x-1');
                        btn.querySelector('span').classList.add('translate-x-6');
                    } else {
                        btn.classList.remove('bg-indigo-600');
                        btn.classList.add('bg-gray-300');
                        btn.querySelector('span').classList.remove('translate-x-6');
                        btn.querySelector('span').classList.add('translate-x-1');
                    }
                } else {
                    alert('Gagal mengubah akses.');
                }
            })
            .catch(() => alert('Terjadi kesalahan.'));
        }
    </script>
</x-app-layout>
