<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REBOUND - AI Flight Assistant & Smart Rebooking</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS Play CDN (No Vite Required) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', '"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                    },
                    boxShadow: {
                        'soft': '0 2px 12px -3px rgba(0, 0, 0, 0.04), 0 3px 5px -2px rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 16px -2px rgba(15, 23, 42, 0.05)',
                        'floating': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for Seamless Reactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .chat-bubble-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-[#1E293B] bg-[#F8FAFC] flex flex-col selection:bg-brand-500 selection:text-white"
      x-data="reboundApp()">

    <!-- Top App Bar Navigation -->
    @include('sections.navbar')

    <!-- Main Content Workspace -->
    <main class="flex-1 flex overflow-hidden">
        @yield('content')
    </main>

    <!-- Modal My Trips -->
    @include('sections.my-trips-modal')

    <!-- Modal Tambah Tiket Baru PNR -->
    <div x-show="showAddTicketModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="showAddTicketModal = false"
             class="bg-white rounded-2xl max-w-md w-full p-5 shadow-floating border border-slate-100 space-y-4">
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900" x-text="lang === 'id' ? 'Pantau Tiket Penerbangan Baru' : 'Monitor New Flight Ticket'"></h3>
                </div>
                <button @click="showAddTicketModal = false" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Booking / PNR</label>
                    <input type="text" placeholder="Contoh: GA-9821A" class="w-full border border-slate-200 rounded-lg p-2 focus:outline-none focus:border-brand-500 font-mono uppercase">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Penumpang</label>
                    <input type="text" :value="currentUser.name" class="w-full border border-slate-200 rounded-lg p-2 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="showAddTicketModal = false" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                    Batal
                </button>
                <button @click="showAddTicketModal = false; selectTicket('GA826')" class="flex-1 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-lg transition">
                    Mulai Pantau
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine.js Main State Management -->
    <script>
        function reboundApp() {
            return {
                lang: 'id', // 'id' or 'en'
                flightStatus: 'delayed', // 'on-time', 'delayed', 'rebooked'
                activeSidebarTab: 'overview', // 'overview', 'policy', 'schedule', 'receipts'
                sidebarOpen: true,
                leftSidebarOpen: true,
                showMyTripsModal: false,
                showAddTicketModal: false,
                showUserDropdown: false,
                ticketSearch: '',
                selectedTicketId: 'GA826',
                chatInput: '',
                isTyping: false,
                
                // 3 Dummy Users (Zakaria MP, Haikal Firmansyah, Tiara Fatimah Azzahra)
                users: [
                    { 
                        id: 1, 
                        name: 'Zakaria MP', 
                        initials: 'ZM', 
                        email: 'zakariamp@rebound.ai', 
                        passenger: 'Zakaria MP (MR)',
                        role: 'Frequent Flyer Platinum'
                    },
                    { 
                        id: 2, 
                        name: 'Haikal Firmansyah', 
                        initials: 'HF', 
                        email: 'haikal.firmansyah@rebound.ai', 
                        passenger: 'Haikal Firmansyah (MR)',
                        role: 'Business Traveler'
                    },
                    { 
                        id: 3, 
                        name: 'Tiara Fatimah Azzahra', 
                        initials: 'TF', 
                        email: 'tiara.azzahra@rebound.ai', 
                        passenger: 'Tiara Fatimah Azzahra (MS)',
                        role: 'Premium Economy'
                    }
                ],
                currentUser: { 
                    id: {{ Auth::id() ?? 1 }}, 
                    name: @json(Auth::user()->name ?? 'Zakaria MP'), 
                    initials: @json(strtoupper(substr(Auth::user()->name ?? 'ZM', 0, 2))), 
                    email: @json(Auth::user()->email ?? 'zakariamp@rebound.ai'), 
                    passenger: @json((Auth::user()->name ?? 'Zakaria MP') . ' (MR)'),
                    role: 'Frequent Flyer Platinum'
                },

                setUser(user) {
                    this.currentUser = user;
                    this.showUserDropdown = false;
                },

                // Flight Data State
                flight: {
                    original: {
                        flightNumber: 'GA826',
                        airline: 'Garuda Indonesia',
                        airlineCode: 'GA',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        date: '30 Nov',
                        dateFullId: '30 November, 09.30',
                        dateFullEn: '30 Nov, 09:30 AM',
                        depTime: '09:30',
                        arrTime: '12:20',
                        class: 'Economy (V)',
                        statusId: 'Terlambat +4j 25m',
                        statusEn: 'Delayed +4h 25m',
                        delayTime: '30 Nov, 09.30 PM',
                        delayCauseId: 'Cuaca buruk',
                        delayCauseEn: 'Bad weather',
                        changeAllowedId: 'Ya',
                        changeAllowedEn: 'Yes',
                        feeAmountId: 'Rp750.000',
                        feeAmountEn: '$50',
                        fareDiffId: 'Berlaku',
                        fareDiffEn: 'Applies',
                    },
                    alternative: {
                        flightNumber: 'GA830',
                        airline: 'Garuda Indonesia',
                        airlineCode: 'GA',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        depTime: '12:40',
                        arrTime: '15:25',
                        duration: '2j 45m',
                        durationEn: '2h 45m',
                        departureCountdownId: 'Berangkat 45 menit lagi',
                        departureCountdownEn: 'Departs in 45 min',
                    }
                },

                // Chat Messages History
                messages: [
                    {
                        sender: 'ai',
                        time: '09:35',
                        type: 'greeting',
                        textId: 'Halo! Saya sedang memantau penerbangan GA826 Anda ke Singapura. Saat ini penerbangan Anda mengalami keterlambatan 4 jam 25 menit akibat cuaca buruk. Saya sudah mulai memeriksa aturan tiket dan mencari penerbangan alternatif untuk Anda.',
                        textEn: "Hello! I'm monitoring your flight GA826 to Singapore. Your flight is currently delayed by 4 hours 25 minutes due to bad weather. I have begun checking ticket rules and finding alternative flights for you.",
                        showRecommendation: true,
                    }
                ],

                // Select Ticket from Left Sidebar
                selectTicket(id) {
                    this.selectedTicketId = id;
                    if (id === 'GA826') {
                        this.flightStatus = 'delayed';
                        this.flight.original.flightNumber = 'GA826';
                        this.flight.original.fromCode = 'CGK';
                        this.flight.original.toCode = 'SIN';
                        this.flight.original.airline = 'Garuda Indonesia';
                        this.messages = [
                            {
                                sender: 'ai',
                                time: '09:35',
                                type: 'greeting',
                                textId: `Halo ${this.currentUser.name}! Saya sedang memantau penerbangan GA826 Anda ke Singapura. Saat ini penerbangan Anda mengalami keterlambatan 4 jam 25 menit akibat cuaca buruk. Saya sudah mulai memeriksa aturan tiket dan mencari penerbangan alternatif untuk Anda.`,
                                textEn: `Hello ${this.currentUser.name}! I'm monitoring your flight GA826 to Singapore. Your flight is currently delayed by 4 hours 25 minutes due to bad weather. I have begun checking ticket rules and finding alternative flights for you.`,
                                showRecommendation: true,
                            }
                        ];
                    } else if (id === 'SQ638') {
                        this.flightStatus = 'on-time';
                        this.flight.original.flightNumber = 'SQ638';
                        this.flight.original.fromCode = 'SIN';
                        this.flight.original.toCode = 'HND';
                        this.flight.original.airline = 'Singapore Airlines';
                        this.flight.original.class = 'Economy (K)';
                        this.messages = [
                            {
                                sender: 'ai',
                                time: '11:00',
                                type: 'greeting',
                                textId: `Halo ${this.currentUser.name}! Saya memantau tiket SQ638 Anda ke Tokyo (Haneda) pada 05 Desember 2026. Penerbangan saat ini dijadwalkan tepat waktu pukul 23:55 dari Bandara Changi (Terminal 3).`,
                                textEn: `Hello ${this.currentUser.name}! I am tracking your ticket SQ638 to Tokyo (Haneda) on 05 Dec 2026. Flight is currently on time at 23:55 from Changi Airport (Terminal 3).`,
                                showRecommendation: false,
                            }
                        ];
                    } else {
                        this.flightStatus = 'on-time';
                        this.messages = [
                            {
                                sender: 'ai',
                                time: '10:00',
                                type: 'greeting',
                                textId: `Perjalanan ini telah selesai untuk ${this.currentUser.name}. Anda dapat melihat resi dan riwayat e-tiket pada tab Resi di sidebar kanan.`,
                                textEn: `This trip has been completed for ${this.currentUser.name}. You can view receipts and e-ticket history in the Receipts tab on the right sidebar.`,
                                showRecommendation: false,
                            }
                        ];
                    }
                },

                // Switch status helpers
                setStatus(status) {
                    this.flightStatus = status;
                    if (status === 'rebooked') {
                        this.activeSidebarTab = 'schedule';
                    }
                },

                // Toggle language
                setLanguage(l) {
                    this.lang = l;
                },

                // Rebook action
                rebookFlight() {
                    this.flightStatus = 'rebooked';
                    this.messages.push({
                        sender: 'user',
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        type: 'text',
                        textId: 'Pindahkan saya ke penerbangan Garuda GA830.',
                        textEn: 'Transfer me to Garuda GA830 flight.',
                    });

                    this.isTyping = true;
                    setTimeout(() => {
                        this.isTyping = false;
                        this.messages.push({
                            sender: 'ai',
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                            type: 'success_card',
                            textId: `Karena keterlambatan penerbangan Anda sebelumnya, biaya perubahan telah dibebaskan untuk ${this.currentUser.name} sesuai kebijakan maskapai.`,
                            textEn: `Due to the previous flight delay, change fees have been waived for ${this.currentUser.name} according to airline policy.`,
                            showSuccess: true
                        });
                    }, 500);
                },

                // Send User Message
                sendMessage(customText = null) {
                    const text = customText || this.chatInput;
                    if (!text || text.trim() === '') return;

                    this.messages.push({
                        sender: 'user',
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        type: 'text',
                        textId: text,
                        textEn: text
                    });

                    this.chatInput = '';
                    this.isTyping = true;

                    // AI Simulated Response
                    setTimeout(() => {
                        this.isTyping = false;
                        const lower = text.toLowerCase();

                        if (lower.includes('cuaca') || lower.includes('weather')) {
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'text',
                                textId: 'Kondisi cuaca di Bandara Soekarno-Hatta (CGK) saat ini mengalami hujan badai dengan jarak pandang terbatas, yang menyebabkan penundaan keberangkatan GA826. Namun rute udara menuju Changi (SIN) diperkirakan membaik mulai pukul 12:00 WIB.',
                                textEn: 'Weather conditions at Soekarno-Hatta Airport (CGK) currently indicate thunderstorms with reduced visibility, delaying GA826. However, airspace to Changi (SIN) is projected to clear starting at 12:00 PM.'
                            });
                        } else if (lower.includes('aturan') || lower.includes('policy') || lower.includes('biaya') || lower.includes('fee')) {
                            this.activeSidebarTab = 'policy';
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'text',
                                textId: `Berdasarkan aturan tiket Ekonomi (V), ${this.currentUser.name} berhak melakukan perubahan jadwal tanpa biaya tambahan (Free Reschedule) karena keterlambatan penerbangan melebihi 4 jam akibat alasan operasional/cuaca.`,
                                textEn: `Based on Economy (V) fare rules, ${this.currentUser.name} is entitled to a free reschedule without additional penalty fees because the flight disruption exceeds 4 hours due to weather/operational reasons.`
                            });
                        } else {
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'text',
                                textId: `Saya siap membantu Anda, ${this.currentUser.name}. Saya dapat memberikan update penerbangan, rekomendasi rute alternatif, atau klaim kompensasi.`,
                                textEn: `I am here to assist you, ${this.currentUser.name}. I can provide flight updates, rebooking recommendations, or compensation guidance.`
                            });
                        }
                    }, 600);
                }
            }
        }
    </script>
</body>
</html>
