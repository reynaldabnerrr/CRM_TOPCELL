<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pengaturan Mekari Qontak WA') }}
            </h2>
            @if ($settings->access_token)
                <a href="{{ route('qontak-settings.templates') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold transition">
                    📋 Lihat Daftar Template
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success / Error Alert -->
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <form method="POST" action="{{ route('qontak-settings.update') }}" class="space-y-8">
                        @csrf
                        @method('PATCH')

                        <script>
                            window.qontakTemplates = @json($templates);
                            function getQontakTemplate(id) {
                                if (!window.qontakTemplates) return null;
                                return window.qontakTemplates.find(function(t) {
                                    return t.id === id;
                                });
                            }
                        </script>


                        <!-- API Credentials Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">
                                🔑 Kredensial & API Qontak
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="base_url" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Base URL API Qontak
                                    </label>
                                    <input type="text" name="base_url" id="base_url" 
                                        value="{{ old('base_url', $settings->base_url) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" 
                                        required>
                                    <p class="text-xs text-gray-400 mt-1">Default: https://service-chat.qontak.com</p>
                                </div>

                                <div>
                                    <label for="channel_integration_id" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Channel Integration ID
                                    </label>
                                    <input type="text" name="channel_integration_id" id="channel_integration_id" 
                                        value="{{ old('channel_integration_id', $settings->channel_integration_id) }}"
                                        placeholder="Masukkan ID Integrasi WhatsApp Saluran"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Ditemukan di dashboard Qontak (Settings > Integrations > Channel Integration ID)</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="access_token" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Access Token
                                    </label>
                                    <textarea name="access_token" id="access_token" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono"
                                        placeholder="Masukkan token akses saat ini">{{ old('access_token', $settings->access_token) }}</textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="refresh_token" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Refresh Token
                                    </label>
                                    <textarea name="refresh_token" id="refresh_token" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono"
                                        placeholder="Masukkan token refresh">{{ old('refresh_token', $settings->refresh_token) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Templates Section (Sales Aftercare) -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">
                                📦 WhatsApp Template - Follow-up Penjualan (Sales Aftercare)
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div x-data="{ 
                                    selectedId: '{{ old('sales_template_h1', $settings->sales_template_h1) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="sales_template_h1" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template H+1
                                    </label>
                                    @if(!empty($templates))
                                        <select name="sales_template_h1" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="sales_template_h1" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template H+1"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['sales_template_h1']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['sales_template_h1']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->sales_template_h1_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->sales_template_h1_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->sales_template_h1_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['sales_template_h1'][$i]['source'] ?? ($i == 1 ? 'customer_name' : ($i == 2 ? 'item_name' : 'invoice_date')) }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[sales_template_h1][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Customer</option>
                                                        <option value="item_name">Nama Barang</option>
                                                        <option value="invoice_date">Tanggal Invoice</option>
                                                        <option value="notes">Catatan Penjualan</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[sales_template_h1][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['sales_template_h1'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <div x-data="{ 
                                    selectedId: '{{ old('sales_template_h7', $settings->sales_template_h7) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="sales_template_h7" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template H+7
                                    </label>
                                    @if(!empty($templates))
                                        <select name="sales_template_h7" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="sales_template_h7" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template H+7"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['sales_template_h7']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['sales_template_h7']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->sales_template_h7_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->sales_template_h7_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->sales_template_h7_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['sales_template_h7'][$i]['source'] ?? ($i == 1 ? 'customer_name' : ($i == 2 ? 'item_name' : 'invoice_date')) }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[sales_template_h7][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Customer</option>
                                                        <option value="item_name">Nama Barang</option>
                                                        <option value="invoice_date">Tanggal Invoice</option>
                                                        <option value="notes">Catatan Penjualan</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[sales_template_h7][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['sales_template_h7'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <div x-data="{ 
                                    selectedId: '{{ old('sales_template_1month', $settings->sales_template_1month) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="sales_template_1month" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template 1 Bulan
                                    </label>
                                    @if(!empty($templates))
                                        <select name="sales_template_1month" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="sales_template_1month" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template 1 Bulan"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['sales_template_1month']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['sales_template_1month']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->sales_template_1month_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->sales_template_1month_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->sales_template_1month_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['sales_template_1month'][$i]['source'] ?? ($i == 1 ? 'customer_name' : ($i == 2 ? 'item_name' : 'invoice_date')) }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[sales_template_1month][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Customer</option>
                                                        <option value="item_name">Nama Barang</option>
                                                        <option value="invoice_date">Tanggal Invoice</option>
                                                        <option value="notes">Catatan Penjualan</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[sales_template_1month][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['sales_template_1month'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Templates Section (Pending Customer / Prospek) -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">
                                👥 WhatsApp Template - Prospek (Calon Customer)
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div x-data="{ 
                                    selectedId: '{{ old('pending_template_h1', $settings->pending_template_h1) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="pending_template_h1" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template H+1
                                    </label>
                                    @if(!empty($templates))
                                        <select name="pending_template_h1" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="pending_template_h1" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template H+1"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['pending_template_h1']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['pending_template_h1']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->pending_template_h1_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->pending_template_h1_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->pending_template_h1_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['pending_template_h1'][$i]['source'] ?? ($i == 1 ? 'customer_name' : 'notes') }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[pending_template_h1][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Calon Customer</option>
                                                        <option value="invoice_date">Tanggal Pendaftaran Calon Customer</option>
                                                        <option value="notes">Catatan Calon Customer</option>
                                                        <option value="status">Status Calon Customer</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[pending_template_h1][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['pending_template_h1'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <div x-data="{ 
                                    selectedId: '{{ old('pending_template_h7', $settings->pending_template_h7) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="pending_template_h7" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template H+7
                                    </label>
                                    @if(!empty($templates))
                                        <select name="pending_template_h7" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="pending_template_h7" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template H+7"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['pending_template_h7']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['pending_template_h7']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->pending_template_h7_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->pending_template_h7_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->pending_template_h7_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['pending_template_h7'][$i]['source'] ?? ($i == 1 ? 'customer_name' : 'notes') }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[pending_template_h7][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Calon Customer</option>
                                                        <option value="invoice_date">Tanggal Pendaftaran Calon Customer</option>
                                                        <option value="notes">Catatan Calon Customer</option>
                                                        <option value="status">Status Calon Customer</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[pending_template_h7][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['pending_template_h7'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <div x-data="{ 
                                    selectedId: '{{ old('pending_template_1month', $settings->pending_template_1month) }}',
                                    get selectedTemplate() {
                                        return getQontakTemplate(this.selectedId);
                                    }
                                }">
                                    <label for="pending_template_1month" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Template 1 Bulan
                                    </label>
                                    @if(!empty($templates))
                                        <select name="pending_template_1month" x-model="selectedId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2 bg-white">
                                            <option value="">-- Pilih Template WhatsApp --</option>
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['id'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="pending_template_1month" x-model="selectedId" 
                                            placeholder="Masukkan UUID Template 1 Bulan"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-2">
                                    @endif

                                    <div class="mb-3 bg-indigo-50/40 p-2.5 rounded-lg border border-indigo-100/50" x-show="selectedTemplate" style="display: none;">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all" x-text="selectedTemplate ? selectedTemplate.body : ''"></p>
                                    </div>

                                    <div class="mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50" x-show="!selectedTemplate && '{{ isset($settings->variable_mappings['pending_template_1month']['body']) }}'">
                                        <span class="block text-[10px] font-bold text-indigo-800 mb-1 uppercase tracking-wider">📝 Pratinjau Pesan:</span>
                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed font-mono select-all">{{ $settings->variable_mappings['pending_template_1month']['body'] ?? '' }}</p>
                                    </div>
                                    
                                    @if($settings->pending_template_1month_vars)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 mb-3">
                                            <span>Variabel Terdeteksi:</span>
                                            <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">{{ $settings->pending_template_1month_vars }} Variabel</span>
                                        </div>

                                        <div class="space-y-3 bg-gray-50/50 p-3 rounded-lg border border-dashed">
                                            <h4 class="text-xs font-bold text-gray-700">Pemetaan Variabel:</h4>
                                            @for($i = 1; $i <= $settings->pending_template_1month_vars; $i++)
                                                <div x-data="{ source: '{{ $settings->variable_mappings['pending_template_1month'][$i]['source'] ?? ($i == 1 ? 'customer_name' : 'notes') }}' }">
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">
                                                        Variabel {{ $i }}
                                                    </label>
                                                    <select name="variable_mappings[pending_template_1month][{{ $i }}][source]" x-model="source" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-white mb-1">
                                                        <option value="customer_name">Nama Calon Customer</option>
                                                        <option value="invoice_date">Tanggal Pendaftaran Calon Customer</option>
                                                        <option value="notes">Catatan Calon Customer</option>
                                                        <option value="status">Status Calon Customer</option>
                                                        <option value="custom">Teks Kustom...</option>
                                                    </select>
                                                    <input type="text" x-show="source === 'custom'" name="variable_mappings[pending_template_1month][{{ $i }}][custom_value]" value="{{ $settings->variable_mappings['pending_template_1month'][$i]['custom_value'] ?? '' }}" placeholder="Ketik teks kustom..." class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 border-t pt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                💾 Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Manual Refresh Action Card -->
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Uji Coba Penyegaran Token Manual</h4>
                            <p class="text-xs text-gray-500 mt-1">Gunakan tombol ini untuk mengetes apakah Refresh Token valid dan dapat menghasilkan Access Token baru dari Qontak API secara manual.</p>
                        </div>
                        <form method="POST" action="{{ route('qontak-settings.test-refresh') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                🔄 Refresh Token Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
