<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('WhatsApp Live Chat') }}
            </h2>
            <div class="flex items-center space-x-2 bg-indigo-50 border border-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                <span>Real-time Sync Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="chatSystem({{ json_encode($chats) }})" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden flex flex-col md:flex-row h-[calc(100vh-14rem)] min-h-[500px]">
                
                <!-- LEFT COLUMN: Room list -->
                <div :class="showConversationOnMobile ? 'hidden md:flex' : 'flex'" class="w-full md:w-80 lg:w-96 border-r border-gray-100 flex-col flex-shrink-0 bg-gray-50/50">
                    <!-- Search input -->
                    <div class="p-4 border-b border-gray-100 bg-white">
                        <label for="search" class="sr-only">Cari customer</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input
                                id="search"
                                type="text"
                                x-model="searchQuery"
                                placeholder="Cari nama atau nomor HP..."
                                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all"
                            />
                        </div>
                    </div>

                    <!-- Room items scroll area -->
                    <div class="flex-1 overflow-y-auto p-2 space-y-1">
                        <template x-if="filteredRooms.length === 0">
                            <div class="text-center py-8 px-4 text-gray-400 text-sm">
                                <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Tidak ada obrolan ditemukan
                            </div>
                        </template>

                        <template x-for="room in filteredRooms" :key="room.id">
                            <button
                                @click="selectRoom(room)"
                                :class="activeRoom && activeRoom.id === room.id 
                                    ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md shadow-indigo-100' 
                                    : 'hover:bg-white hover:shadow-sm text-gray-700 bg-transparent'"
                                class="w-full text-left px-4 py-3.5 rounded-xl flex items-start space-x-3 transition-all duration-200"
                            >
                                <!-- Avatar -->
                                <div :class="activeRoom && activeRoom.id === room.id ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-600'" 
                                     class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm">
                                    <span x-text="room.customer_name.charAt(0).toUpperCase()"></span>
                                </div>

                                <!-- Text information -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline">
                                        <h4 class="text-sm font-semibold truncate" :class="activeRoom && activeRoom.id === room.id ? 'text-white' : 'text-gray-900'" x-text="room.customer_name"></h4>
                                        <span class="text-xxs opacity-75 flex-shrink-0 ml-2" :class="activeRoom && activeRoom.id === room.id ? 'text-indigo-200' : 'text-gray-400'" x-text="formatTime(room.last_message_time)"></span>
                                    </div>
                                    <p class="text-xs font-medium truncate mt-0.5" :class="activeRoom && activeRoom.id === room.id ? 'text-indigo-200' : 'text-gray-500'" x-text="room.phone_number"></p>
                                    <p class="text-xs truncate mt-1" :class="activeRoom && activeRoom.id === room.id ? 'text-indigo-100' : 'text-gray-400'" x-text="room.last_message || 'Belum ada pesan'"></p>
                                </div>

                                <!-- Badge unread -->
                                <template x-if="room.unread_count > 0">
                                    <span :class="activeRoom && activeRoom.id === room.id ? 'bg-white text-indigo-700' : 'bg-indigo-600 text-white animate-pulse'" 
                                          class="flex-shrink-0 min-w-5 h-5 px-1.5 rounded-full flex items-center justify-center text-3xs font-extrabold" x-text="room.unread_count"></span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Active conversation -->
                <div :class="showConversationOnMobile ? 'flex' : 'hidden md:flex'" class="flex-1 flex-col min-w-0 bg-white relative">
                    
                    <!-- Top Info Header -->
                    <template x-if="activeRoom">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white flex-shrink-0 z-10">
                            <div class="flex items-center space-x-3 min-w-0">
                                <!-- Back button for mobile -->
                                <button @click="showConversationOnMobile = false" class="md:hidden flex items-center text-gray-500 hover:text-gray-700 mr-2 p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    <span x-text="activeRoom.customer_name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate" x-text="activeRoom.customer_name"></h3>
                                    <div class="flex items-center space-x-2 mt-0.5">
                                        <span class="text-xs text-gray-500 font-medium" x-text="activeRoom.phone_number"></span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-xs text-green-600 font-semibold flex items-center">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 mr-1"></span>
                                            Sesi Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            

                        </div>
                    </template>

                    <!-- Message Body (Scroll area) -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50" x-ref="messageContainer">
                        
                        <!-- Empty State (No Active Room) -->
                        <template x-if="!activeRoom">
                            <div class="h-full flex flex-col items-center justify-center text-center p-8">
                                <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 mb-4 animate-bounce">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Mulai Obrolan WhatsApp</h3>
                                <p class="text-sm text-gray-500 max-w-sm mt-1">Pilih salah satu customer dari daftar di sebelah kiri untuk membaca riwayat obrolan dan membalas pesan.</p>
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
                                        <div class="flex justify-center">
                                            <span class="bg-gray-100 text-gray-500 text-xxs font-medium px-2.5 py-1 rounded-full">
                                                Awal obrolan dengan customer dimulai
                                            </span>
                                        </div>

                                        <template x-for="msg in messages" :key="msg.id">
                                            <div class="flex flex-col" :class="msg.sender_type === 'agent' ? 'items-end' : 'items-start'">
                                                <!-- Message Bubble -->
                                                <div :class="msg.sender_type === 'agent' 
                                                        ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl rounded-tr-sm shadow-sm' 
                                                        : 'bg-white text-gray-800 border border-gray-100 rounded-2xl rounded-tl-sm shadow-sm'" 
                                                     class="max-w-[75%] px-4 py-2.5 text-sm leading-relaxed"
                                                >
                                                    <!-- Message Text -->
                                                    <span class="whitespace-pre-wrap select-text" x-text="msg.message_content"></span>
                                                </div>

                                                <!-- Sender & Time Metadata -->
                                                <div class="flex items-center space-x-1.5 mt-1 px-1 text-xxs text-gray-400" :class="msg.sender_type === 'agent' ? 'flex-row-reverse space-x-reverse' : ''">
                                                    <span class="font-semibold text-gray-500" x-text="msg.sender_type === 'agent' ? msg.sender_name : 'Customer'"></span>
                                                    <span class="text-gray-300">•</span>
                                                    <span x-text="formatDateTime(msg.created_at)"></span>
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
                        <div class="p-4 border-t border-gray-100 bg-white flex-shrink-0 z-10 shadow-[0_-4px_12px_rgba(0,0,0,0.02)]">
                            <form @submit.prevent="sendReply" class="flex items-end space-x-2">
                                <div class="flex-1 relative">
                                    <textarea
                                        x-model="newMessage"
                                        @keydown.enter.prevent="if (!sending && newMessage.trim()) sendReply()"
                                        placeholder="Ketik balasan pesan..."
                                        rows="2"
                                        class="w-full py-2.5 pl-4 pr-10 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm resize-none bg-gray-50/50 focus:bg-white transition-all"
                                        :disabled="sending"
                                    ></textarea>
                                </div>
                                
                                <button
                                    type="submit"
                                    :disabled="sending || !newMessage.trim()"
                                    :class="sending || !newMessage.trim() 
                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                        : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-100 active:scale-95'"
                                    class="p-3 rounded-xl flex items-center justify-center transition-all duration-150 flex-shrink-0"
                                >
                                    <!-- Send Icon / Spinner -->
                                    <template x-if="sending">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="!sending">
                                        <svg class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </template>
                                </button>
                            </form>
                        </div>
                    </template>

                </div>

            </div>
        </div>
    </div>

    <!-- Script Block for Chat Logic -->
    <script>
        function chatSystem(initialRooms) {
            return {
                rooms: initialRooms,
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
                            this.errorMessage = data.error || 'Gagal mengirim pesan.';
                            setTimeout(() => { this.errorMessage = ''; }, 5000);
                        }
                    } catch (err) {
                        this.newMessage = textToSend;
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
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                },

                formatDateTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString([], { day: '2-digit', month: 'short' }) + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
</x-app-layout>
