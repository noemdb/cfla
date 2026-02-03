<div class="py-12 px-4 fade-in">
    <div class="container mx-auto max-w-2xl">
        @if ($showQRCode && $hasVoted)
            <!-- QR Code Section after voting -->
            <div class="space-y-8">
                <!-- Confirmation Header -->
                <div class="text-center">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-emerald-500/10 backdrop-blur-xl rounded-2xl text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Participación Confirmada
                    </div>
                    <h1 class="text-4xl font-black text-white mb-4 tracking-tight">¡Gracias por participar!</h1>
                    <p class="text-gray-400 font-medium">Tu comprobante digital de participación está listo.</p>
                </div>

                <!-- QR Card -->
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-2xl rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative group">
                    <div
                        class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/5 blur-3xl -mr-20 -mt-20 group-hover:bg-emerald-500/10 transition-all duration-700">
                    </div>

                    <!-- Poll Info Header -->
                    <div
                        class="bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent px-8 py-8 border-b border-white/5">
                        <h2 class="text-2xl font-bold text-white mb-2 leading-tight">{{ $poll->title }}</h2>
                        <div class="flex items-center text-xs font-bold text-emerald-400/80 uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Registrado el {{ now()->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <!-- QR Content -->
                    <div class="p-10 text-center space-y-8">
                        <div class="relative inline-block group/qr">
                            <div
                                class="absolute -inset-4 bg-emerald-500/20 blur-xl opacity-0 group-hover/qr:opacity-100 transition-opacity duration-500">
                            </div>
                            <div class="relative bg-white rounded-3xl p-6 shadow-2xl">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-white">Código QR de Participación</h3>
                            <p class="text-sm text-gray-500 font-medium max-w-xs mx-auto">
                                Escanea este código para acceder a los resultados en tiempo real y detalles de tu voto.
                            </p>
                        </div>

                        <!-- URL Box -->
                        <div class="bg-white/5 border border-white/5 rounded-2xl p-5 space-y-3">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Enlace Directo</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex-1 bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-emerald-400 font-mono text-xs truncate">
                                    {{ $participationUrl }}
                                </div>
                                <button wire:click="copyParticipationUrl"
                                    class="p-3 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white rounded-xl border border-emerald-500/20 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ $participationUrl }}" target="_blank"
                                class="col-span-1 sm:col-span-2 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl transition-all shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                VER RESULTADOS
                            </a>

                            <button onclick="shareParticipation('{{ $participationUrl }}', '{{ $poll->title }}')"
                                class="py-4 bg-white/5 hover:bg-white/10 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl border border-white/5 transition-all text-center">
                                COMPARTIR
                            </button>

                            <button onclick="window.print()"
                                class="py-4 bg-white/5 hover:bg-white/10 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl border border-white/5 transition-all text-center">
                                IMPRIMIR
                            </button>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="bg-gray-900/50 px-8 py-6 border-t border-white/5">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-white uppercase tracking-tight">Seguridad y Anonimato
                                </p>
                                <p
                                    class="text-[10px] text-gray-500 font-medium uppercase tracking-tight leading-relaxed">
                                    Tu participación es 100% anónima. Este código QR es el único comprobante de tu voto
                                    y no está vinculado a tu identidad personal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Normal Voting Section -->
            <div class="space-y-10">
                @if ($poll)
                    <!-- Poll Header -->
                    <div class="text-center space-y-6">
                        <div
                            class="inline-flex items-center px-4 py-2 bg-emerald-500/10 backdrop-blur-xl rounded-2xl text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                            Encuesta Activa
                        </div>
                        <h1 class="text-4xl font-black text-white tracking-tight">{{ $poll->title }}</h1>
                        @if ($poll->description)
                            <p class="text-gray-400 font-medium max-w-lg mx-auto leading-relaxed">
                                {{ $poll->description }}</p>
                        @endif

                        @if ($timeRemaining)
                            <div
                                class="inline-flex items-center px-3 py-1 bg-amber-500/10 text-amber-500 rounded-xl text-[10px] font-bold border border-amber-500/20 uppercase tracking-widest">
                                <svg class="w-3.4 h-3.4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $timeRemaining }} restantes
                            </div>
                        @endif
                    </div>

                    <!-- Voting Card -->
                    <div
                        class="diagnostic-card bg-gray-900/40 backdrop-blur-2xl rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl">
                        @if ($errorState)
                            <!-- Error States -->
                            <div class="p-16 text-center space-y-8">
                                <div
                                    class="w-20 h-20 bg-red-500/10 rounded-3xl flex items-center justify-center mx-auto text-red-500 uppercase">
                                    @if ($errorState === 'poll_not_found' || $errorState === 'poll_expired')
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    @elseif($errorState === 'poll_inactive')
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($errorState === 'already_voted')
                                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <div class="space-y-2">
                                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">
                                        {{ $errorState === 'poll_not_found' ? 'No Encontrada' : ($errorState === 'poll_inactive' ? 'Inactiva' : ($errorState === 'poll_expired' ? 'Expirada' : 'Ya Participaste')) }}
                                    </h3>
                                    <p class="text-gray-500 font-medium">
                                        {{ $errorState === 'poll_not_found' ? 'La encuesta solicitada no está disponible.' : ($errorState === 'poll_inactive' ? 'Esta encuesta no está recibiendo votos ahora.' : ($errorState === 'poll_expired' ? 'El tiempo límite de participación ha terminado.' : 'Gracias, tu voto ya ha sido registrado previamente.')) }}
                                    </p>
                                </div>

                                <button wire:click="refreshPoll"
                                    class="px-10 py-4 bg-white/5 hover:bg-emerald-500 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl border border-white/5 transition-all">
                                    ACTUALIZAR ESTADO
                                </button>
                            </div>
                        @elseif($canVote && !$hasVoted)
                            <!-- Voting Form -->
                            <div class="p-10 sm:p-12 space-y-10">
                                <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] text-center">
                                    Selecciona una Opción</h3>

                                <div class="grid grid-cols-1 gap-4">
                                    @foreach ($poll->options as $option)
                                        <button wire:click="selectOption({{ $option->id }})"
                                            class="w-full text-left p-6 rounded-3xl border transition-all duration-500 group relative overflow-hidden {{ $selectedOption === $option->id ? 'border-emerald-500 bg-emerald-500/10 ring-4 ring-emerald-500/10' : 'border-white/5 bg-white/5 hover:bg-white/[0.08] hover:border-white/10' }}">
                                            @if ($selectedOption === $option->id)
                                                <div
                                                    class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 blur-2xl -mr-12 -mt-12">
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-5 relative">
                                                <div
                                                    class="w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center {{ $selectedOption === $option->id ? 'border-emerald-500 bg-emerald-500' : 'border-gray-700' }}">
                                                    @if ($selectedOption === $option->id)
                                                        <svg class="w-3 h-3 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span
                                                    class="text-lg font-bold transition-all {{ $selectedOption === $option->id ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}">
                                                    {{ $option->label }}
                                                </span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>

                                @error('selectedOption')
                                    <div
                                        class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-500 text-[10px] font-bold uppercase tracking-widest text-center animate-shake">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <button wire:click="vote" wire:loading.attr="disabled"
                                    class="w-full py-6 bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-800 disabled:opacity-50 text-white font-black uppercase tracking-[0.2em] text-xs rounded-3xl transition-all shadow-2xl shadow-emerald-500/20 relative group overflow-hidden">
                                    <div class="relative z-10 flex items-center justify-center gap-3">
                                        <span wire:loading.remove>REGISTRAR MI VOTO</span>
                                        <div wire:loading class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            PROCESANDO...
                                        </div>
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    </div>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    @section('style')
        @parent
        <style>
            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0);
                }

                10%,
                30%,
                50%,
                70%,
                90% {
                    transform: translateX(-4px);
                }

                20%,
                40%,
                60%,
                80% {
                    transform: translateX(4px);
                }
            }

            .animate-shake {
                animation: shake 0.6s cubic-bezier(.36, .07, .19, .97) both;
            }

            @media print {
                body * {
                    visibility: hidden;
                }

                .container,
                .container * {
                    visibility: visible;
                }

                .diagnostic-card {
                    background: white !important;
                    border: 1px solid #eee !important;
                    color: black !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                }

                .text-white,
                .text-gray-400,
                .text-emerald-400,
                .text-emerald-500 {
                    color: black !important;
                }

                .bg-gray-900\/40,
                .bg-emerald-500\/10,
                .bg-white\/5,
                .bg-emerald-500 {
                    background: transparent !important;
                }

                .border-white\/5,
                .border-emerald-500\/20 {
                    border-color: #ddd !important;
                }

                button,
                a:not(.print-visible) {
                    display: none !important;
                }
            }
        </style>
    @endsection
</div>
