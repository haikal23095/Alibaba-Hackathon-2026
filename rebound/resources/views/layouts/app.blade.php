<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REBOUND - AI Flight Assistant & Smart Rebooking</title>
    
    <!-- Favicon: REBOUND Aviation Emblem -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="REBOUND">
    <meta name="description" content="REBOUND - Enterprise AI Passenger Assistant for Instant Flight Disruption Monitoring, Ticket Policy Waiver Verification & One-Click GDS Rebooking.">

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

    <!-- JsBarcode CDN for 100% Real Scannable Barcodes (IATA / Code128) -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

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

    <!-- First-Time Access PNR Onboarding & Physical Ticket Scanner Modal -->
    @include('sections.pnr-onboarding-modal')

    <!-- Modal My Trips -->
    @include('sections.my-trips-modal')

    <!-- Help & FAQ Guide Modal -->
    @include('sections.help-modal')

    <!-- Scannable Large QR Code Modal -->
    @include('sections.qr-modal')

    <!-- Universal Mobile Wallet Selector Modal (Android Google Wallet + iOS Apple Wallet) -->
    @include('sections.wallet-modal')

    <!-- Rebooking Process Animation Modal -->
    @include('sections.rebooking-modal')

    <!-- Multi-Flight GDS Atlas Alternative Schedules Modal -->
    @include('sections.flight-options-modal')

    <!-- Smartphone Bottom Navigation Bar -->
    @include('sections.mobile-bottom-nav')

    <!-- Global Toast Notification Banner -->
    <div x-show="toast.visible" 
         x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-3 scale-95"
         class="fixed bottom-5 right-5 z-50 max-w-sm bg-slate-950 text-white p-3 rounded-lg shadow-lg border border-slate-700 flex items-center gap-2.5 text-xs">
        <div class="w-6 h-6 rounded-md bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
            <i class="fa-solid fa-circle-check text-xs"></i>
        </div>
        <div class="flex-1 font-medium leading-snug" x-text="toast.message"></div>
        <button @click="toast.visible = false" class="text-slate-400 hover:text-white p-1 cursor-pointer">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

    <!-- Modal Tambah Tiket Baru PNR -->
    <div x-show="showAddTicketModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="showAddTicketModal = false"
             class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200 space-y-3.5 text-left">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-brand-50 text-brand-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900" x-text="lang === 'id' ? 'Pantau Tiket PNR Baru' : 'Monitor New Flight PNR'"></h3>
                </div>
                <button @click="showAddTicketModal = false" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1" x-text="t('pnr_input_label')"></label>
                    <input type="text" :placeholder="t('pnr_input_placeholder')" class="w-full border border-slate-200 rounded-lg p-2 text-xs focus:outline-none focus:border-brand-500 font-mono uppercase">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1" x-text="t('passenger_input_label')"></label>
                    <input type="text" :value="currentUser.name" class="w-full border border-slate-200 rounded-lg p-2 text-xs focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="flex gap-2 pt-1">
                <button @click="showAddTicketModal = false" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition cursor-pointer"
                        x-text="t('btn_cancel')">
                </button>
                <button @click="showAddTicketModal = false; selectTicket('GA826')" class="flex-1 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-lg transition cursor-pointer"
                        x-text="t('btn_start_monitor')">
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine.js Main State Management -->
    <script>
        function reboundApp() {
            return {
                lang: localStorage.getItem('rebound_lang') || '{{ App::getLocale() }}' || 'en', // 'en' (primary) or 'id'
                langOpen: false,
                mobileTab: 'assistant', // 'assistant', 'details', 'tickets'
                flightStatus: 'delayed', // 'on-time', 'delayed', 'rebooked'
                activeSidebarTab: 'overview', // 'overview', 'policy', 'schedule', 'receipts'
                sidebarOpen: true,
                leftSidebarOpen: true,
                showMyTripsModal: false,
                showHelpModal: false,
                showQrModal: false,
                showWalletModal: false,
                showFlightOptionsModal: false,
                hasSetupPnr: (localStorage.getItem('rebound_has_setup_pnr') === 'true' ? true : false),
                hasUnreadNotif: true,
                isDownloadingPdf: false,
                isDownloadingPkpass: false,
                toast: { visible: false, message: '' },
                showAddTicketModal: false,
                showUserDropdown: false,
                isRebookingProcess: false,
                rebookStep: 1,
                ticketSearch: '',
                selectedTicketId: 'GA826',
                chatInput: '',
                isTyping: false,

                // Dynamic Context-Aware Prompt Suggestions (Siap integrasi API AI & GDS Atlas)
                get dynamicSuggestions() {
                    if (this.selectedTicketId === 'GA826') {
                        if (this.flightStatus === 'rebooked') {
                            return [
                                { id: 'Lihat e-Boarding Pass baru penerbangan GA830', en: 'View new GA830 e-Boarding Pass' },
                                { id: 'Cek status pengalihan bagasi #GA-489102', en: 'Check baggage transfer status #GA-489102' }
                            ];
                        }
                        return [
                            { id: 'Cek tiket penerbangan untuk besok pagi.', en: 'Check flight tickets for tomorrow morning.' },
                            { id: 'Tanyakan tentang kondisi cuaca di jadwal penerbangan Anda..', en: 'Ask about weather conditions affecting your flight..' },
                            { id: 'Bagaimana hak kompensasi & makanan saya?', en: 'What are my compensation and meal entitlements?' }
                        ];
                    } else if (this.selectedTicketId === 'SQ951') {
                        return [
                            { id: 'Lokasi Plaza Premium Lounge di Terminal 3', en: 'Plaza Premium Lounge location at Terminal 3' },
                            { id: 'Berapa batas berat bagasi kabin Business Class?', en: 'What is Business Class cabin baggage allowance?' }
                        ];
                    } else if (this.selectedTicketId === 'SQ638') {
                        return [
                            { id: 'Cek prakiraan cuaca di Haneda (HND)', en: 'Check weather forecast at Haneda (HND)' },
                            { id: 'Berapa batas berat bagasi kabin Singapore Airlines?', en: 'What is Singapore Airlines cabin baggage allowance?' }
                        ];
                    }
                    return [
                        { id: 'Cek status jadwal penerbangan saya', en: 'Check my flight schedule status' }
                    ];
                },

                // GDS Atlas Multi-Flight Inventory
                alternativeFlightsList: [
                    {
                        flightNumber: 'GA830',
                        airline: 'Garuda Indonesia',
                        airlineCode: 'GA',
                        aircraft: 'Boeing 737-800',
                        gate: '4A',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        depTime: '12:40',
                        arrTime: '15:25',
                        duration: '2j 45m',
                        durationEn: '2h 45m',
                        seatsAvailable: 12,
                        isRecommended: true,
                    },
                    {
                        flightNumber: 'SQ638',
                        airline: 'Singapore Airlines',
                        airlineCode: 'SQ',
                        aircraft: 'Airbus A350-900',
                        gate: '2A',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        depTime: '14:15',
                        arrTime: '17:05',
                        duration: '2j 50m',
                        durationEn: '2h 50m',
                        seatsAvailable: 8,
                        isRecommended: false,
                    },
                    {
                        flightNumber: 'QG524',
                        airline: 'Citilink (Garuda Group)',
                        airlineCode: 'QG',
                        aircraft: 'Airbus A320neo',
                        gate: '5B',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        depTime: '16:30',
                        arrTime: '19:15',
                        duration: '2j 45m',
                        durationEn: '2h 45m',
                        seatsAvailable: 15,
                        isRecommended: false,
                    },
                    {
                        flightNumber: 'ID7153',
                        airline: 'Batik Air',
                        airlineCode: 'ID',
                        aircraft: 'Boeing 737-800',
                        gate: '1C',
                        fromCity: 'Jakarta',
                        fromCode: 'CGK',
                        toCity: 'Singapura',
                        toCityEn: 'Singapore',
                        toCode: 'SIN',
                        depTime: '18:00',
                        arrTime: '20:50',
                        duration: '2j 50m',
                        durationEn: '2h 50m',
                        seatsAvailable: 6,
                        isRecommended: false,
                    }
                ],

                // Translation Catalogue from Symfony/Laravel Translation Service
                translations: {
                    id: @json(trans('messages', [], 'id')),
                    en: @json(trans('messages', [], 'en'))
                },

                t(key) {
                    return (this.translations[this.lang] && this.translations[this.lang][key]) || key;
                },

                init() {
                    this.$nextTick(() => {
                        this.renderBarcode();
                    });
                    this.$watch('flightStatus', () => this.renderBarcode());
                    this.$watch('selectedTicketId', () => this.renderBarcode());
                    this.$watch('activeSidebarTab', () => this.renderBarcode());
                    this.$watch('currentUser', () => this.renderBarcode());
                },

                // Generate Real Mathematical Code128 Scannable Barcode via JsBarcode
                renderBarcode() {
                    this.$nextTick(() => {
                        const barcodeEl = document.getElementById('live-boarding-barcode');
                        if (barcodeEl && typeof JsBarcode !== 'undefined') {
                            const flightNo = this.flightStatus === 'rebooked' ? 'GA830' : 'GA826';
                            const paxName = (this.currentUser && this.currentUser.name) ? this.currentUser.name.replace(/[^A-Za-z]/g, '').slice(0, 8).toUpperCase() : 'ZAKARIA';
                            const codeVal = `M1${paxName}-${flightNo}-14A`;
                            try {
                                JsBarcode(barcodeEl, codeVal, {
                                    format: "CODE128",
                                    lineColor: "#0f172a",
                                    width: 1.1,
                                    height: 16,
                                    displayValue: false,
                                    margin: 0
                                });
                            } catch(e) {}
                        }
                    });
                },
                
                // Authenticated Current User
                currentUser: { 
                    id: {{ Auth::id() ?? 1 }}, 
                    name: @json(Auth::user()->name ?? 'Zakaria MP'), 
                    initials: @json(strtoupper(substr(Auth::user()->name ?? 'ZM', 0, 2))), 
                    email: @json(Auth::user()->email ?? 'zakariamp@rebound.ai'), 
                    passenger: @json((Auth::user()->name ?? 'Zakaria MP') . ' (MR)'),
                    role: 'Frequent Flyer Platinum'
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
                    } else if (id === 'SQ951') {
                        this.flightStatus = 'on-time';
                        this.flight.original = {
                            flightNumber: 'SQ951',
                            airline: 'Singapore Airlines',
                            airlineCode: 'SQ',
                            fromCity: 'Jakarta',
                            fromCode: 'CGK',
                            toCity: 'Singapura',
                            toCityEn: 'Singapore',
                            toCode: 'SIN',
                            date: '15 Okt',
                            dateFullId: '15 Oktober 2026, 05.00',
                            dateFullEn: '15 Oct 2026, 05:00 AM',
                            depTime: '05:00',
                            arrTime: '07:50',
                            class: 'Business Class (SQ KFLY)',
                            seat: (this.currentUser && this.currentUser.name && this.currentUser.name.toLowerCase().includes('maulana')) ? '23D' : '23A',
                            gate: '6',
                            terminal: '3',
                            boardingGroup: '2',
                            lounge: 'Plaza Premium Lounge',
                            statusId: 'Tepat Waktu',
                            statusEn: 'On Time',
                            delayTime: '-',
                            delayCauseId: 'Normal',
                            delayCauseEn: 'Normal',
                            changeAllowedId: 'Ya (Gratis)',
                            changeAllowedEn: 'Yes (Complimentary)',
                            feeAmountId: 'Rp 0',
                            feeAmountEn: '$0',
                            fareDiffId: 'Berlaku',
                            fareDiffEn: 'Applies',
                        };
                        this.messages = [
                            {
                                sender: 'ai',
                                time: '05:00',
                                type: 'greeting',
                                textId: `Halo ${this.currentUser.name}! Tiket Business Class Singapore Airlines SQ951 (Jakarta CGK → Singapura SIN) Anda telah terverifikasi. Keberangkatan dari Terminal 3, Gate 6 pukul 05:00 AM. Anda memiliki akses ke Plaza Premium Lounge.`,
                                textEn: `Hello ${this.currentUser.name}! Your Singapore Airlines SQ951 Business Class ticket (Jakarta CGK → Singapore SIN) is verified. Departing from Terminal 3, Gate 6 at 05:00 AM with Plaza Premium Lounge access.`,
                                showRecommendation: false,
                            }
                        ];
                    } else if (id === 'SQ638') {
                        this.flightStatus = 'on-time';
                        this.flight.original = {
                            flightNumber: 'SQ638',
                            airline: 'Singapore Airlines',
                            airlineCode: 'SQ',
                            fromCity: 'Singapura',
                            fromCode: 'SIN',
                            toCity: 'Tokyo',
                            toCityEn: 'Tokyo',
                            toCode: 'HND',
                            date: '05 Des',
                            dateFullId: '05 Desember 2026, 23.55',
                            dateFullEn: '05 Dec 2026, 11:55 PM',
                            depTime: '23:55',
                            arrTime: '07:30',
                            class: 'Economy (K)',
                            seat: '18C',
                            gate: 'B4',
                            terminal: '3',
                            boardingGroup: '4',
                            lounge: '-',
                            statusId: 'Tepat Waktu',
                            statusEn: 'On Time',
                            delayTime: '-',
                            delayCauseId: 'Normal',
                            delayCauseEn: 'Normal',
                            changeAllowedId: 'Ya',
                            changeAllowedEn: 'Yes',
                            feeAmountId: 'Rp750.000',
                            feeAmountEn: '$50',
                            fareDiffId: 'Berlaku',
                            fareDiffEn: 'Applies',
                        };
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

                // Set language (Sync with Laravel / Symfony Translator backend)
                async setLanguage(l) {
                    this.lang = l;
                    try {
                        localStorage.setItem('rebound_lang', l);
                        await fetch('/lang/' + l, { headers: { 'Accept': 'application/json' } });
                    } catch(e) {}
                    this.langOpen = false;
                },

                // Select Custom Alternative Flight from GDS Atlas Modal
                selectCustomAlternative(altFlight) {
                    this.flight.alternative = {
                        flightNumber: altFlight.flightNumber,
                        airline: altFlight.airline,
                        airlineCode: altFlight.airlineCode,
                        fromCity: altFlight.fromCity,
                        fromCode: altFlight.fromCode,
                        toCity: altFlight.toCity,
                        toCityEn: altFlight.toCityEn,
                        toCode: altFlight.toCode,
                        depTime: altFlight.depTime,
                        arrTime: altFlight.arrTime,
                        duration: altFlight.duration,
                        durationEn: altFlight.durationEn,
                        departureCountdownId: 'Berangkat ' + altFlight.depTime + ' WIB',
                        departureCountdownEn: 'Departs at ' + altFlight.depTime,
                    };
                    this.rebookFlight(altFlight);
                },

                // Rebook action with multi-step telemetry dispatch animation
                rebookFlight(targetFlight = null) {
                    const target = targetFlight || this.flight.alternative;
                    this.isRebookingProcess = true;
                    this.rebookStep = 1;

                    // Step 1: Disruption Tariff Waiver Check
                    setTimeout(() => {
                        this.rebookStep = 2;
                    }, 700);

                    // Step 2: Baggage Auto-Transfer
                    setTimeout(() => {
                        this.rebookStep = 3;
                    }, 1400);

                    // Step 3: Boarding Pass & Seat Assignment Completed
                    setTimeout(() => {
                        this.rebookStep = 4;
                        setTimeout(() => {
                            this.isRebookingProcess = false;
                            this.flightStatus = 'rebooked';
                            this.activeSidebarTab = 'receipts'; // Automatically display the new Boarding Pass in sidebar!

                            this.messages.push({
                                sender: 'user',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'text',
                                textId: `Pindahkan saya ke penerbangan ${target.airline} (${target.flightNumber}).`,
                                textEn: `Transfer me to ${target.airline} (${target.flightNumber}).`,
                            });

                            this.scrollToBottom();
                            this.isTyping = true;
                            setTimeout(() => {
                                this.isTyping = false;
                                this.messages.push({
                                    sender: 'ai',
                                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                    type: 'success_card',
                                    textId: `Pengalihan ke ${target.flightNumber} berhasil dikonfirmasi. Klausul bebas penalti (Waiver 72A) aktif dan e-Boarding Pass baru telah diterbitkan untuk ${this.currentUser.name}. Bagasi tag #GA-489102 telah dialihkan ke pesawat baru.`,
                                    textEn: `Rebooking to ${target.flightNumber} confirmed. Disruption fee waiver (Rule 72A) applied and new e-Boarding Pass issued for ${this.currentUser.name}. Baggage tag #GA-489102 has been routed to the new flight.`,
                                    showSuccess: true
                                });
                                this.scrollToBottom();
                                this.renderBarcode();
                            }, 400);
                        }, 600);
                    }, 2100);
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
                    this.scrollToBottom();

                    // AI Simulated Response
                    setTimeout(() => {
                        this.isTyping = false;
                        const lower = text.toLowerCase();

                        if (lower.includes('besok pagi') || lower.includes('cek tiket') || lower.includes('aturan') || lower.includes('policy') || lower.includes('biaya')) {
                            // Figma Node 22:1109 - In-Chat Verified Ticket Policy Card
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'policy_card',
                                textId: 'Sedang memeriksa kebijakan tiket Anda...',
                                textEn: 'Checking your ticket policy...',
                                showTicketPolicy: true
                            });
                        } else if (lower.includes('cuaca') || lower.includes('weather') || lower.includes('kondisi')) {
                            // Figma Node 15:777 & 21:894 - Disruption Progress & Recommendation
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'disruption_alert',
                                textId: 'Penerbangan GA826 Anda mengalami keterlambatan 4 jam 25 menit akibat cuaca buruk. Saya sudah mulai memeriksa aturan tiket dan mencari penerbangan alternatif untuk Anda.',
                                textEn: 'Your flight GA826 is delayed 4 hours 25 minutes due to bad weather. I have begun checking ticket rules and finding alternative flights for you.',
                                showDisruptionProgress: true,
                                showRecommendation: true
                            });
                        } else if (lower.includes('opsi') || lower.includes('lain') || lower.includes('jadwal') || lower.includes('alternative')) {
                            this.showFlightOptionsModal = true;
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'options_list',
                                textId: 'Berikut daftar penerbangan alternatif dari sistem GDS Atlas maskapai mitra yang tersedia untuk rute Anda hari ini. Seluruh jadwal memenuhi syarat bebas biaya (Waiver 72A).',
                                textEn: 'Here are the alternative flights from the partner airline GDS Atlas system available for your route today. All schedules are eligible for zero-fee waiver (Waiver 72A).'
                            });
                        } else {
                            this.messages.push({
                                sender: 'ai',
                                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                type: 'text',
                                textId: `Saya siap membantu Anda, ${this.currentUser.name}. Saya dapat memberikan update penerbangan, rekomendasi rute alternatif GDS, atau klaim kompensasi gangguan penerbangan.`,
                                textEn: `I am here to assist you, ${this.currentUser.name}. I can provide flight updates, GDS alternative route recommendations, or flight disruption compensation.`
                            });
                        }
                        this.scrollToBottom();
                    }, 500);
                },

                // Download Official PDF Boarding Pass / Trigger Print to PDF
                downloadPdf() {
                    this.isDownloadingPdf = true;
                    setTimeout(() => {
                        this.isDownloadingPdf = false;
                        const flightNo = this.flightStatus === 'rebooked' ? 'GA830' : 'GA826';
                        const airline = 'Garuda Indonesia';
                        const gate = this.flightStatus === 'rebooked' ? '4A' : '3B';
                        const boarding = this.flightStatus === 'rebooked' ? '12:00 WIB' : '08:50 WIB';

                        // Generate printable official E-Boarding Pass HTML Document
                        const printWindow = window.open('', '_blank');
                        if (printWindow) {
                            printWindow.document.write(`
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <title>E-Boarding Pass - ${flightNo} - ${this.currentUser.passenger}</title>
                                    <style>
                                        @page { size: A4 portrait; margin: 12mm; }
                                        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; margin: 0; padding: 20px; background: #f8fafc; }
                                        .ticket { max-width: 650px; margin: 0 auto; background: #fff; border: 2px solid #cbd5e1; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
                                        .header { background: #0B3B60; color: #fff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
                                        .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.5px; }
                                        .header p { margin: 4px 0 0; font-size: 12px; color: #bae6fd; font-family: monospace; }
                                        .badge { background: rgba(52,211,153,0.2); border: 1px solid #34d399; color: #6ee7b7; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
                                        .body { padding: 24px; }
                                        .route { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; }
                                        .city { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; }
                                        .code { font-size: 32px; font-weight: 900; margin: 2px 0; color: #0f172a; }
                                        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; background: #f8fafc; padding: 14px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
                                        .grid div { text-align: center; }
                                        .grid .label { font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; }
                                        .grid .val { font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 2px; }
                                        .grid .val.gate { color: #0284c7; }
                                        .baggage { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; display: flex; justify-content: space-between; margin-bottom: 20px; }
                                        .stub { border-top: 2px dashed #cbd5e1; padding-top: 16px; display: flex; justify-content: space-between; align-items: center; }
                                        .qr img { width: 90px; height: 90px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; }
                                        .footer { font-size: 11px; color: #94a3b8; text-align: center; margin-top: 24px; }
                                    </style>
                                </head>
                                <body>
                                    <div class="ticket">
                                        <div class="header">
                                            <div>
                                                <h1>${airline}</h1>
                                                <p>${flightNo} • BOEING 737-800 • ECONOMY CLASS (V)</p>
                                            </div>
                                            <div class="badge">CONFIRMED / BOARDING</div>
                                        </div>
                                        <div class="body">
                                            <div class="route">
                                                <div>
                                                    <div class="city">FROM / DARI</div>
                                                    <div class="code">CGK</div>
                                                    <div style="font-size:12px; font-weight:600;">Jakarta (CGK)</div>
                                                </div>
                                                <div style="text-align:center;">
                                                    <div style="font-size:11px; font-weight:bold; color:#059669;">NON-STOP (1h 45m)</div>
                                                    <div style="font-size:14px; font-weight:bold; color:#0284c7; letter-spacing:2px;">DIRECT FLIGHT &rarr;</div>
                                                </div>
                                                <div style="text-align:right;">
                                                    <div class="city">TO / KE</div>
                                                    <div class="code">SIN</div>
                                                    <div style="font-size:12px; font-weight:600;">Singapore (SIN)</div>
                                                </div>
                                            </div>
                                            <div class="grid">
                                                <div><div class="label">GATE</div><div class="val gate">${gate}</div></div>
                                                <div><div class="label">BOARDING</div><div class="val">${boarding}</div></div>
                                                <div><div class="label">SEAT</div><div class="val">14A</div></div>
                                                <div><div class="label">ZONE</div><div class="val">2</div></div>
                                            </div>
                                            <div class="baggage">
                                                <span>Baggage Tag: <strong>#GA-489102</strong></span>
                                                <span style="color:#047857; font-weight:bold;">AUTO-TRANSFERRED</span>
                                            </div>
                                            <div class="stub">
                                                <div>
                                                    <div style="font-size:10px; color:#64748b; font-weight:bold;">PASSENGER / NAMA PENUMPANG</div>
                                                    <div style="font-size:14px; font-weight:bold; margin-top:2px;">${this.currentUser.passenger}</div>
                                                    <div style="font-size:11px; font-family:monospace; margin-top:4px; color:#0284c7;">PNR: GA-9821A • ETKT: 126-289410293</div>
                                                    <div style="font-size:11px; color:#059669; font-weight:bold; margin-top:4px;">Disruption Waiver 72A (Fee $0 / Rp 0)</div>
                                                </div>
                                                <div class="qr">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=REBOUND%20E-BOARDING%20PASS%20PNR%20GA-9821A%20${encodeURIComponent(this.currentUser.passenger)}%20${flightNo}%20GATE%20${gate}%20SEAT%2014A" alt="QR Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer">REBOUND Smart Airline Dispatch System • IATA Barcode & QR Code Compliant</div>
                                    <script>window.onload = function() { window.print(); }<\/script>
                                </body>
                                </html>
                            `);
                            printWindow.document.close();
                        }

                        this.showToast(
                            this.lang === 'id'
                                ? `Dokumen PDF Resmi E-Boarding Pass ${flightNo} siap dicetak/disimpan!`
                                : `Official E-Boarding Pass PDF for ${flightNo} ready to save/print!`
                        );
                    }, 500);
                },

                // Download Digital Apple Wallet Pass (.pkpass)
                downloadPkpass() {
                    this.isDownloadingPkpass = true;
                    setTimeout(() => {
                        this.isDownloadingPkpass = false;
                        const flightNo = this.flightStatus === 'rebooked' ? 'GA830' : 'GA826';
                        const filename = `BoardingPass-${flightNo}-${this.currentUser.name.replace(/\s+/g, '_')}.pkpass`;
                        const dummyBlob = new Blob([
                            `REBOUND AVIATION ELECTRONIC BOARDING PASS (.PKPASS)\n===================================================\nPassenger: ${this.currentUser.passenger}\nFlight: ${flightNo}\nRoute: CGK -> SIN\nGate: ${this.flightStatus === 'rebooked' ? '4A' : '3B'}\nSeat: 14A\nZone: 2\nPNR: GA-9821A\nStatus: CONFIRMED\nDisruption Fee Waiver 72A: Rp 0\nBarcode: M1PRASETYO/ZAKARIA EGA830 CGKSIN\n===================================================`
                        ], { type: 'application/vnd.apple.pkpass' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(dummyBlob);
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        this.showToast(
                            this.lang === 'id'
                                ? `Digital Apple Wallet Pass (.pkpass) ${flightNo} berhasil diunduh!`
                                : `Digital Apple Wallet Pass (.pkpass) for ${flightNo} downloaded!`
                        );
                    }, 500);
                },

                // Save to Google Wallet (Android Support)
                saveGoogleWallet() {
                    const flightNo = this.flightStatus === 'rebooked' ? 'GA830' : 'GA826';
                    this.showToast(
                        this.lang === 'id'
                            ? `Pass Digital ${flightNo} berhasil disinkronkan ke Google Wallet Android!`
                            : `Digital Pass ${flightNo} successfully added to Google Wallet Android!`
                    );
                },

                // Save High-Resolution Boarding Pass Image to Photo Gallery (Universal)
                downloadPassImage() {
                    const flightNo = this.flightStatus === 'rebooked' ? 'GA830' : 'GA826';
                    const link = document.createElement('a');
                    link.href = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=15&data=${encodeURIComponent('REBOUND PASS: PNR GA-9821A | ' + this.currentUser.passenger + ' | ' + flightNo + ' CGK->SIN | SEAT: 14A | GATE: ' + (this.flightStatus === 'rebooked' ? '4A' : '3B'))}`;
                    link.download = `BoardingPass-QR-${flightNo}.png`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.showToast(
                        this.lang === 'id'
                            ? `Gambar Tiket & QR Code HD berhasil disimpan ke Galeri Foto HP!`
                            : `Boarding Pass & HD QR Image saved to Photo Gallery!`
                    );
                },

                // Show Global Toast Notification
                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.visible = true;
                    setTimeout(() => {
                        this.toast.visible = false;
                    }, 4000);
                },

                // Smooth Scroll to bottom
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('chat-messages-container');
                        if (container) {
                            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
