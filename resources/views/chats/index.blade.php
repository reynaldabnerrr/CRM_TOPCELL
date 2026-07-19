<x-app-layout>
    <div class="-m-6">
        <style>
            /* Prevent scrolling on the entire page/body for the chat page only */
            html, body {
                overflow: hidden !important;
                height: 100% !important;
                position: fixed !important;
                width: 100% !important;
            }
            
            /* Make the sidebar container non-scrollable and fit the viewport */
            div.md\:ml-64 {
                height: 100dvh !important;
                min-height: 100dvh !important;
                max-height: 100dvh !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
            }

            /* Target the main slot wrapper */
            main.p-6 {
                padding: 0 !important;
                margin: 0 !important;
                flex: 1 !important;
                height: 100% !important;
                min-height: 0 !important;
                overflow: hidden !important;
            }

            /* Reset the negative margin wrapper */
            .-m-6 {
                margin: 0 !important;
                height: 100% !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
            }

            /* Premium custom scrollbar styling */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1; /* slate-300 */
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8; /* slate-400 */
            }

            /* WhatsApp-like subtle background pattern */
            .chat-bg-pattern {
                background-color: #f8fafc; /* slate-50 */
                background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
                background-size: 20px 20px;
                position: relative;
            }
        </style>

        <div x-data="chatSystem({{ json_encode($chats) }}, {{ json_encode($statuses) }})" class="bg-white flex flex-col lg:flex-row w-full h-full min-h-0 overflow-hidden">
            
            <!-- LEFT COLUMN: Room list -->
            <div :class="showConversationOnMobile ? 'hidden lg:flex' : 'flex'" class="w-full lg:w-80 xl:w-96 border-r border-slate-100 flex-col flex-shrink-0 bg-slate-50/50 h-full max-h-full overflow-hidden">
                <!-- Sidebar Header -->
                <div class="px-5 py-4 border-b border-slate-100 bg-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        <h2 class="text-base font-bold text-slate-800 tracking-tight">WhatsApp Live Chat</h2>
                    </div>
                    <span class="text-xs bg-indigo-50 text-indigo-600 font-semibold px-2 py-0.5 rounded-full" x-text="rooms.length + ' Chat'"></span>
                </div>

                <!-- Search input -->
                <div class="p-4 border-b border-slate-100 bg-white">
                    <label for="search" class="sr-only">Cari customer</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            id="search"
                            type="text"
                            x-model="searchQuery"
                            placeholder="Cari nama atau nomor HP..."
                            class="w-full pl-10 pr-9 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all placeholder:text-slate-400 text-slate-700"
                        />
                        <!-- Clear search query button -->
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Room items scroll area -->
                <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                    <template x-if="filteredRooms.length === 0">
                        <div class="text-center py-8 px-4 text-slate-400 text-sm">
                            <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Tidak ada obrolan ditemukan
                        </div>
                    </template>

                    <template x-for="room in filteredRooms" :key="room.id">
                        <button
                            @click="selectRoom(room)"
                            :class="activeRoom && activeRoom.id === room.id 
                                ? 'bg-indigo-50/60 text-slate-800 font-medium' 
                                : 'hover:bg-slate-100/50 text-slate-700'"
                            class="w-full text-left px-4 py-3.5 rounded-2xl flex items-start space-x-3 transition-all duration-200 relative group"
                        >
                            <!-- Left Active Indicator Bar (only shown when active) -->
                            <div x-show="activeRoom && activeRoom.id === room.id" class="absolute left-0 top-3.5 bottom-3.5 w-1 bg-indigo-600 rounded-r-md"></div>

                            <!-- Avatar -->
                            <div :class="activeRoom && activeRoom.id === room.id ? 'bg-indigo-600 text-white ring-4 ring-indigo-50' : 'bg-indigo-100 text-indigo-600'" 
                                 class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-base transition-all duration-200">
                                <span x-text="room.customer_name.charAt(0).toUpperCase()"></span>
                            </div>

                            <!-- Text information -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="text-sm font-bold truncate text-slate-800" x-text="room.customer_name"></h4>
                                    <span class="text-[10px] font-medium flex-shrink-0 ml-2 text-slate-400" x-text="formatTime(room.last_message_time)"></span>
                                </div>
                                <p class="text-xs font-semibold text-indigo-600/80 mt-0.5" x-text="room.phone_number"></p>
                                <p class="text-xs text-slate-400 truncate mt-1 flex items-center" :class="activeRoom && activeRoom.id === room.id ? 'text-slate-500 font-medium' : ''">
                                    <template x-if="room.last_message && room.last_message.includes('[Gambar]')">
                                        <svg class="h-3.5 w-3.5 text-slate-400 mr-1 inline-block flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                    <span x-text="room.last_message || 'Belum ada pesan'"></span>
                                </p>
                            </div>

                            <!-- Badge unread -->
                            <template x-if="room.unread_count > 0">
                                <span class="flex-shrink-0 bg-red-500 text-white min-w-[20px] h-5 px-1.5 rounded-full flex items-center justify-center text-[10px] font-bold animate-pulse shadow-sm shadow-red-200 ml-1.5 align-self-center mt-1" x-text="room.unread_count"></span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            <!-- RIGHT COLUMN: Active conversation -->
            <div :class="showConversationOnMobile ? 'flex' : 'hidden lg:flex'" class="flex-1 flex-col min-w-0 bg-white relative h-full max-h-full overflow-hidden">
                
                <!-- Top Info Header -->
                <template x-if="activeRoom">
                    <div class="px-5 py-4 border-b border-slate-100 bg-white flex-shrink-0 z-10 flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <!-- Back button for mobile/tablet -->
                            <button @click="showConversationOnMobile = false" class="lg:hidden flex-shrink-0 flex items-center justify-center text-slate-500 hover:text-slate-700 p-2 rounded-xl bg-slate-100 hover:bg-slate-200 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Avatar -->
                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base flex-shrink-0 border border-indigo-100 shadow-sm">
                                <span x-text="activeRoom.customer_name.charAt(0).toUpperCase()"></span>
                            </div>

                            <!-- Name & Phone -->
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm sm:text-base font-bold text-slate-800 truncate" x-text="activeRoom.customer_name"></h3>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-xs text-indigo-600 font-semibold flex-shrink-0" x-text="activeRoom.phone_number"></span>
                                    <span class="hidden sm:inline text-slate-300 flex-shrink-0">•</span>
                                    <span class="hidden sm:inline-flex text-[10px] text-emerald-600 bg-emerald-50 border border-emerald-100/50 font-bold px-2 py-0.5 rounded-full items-center whitespace-nowrap flex-shrink-0">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                                        Sesi Aktif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons (icon-only below xl screen size) -->
                        <div class="flex items-center space-x-2 flex-shrink-0 ml-4">
                            <!-- Add to Calon Customer -->
                            <button 
                                @click="openLeadModal()" 
                                :disabled="addingLead"
                                title="Tambah Calon Customer"
                                class="inline-flex items-center justify-center h-10 w-10 xl:w-auto xl:px-4 bg-indigo-50 hover:bg-indigo-100 disabled:opacity-50 border border-indigo-100/60 rounded-xl text-xs font-bold text-indigo-700 transition active:scale-95 shadow-sm"
                            >
                                <svg class="h-5 w-5 xl:mr-1.5 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                <span class="hidden xl:inline" x-text="addingLead ? 'Memproses...' : 'Tambah Calon Customer'"></span>
                            </button>
                            
                            <!-- Delete Chat -->
                            <button 
                                @click="deleteChat()" 
                                :disabled="deletingChat"
                                title="Hapus Chat"
                                class="inline-flex items-center justify-center h-10 w-10 xl:w-auto xl:px-4 bg-red-50 hover:bg-red-100 disabled:opacity-50 border border-red-100/60 rounded-xl text-xs font-bold text-red-700 transition active:scale-95 shadow-sm"
                            >
                                <svg class="h-5 w-5 xl:mr-1.5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="hidden xl:inline" x-text="deletingChat ? 'Menghapus...' : 'Hapus Chat'"></span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Message Body (Scroll area) -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 chat-bg-pattern custom-scrollbar" x-ref="messageContainer">
                    
                    <!-- Empty State (No Active Room) -->
                    <template x-if="!activeRoom">
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 bg-slate-50/30">
                            <div class="w-20 h-20 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-500 mb-6 shadow-sm border border-indigo-100/50">
                                <svg class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-800 tracking-tight">WhatsApp Live Chat CRM</h3>
                            <p class="text-sm text-slate-500 max-w-sm mt-2.5 leading-relaxed">Pilih salah satu obrolan customer dari menu sebelah kiri untuk melihat riwayat pesan dan membalas secara langsung.</p>
                            
                            <div class="mt-8 flex items-center space-x-2 bg-emerald-50 border border-emerald-100 rounded-full px-3.5 py-1.5 text-xs text-emerald-700 font-semibold shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Siap Menerima Pesan Baru</span>
                            </div>
                        </div>
                    </template>

                    <!-- Active Room Messages -->
                    <template x-if="activeRoom">
                        <div class="space-y-4">
                            
                            <!-- Loading Spinner inside Chat -->
                            <template x-if="loadingMessages">
                                <div class="flex items-center justify-center py-10">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                </div>
                            </template>

                            <!-- Message History -->
                            <template x-if="!loadingMessages">
                                <div class="space-y-4">
                                    
                                    <!-- Welcome info message -->
                                    <div class="flex justify-center my-2.5">
                                        <span class="bg-indigo-50 border border-indigo-100/60 text-indigo-600/90 text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">
                                            Awal obrolan dengan customer dimulai
                                        </span>
                                    </div>

                                    <template x-for="msg in messages" :key="msg.id">
                                        <div class="flex flex-col" :class="msg.sender_type === 'agent' ? 'items-end' : 'items-start'">
                                            <!-- Message Bubble -->
                                            <div :class="msg.sender_type === 'agent' 
                                                    ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl rounded-tr-none shadow-sm' 
                                                    : 'bg-white text-slate-800 border border-slate-100 rounded-2xl rounded-tl-none shadow-sm'" 
                                                 class="max-w-[70%] px-3.5 py-2.5 text-sm leading-relaxed relative animate-fadeIn"
                                            >
                                                <!-- Text Message -->
                                                <template x-if="msg.message_type === 'text'">
                                                    <span class="whitespace-pre-wrap select-text text-sm" x-html="formatMessageContent(msg.message_content)"></span>
                                                </template>
                                                
                                                <!-- Image Message -->
                                                <template x-if="msg.message_type === 'image'">
                                                    <div class="space-y-1">
                                                        <img :src="msg.message_content" class="max-w-full max-h-64 rounded-xl cursor-zoom-in border border-slate-100 object-cover shadow-sm transition hover:brightness-95" @click="window.open(msg.message_content, '_blank')">
                                                    </div>
                                                </template>

                                                <!-- Other Attachment/File Type -->
                                                <template x-if="msg.message_type !== 'text' && msg.message_type !== 'image'">
                                                    <a :href="msg.message_content" target="_blank" class="flex items-center space-x-2 font-semibold hover:underline" :class="msg.sender_type === 'agent' ? 'text-indigo-100' : 'text-indigo-600'">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span x-text="'File ' + msg.message_type"></span>
                                                    </a>
                                                </template>

                                                <!-- Nested Metadata Inside Bubble (Bottom Right) -->
                                                <div class="flex items-center justify-end space-x-1 mt-1.5 text-[9px] select-none" :class="msg.sender_type === 'agent' ? 'text-indigo-200' : 'text-slate-400'">
                                                    <template x-if="msg.sender_type === 'agent'">
                                                        <span class="font-bold mr-1" x-text="msg.sender_name"></span>
                                                    </template>
                                                    <span x-text="formatTime(msg.created_at)"></span>
                                                    <template x-if="msg.sender_type === 'agent'">
                                                        <svg class="h-3.5 w-3.5 text-indigo-200 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M17 5L9.5 12.5L6 9" />
                                                            <path d="M22 5L14.5 12.5L13 11" />
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Error Message Floating in Chat -->
                                    <template x-if="errorMessage">
                                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs flex items-center space-x-2 mt-2 animate-pulse">
                                            <svg class="h-4 w-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span x-text="errorMessage"></span>
                                        </div>
                                    </template>

                                </div>
                            </template>
                        </div>
                    </template>

                </div>

                <!-- Input Bar (Fixed Bottom) -->
                <template x-if="activeRoom">
                    <div class="p-3 border-t border-slate-100 bg-white flex-shrink-0 z-10 shadow-[0_-4px_12px_rgba(0,0,0,0.015)]">
                        <!-- Hidden inputs for media upload and camera capture -->
                        <input type="file" id="chat-file-input" @change="triggerFileUpload" accept="image/*" class="hidden">
                        <input type="file" id="chat-camera-input" @change="triggerFileUpload" accept="image/*" capture="environment" class="hidden">
                        
                        <form @submit.prevent="sendReply" class="flex items-center space-x-2">
                            <!-- Unified Input Wrapper -->
                            <div class="flex-1 flex items-center bg-slate-50 border border-slate-200/80 rounded-2xl p-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all">
                                <!-- Attachment Buttons -->
                                <div class="flex items-center space-x-1 pl-1">
                                    <button
                                        type="button"
                                        @click="document.getElementById('chat-file-input').click()"
                                        :disabled="sending"
                                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/55 rounded-xl transition flex-shrink-0 active:scale-95 disabled:opacity-50"
                                        title="Kirim Gambar"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        @click="document.getElementById('chat-camera-input').click()"
                                        :disabled="sending"
                                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/55 rounded-xl transition flex-shrink-0 active:scale-95 disabled:opacity-50"
                                        title="Buka Kamera"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Text Input Textarea -->
                                <textarea
                                    x-model="newMessage"
                                    @keydown.enter.prevent="if (!sending && newMessage.trim()) sendReply()"
                                    placeholder="Ketik balasan pesan..."
                                    rows="1"
                                    @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                    class="flex-1 py-1.5 px-3 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm resize-none text-slate-700 min-h-[36px] max-h-[120px] custom-scrollbar leading-relaxed"
                                    :disabled="sending"
                                ></textarea>
                            </div>
                            
                            <!-- Send Button -->
                            <button
                                type="submit"
                                :disabled="sending || !newMessage.trim()"
                                :class="sending || !newMessage.trim() 
                                    ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200/50' 
                                    : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-100 active:scale-95'"
                                class="h-[46px] w-[46px] rounded-full flex items-center justify-center transition-all duration-150 flex-shrink-0"
                            >
                                <template x-if="sending">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <template x-if="!sending">
                                    <svg class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </template>
                            </button>
                        </form>
                    </div>
                </template>

            </div>

            <!-- Lead Modal Overlay -->
            <div 
                x-show="showLeadModal" 
                class="fixed inset-0 z-50 overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showLeadModal = false"></div>

                <!-- Modal Content Wrapper -->
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div 
                        x-show="showLeadModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100"
                    >
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center">
                                <svg class="h-4.5 w-4.5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Tambah Calon Customer
                            </h3>
                            <button @click="showLeadModal = false" class="text-slate-400 hover:text-slate-600 transition p-1.5 rounded-lg hover:bg-slate-50">
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form Body -->
                        <div class="p-6 space-y-4">
                            <!-- Info Card -->
                            <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-2xl p-4 flex flex-col gap-1.5 shadow-inner">
                                <div class="flex items-center text-xs">
                                    <span class="w-20 font-semibold text-slate-500">Nama:</span>
                                    <span class="font-bold text-slate-800 truncate" x-text="activeRoom ? activeRoom.customer_name : ''"></span>
                                </div>
                                <div class="flex items-center text-xs">
                                    <span class="w-20 font-semibold text-slate-500">Nomor HP:</span>
                                    <span class="font-mono font-bold text-indigo-700" x-text="activeRoom ? activeRoom.phone_number : ''"></span>
                                </div>
                            </div>

                            <!-- Create New Status Checkbox -->
                            <div class="flex items-center space-x-2 py-1">
                                <input 
                                    type="checkbox" 
                                    id="create_new_status" 
                                    x-model="create_new_status" 
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer"
                                >
                                <label for="create_new_status" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Buat Status Baru</label>
                            </div>

                            <!-- Status Select (Toggled by checkbox) -->
                            <div class="space-y-1.5" x-show="!create_new_status">
                                <label class="block text-xs font-bold text-slate-700">Pilih Status Awal</label>
                                <select 
                                    x-model="leadForm.status_id" 
                                    class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-slate-700 transition"
                                >
                                    <template x-for="status in statuses" :key="status.id">
                                        <option :value="status.id" x-text="status.name" :selected="leadForm.status_id == status.id"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Status Input (Toggled by checkbox) -->
                            <div class="space-y-1.5" x-show="create_new_status" style="display: none;">
                                <label class="block text-xs font-bold text-slate-700">Nama Status Baru</label>
                                <input 
                                    type="text" 
                                    x-model="leadForm.new_status_name" 
                                    placeholder="Ketik nama status baru..."
                                    class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-slate-700 transition"
                                >
                            </div>

                            <!-- Notes Textarea -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Catatan</label>
                                <textarea 
                                    x-model="leadForm.notes" 
                                    rows="3" 
                                    placeholder="Ketik catatan di sini..."
                                    class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none bg-slate-50/50 focus:bg-white text-slate-700 transition"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button 
                                @click="showLeadModal = false" 
                                class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition"
                            >
                                Batal
                            </button>
                            <button 
                                @click="submitLead()" 
                                :disabled="addingLead"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-100 active:scale-95"
                            >
                                <span x-text="addingLead ? 'Menyimpan...' : 'Simpan'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Preview Modal -->
            <div 
                x-show="showUploadModal" 
                class="fixed inset-0 z-50 overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="cancelUpload()"></div>

                <!-- Modal Content Wrapper -->
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div 
                        x-show="showUploadModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100"
                    >
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center">
                                <svg class="h-4.5 w-4.5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Pratinjau Kirim Gambar
                            </h3>
                            <button @click="cancelUpload()" class="text-slate-400 hover:text-slate-600 transition p-1.5 rounded-lg hover:bg-slate-50">
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-4">
                            <!-- Image Box -->
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-2 flex items-center justify-center overflow-hidden max-h-80 shadow-inner">
                                <template x-if="selectedFilePreview">
                                    <img :src="selectedFilePreview" class="max-w-full max-h-72 rounded-xl object-contain shadow-sm">
                                </template>
                            </div>
                            <!-- File Info Card -->
                            <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-xl p-3 flex flex-col gap-1 text-xs shadow-inner">
                                <div class="flex items-center">
                                    <span class="w-20 font-semibold text-slate-500">Nama File:</span>
                                    <span class="font-bold text-slate-800 truncate flex-1" x-text="selectedFile ? selectedFile.name : ''"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-20 font-semibold text-slate-500">Ukuran:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedFile ? (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB' : ''"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button 
                                @click="cancelUpload()" 
                                class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition"
                            >
                                Batal
                            </button>
                            <button 
                                @click="confirmUpload()" 
                                :disabled="sending"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-100 active:scale-95 flex items-center"
                            >
                                <template x-if="sending">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="sending ? 'Mengirim...' : 'Kirim Gambar'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Block for Chat Logic -->
    <script>
        function chatSystem(initialRooms, initialStatuses) {
            return {
                rooms: initialRooms,
                statuses: initialStatuses,
                activeRoom: null,
                messages: [],
                newMessage: '',
                searchQuery: '',
                loadingMessages: false,
                sending: false,
                errorMessage: '',
                pollingInterval: null,
                messagesPollingInterval: null,
                showConversationOnMobile: false,
                addingLead: false,
                deletingChat: false,
                showLeadModal: false,
                create_new_status: false,
                leadForm: {
                    status_id: '',
                    new_status_name: '',
                    notes: 'Ditambahkan langsung dari live chat WhatsApp.'
                },

                // File upload preview modal state
                showUploadModal: false,
                selectedFile: null,
                selectedFilePreview: null,

                init() {
                    // Poll for rooms list updates every 4 seconds
                    this.pollingInterval = setInterval(() => {
                        this.fetchRooms();
                    }, 4000);

                    // Initialize global window state for active room to guide sidebar notifications
                    window.chatSystemData = {
                        activeRoomId: null
                    };

                    // Check if room parameter is present in URL to auto-select
                    const urlParams = new URLSearchParams(window.location.search);
                    const roomId = urlParams.get('room');
                    if (roomId) {
                        const checkAndSelect = () => {
                            const room = this.rooms.find(r => r.room_id === roomId || String(r.id) === roomId);
                            if (room) {
                                this.selectRoom(room);
                            }
                        };
                        setTimeout(checkAndSelect, 500);
                    }
                },

                get filteredRooms() {
                    if (!this.searchQuery) return this.rooms;
                    const q = this.searchQuery.toLowerCase();
                    return this.rooms.filter(room => 
                        room.customer_name.toLowerCase().includes(q) || 
                        room.phone_number.includes(q) ||
                        (room.last_message && room.last_message.toLowerCase().includes(q))
                    );
                },

                async fetchRooms() {
                    try {
                        const res = await fetch('{{ route("chats.rooms") }}');
                        if (res.ok) {
                            const data = await res.json();
                            this.rooms = data;
                            // Update active room data if currently open
                            if (this.activeRoom) {
                                const updatedActive = data.find(r => r.id === this.activeRoom.id);
                                if (updatedActive) {
                                    this.activeRoom.unread_count = updatedActive.unread_count;
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Error fetching rooms:', err);
                    }
                },

                async selectRoom(room) {
                    this.activeRoom = room;
                    this.showConversationOnMobile = true;

                    // Update global active room state
                    if (window.chatSystemData) {
                        window.chatSystemData.activeRoomId = room.id;
                    }
                    this.loadingMessages = true;
                    this.errorMessage = '';
                    this.messages = [];
                    
                    // Clean up previous message polling if any
                    if (this.messagesPollingInterval) {
                        clearInterval(this.messagesPollingInterval);
                    }

                    await this.fetchMessages();
                    this.loadingMessages = false;
                    this.scrollToBottom();
                    setTimeout(() => this.scrollToBottom(), 100);
                    setTimeout(() => this.scrollToBottom(), 300);

                    // Set room unread count to 0 locally
                    const localRoom = this.rooms.find(r => r.id === room.id);
                    if (localRoom) localRoom.unread_count = 0;

                    // Poll for messages in the active room every 3 seconds
                    this.messagesPollingInterval = setInterval(() => {
                        this.fetchMessages(true);
                    }, 3000);
                },

                async fetchMessages(isSilent = false) {
                    if (!this.activeRoom) return;
                    try {
                        const url = '{{ route("chats.messages", ":id") }}'.replace(':id', this.activeRoom.id);
                        const res = await fetch(url);
                        if (res.ok) {
                            const data = await res.json();
                            const oldLength = this.messages.length;
                            this.messages = data.messages;
                            
                            // If room has unread messages, fetch rooms to clear the badge in sidebar/main lists
                            if (data.chat && data.chat.unread_count > 0) {
                                this.fetchRooms();
                            }

                            // Auto scroll to bottom only if there are new messages
                            if (this.messages.length > oldLength) {
                                this.scrollToBottom();
                                setTimeout(() => this.scrollToBottom(), 100);
                            }
                        }
                    } catch (err) {
                        console.error('Error fetching messages:', err);
                    }
                },

                async sendReply() {
                    if (!this.newMessage.trim() || this.sending || !this.activeRoom) return;
                    
                    this.sending = true;
                    this.errorMessage = '';
                    const textToSend = this.newMessage;
                    this.newMessage = ''; // Optimistic clear

                    // Reset textarea height
                    const textarea = document.querySelector('form textarea');
                    if (textarea) textarea.style.height = 'auto';

                    try {
                        const url = '{{ route("chats.send", ":id") }}'.replace(':id', this.activeRoom.id);
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message: textToSend })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.messages.push(data.message);
                            this.scrollToBottom();
                            this.fetchRooms(); // Refresh last message in list
                        } else {
                            this.newMessage = textToSend; // Restore text
                            if (textarea) {
                                textarea.style.height = 'auto';
                                textarea.style.height = textarea.scrollHeight + 'px';
                            }
                            this.errorMessage = data.error || 'Gagal mengirim pesan.';
                            setTimeout(() => { this.errorMessage = ''; }, 5000);
                        }
                    } catch (err) {
                        this.newMessage = textToSend;
                        if (textarea) {
                            textarea.style.height = 'auto';
                            textarea.style.height = textarea.scrollHeight + 'px';
                        }
                        this.errorMessage = 'Terjadi kesalahan jaringan saat mengirim pesan.';
                        setTimeout(() => { this.errorMessage = ''; }, 5000);
                    } finally {
                        this.sending = false;
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                formatTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                },

                formatDateTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString([], { day: '2-digit', month: 'short' }) + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                },

                // Secure WhatsApp markdown parser
                formatMessageContent(content) {
                    if (!content) return '';
                    // Escape HTML first to prevent XSS
                    let escaped = content
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;");
                    
                    // Bold: *text* -> <strong>text</strong>
                    escaped = escaped.replace(/\*([^\*]+)\*/g, '<strong>$1</strong>');
                    
                    // Italic: _text_ -> <em>text</em>
                    escaped = escaped.replace(/_([^_]+)_/g, '<em>$1</em>');
                    
                    // Strikethrough: ~text~ -> <del>text</del>
                    escaped = escaped.replace(/~([^~]+)~/g, '<del>$1</del>');
                    
                    // Code block: `text` -> <code>text</code>
                    escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-red-650 px-1.5 py-0.5 rounded text-xs font-mono">$1</code>');
                    
                    return escaped;
                },

                openLeadModal() {
                    if (!this.activeRoom) return;
                    this.create_new_status = false;
                    this.leadForm.status_id = this.statuses.length > 0 ? this.statuses[0].id : '';
                    this.leadForm.new_status_name = '';
                    this.leadForm.notes = 'Ditambahkan langsung dari live chat WhatsApp.';
                    this.showLeadModal = true;
                },

                async submitLead() {
                    if (this.addingLead || !this.activeRoom) return;
                    if (this.create_new_status && !this.leadForm.new_status_name.trim()) {
                        alert('Silakan masukkan nama status baru.');
                        return;
                    }
                    if (!this.create_new_status && !this.leadForm.status_id) {
                        alert('Silakan pilih status terlebih dahulu.');
                        return;
                    }
                    this.addingLead = true;
                    try {
                        const url = '{{ route("chats.add-to-pending", ":id") }}'.replace(':id', this.activeRoom.id);
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                create_new_status: this.create_new_status,
                                status_id: this.create_new_status ? null : this.leadForm.status_id,
                                new_status_name: this.create_new_status ? this.leadForm.new_status_name : null,
                                notes: this.leadForm.notes
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            alert(data.message);
                            if (data.statuses) {
                                this.statuses = data.statuses;
                            }
                            this.showLeadModal = false;
                        } else {
                            alert(data.error || 'Gagal menambahkan calon customer.');
                        }
                    } catch (err) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.addingLead = false;
                    }
                },

                // File upload preview handlers
                triggerFileUpload(e) {
                    const file = e.target.files[0];
                    if (!file || !this.activeRoom) return;
                    this.selectedFile = file;
                    this.selectedFilePreview = URL.createObjectURL(file);
                    this.showUploadModal = true;
                },

                cancelUpload() {
                    this.showUploadModal = false;
                    this.selectedFile = null;
                    if (this.selectedFilePreview) {
                        URL.revokeObjectURL(this.selectedFilePreview);
                        this.selectedFilePreview = null;
                    }
                    document.getElementById('chat-file-input').value = '';
                    document.getElementById('chat-camera-input').value = '';
                },

                async confirmUpload() {
                    if (!this.selectedFile || !this.activeRoom || this.sending) return;
                    
                    this.sending = true;
                    this.errorMessage = '';
                    this.showUploadModal = false;

                    const formData = new FormData();
                    formData.append('file', this.selectedFile);

                    try {
                        const url = '{{ route("chats.send", ":id") }}'.replace(':id', this.activeRoom.id);
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.messages.push(data.message);
                            this.scrollToBottom();
                            this.fetchRooms();
                        } else {
                            this.errorMessage = data.error || 'Gagal mengirim gambar.';
                            setTimeout(() => { this.errorMessage = ''; }, 5000);
                        }
                    } catch (err) {
                        this.errorMessage = 'Terjadi kesalahan jaringan saat mengirim gambar.';
                        setTimeout(() => { this.errorMessage = ''; }, 5000);
                    } finally {
                        this.sending = false;
                        this.cancelUpload();
                    }
                },

                async deleteChat() {
                    if (this.deletingChat || !this.activeRoom) return;
                    if (!confirm('Apakah Anda yakin ingin menghapus chat ini beserta riwayat pesannya? Tindakan ini tidak dapat dibatalkan.')) return;
                    
                    this.deletingChat = true;
                    try {
                        const url = '{{ route("chats.destroy", ":id") }}'.replace(':id', this.activeRoom.id);
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            alert(data.message);
                            // Remove active room from local rooms list
                            this.rooms = this.rooms.filter(r => r.id !== this.activeRoom.id);
                            this.activeRoom = null;
                            this.showConversationOnMobile = false;
                        } else {
                            alert(data.error || 'Gagal menghapus chat.');
                        }
                    } catch (err) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.deletingChat = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
