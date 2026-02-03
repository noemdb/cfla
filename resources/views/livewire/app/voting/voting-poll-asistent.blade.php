<div>
    @if (!$isCompleted)
        <!-- Fingerprint loading indicator -->
        @if ($isLoadingFingerprint)
            <div
                class="diagnostic-card bg-amber-500/10 backdrop-blur-xl rounded-[2rem] border border-amber-500/20 shadow-2xl p-8 mb-8 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-amber-500/10 transition-all duration-500">
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative flex h-10 w-10 flex-shrink-0">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-20"></span>
                        <div class="relative bg-amber-500/20 rounded-xl p-2 border border-amber-500/30">
                            <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-black text-white uppercase tracking-tight">Iniciando Ecosistema de
                            Votación Seguridad...</h4>
                        <p class="text-xs font-bold text-amber-500/80 uppercase tracking-widest">Generando identificador
                            criptográfico único</p>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-amber-500/10 pt-6">
                    @if ($fingerprintAttempts > 0)
                        <div
                            class="px-4 py-1.5 bg-amber-500/5 rounded-full border border-amber-500/10 text-[10px] font-black text-amber-500 uppercase tracking-widest">
                            Intento {{ $fingerprintAttempts }} de {{ $maxFingerprintAttempts }}
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <button wire:click="retryFingerprintGeneration"
                            class="px-6 py-2.5 bg-amber-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-amber-500/20 hover:bg-amber-600">
                            REINTENTAR
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Progress Bar -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-[2rem] border border-white/5 shadow-2xl p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="space-y-1">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest">Estado del Asistente</h3>
                    <p class="text-lg font-bold text-white tracking-tight">{{ $currentPollIndex + 1 }} de
                        {{ $totalPolls }} Encuestas procesadas</p>
                </div>
                <div
                    class="px-3 py-1 bg-emerald-500/10 rounded-lg text-[10px] font-black text-emerald-400 uppercase tracking-widest">
                    {{ round((($currentPollIndex + 1) / $totalPolls) * 100) }}%
                </div>
            </div>

            <div class="relative w-full h-3 bg-white/5 rounded-full overflow-hidden border border-white/5">
                <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-emerald-600 to-emerald-400 rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ (($currentPollIndex + 1) / $totalPolls) * 100 }}%">
                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Fase de participación</p>
                <p class="text-[10px] font-black text-emerald-500/60 uppercase tracking-widest">
                    {{ $totalPolls - ($currentPollIndex + 1) }} restantes para completar</p>
            </div>
        </div>

        <!-- Current Poll -->
        @if ($currentPoll)
            <div
                class="diagnostic-card bg-gray-900/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 shadow-2xl overflow-hidden {{ $isLoadingFingerprint ? 'opacity-50 pointer-events-none grayscale' : '' }} fade-in">
                <!-- Poll Header -->
                <div
                    class="bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent px-10 py-8 border-b border-white/5 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-all duration-700">
                    </div>

                    <div class="flex items-center justify-between gap-6 relative">
                        <div class="space-y-2">
                            <div
                                class="inline-flex items-center px-3 py-1 bg-emerald-500/10 rounded-lg text-[10px] font-black text-emerald-400 uppercase tracking-widest border border-emerald-500/20 mb-2">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                                ENCUESTA ACTIVA
                            </div>
                            <h2 class="text-2xl font-black text-white leading-tight tracking-tight uppercase">
                                {{ $currentPoll['title'] }}</h2>
                            <div
                                class="flex items-center gap-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    {{ count($currentPoll['options']) }} OPCIONES
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-black text-white leading-none opacity-20 select-none">
                                #{{ sprintf('%02d', $currentPollIndex + 1) }}</p>
                            <p class="text-[10px] font-black text-emerald-500/40 uppercase tracking-widest mt-1">DE
                                {{ sprintf('%02d', $totalPolls) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Expiration Check -->
                @php
                    $currentPollModel = \App\Models\app\Voting\VotingPoll::find($currentPoll['id']);
                    $isExpired = $currentPollModel ? $currentPollModel->isExpired() : false;
                @endphp

                @if ($isExpired)
                    <div class="bg-red-500/10 border-y border-red-500/20 px-10 py-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center flex-shrink-0 border border-red-500/20">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-black text-red-500 uppercase tracking-widest">Encuesta
                                    Finalizada</h4>
                                <p class="text-[10px] text-red-500/60 font-bold uppercase tracking-tight">Esta sesión ya
                                    no acepta nuevas participaciones.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Poll Content -->
                <div class="p-10">
                    @if ($successMessage || $errorMessage)
                        <div
                            class="mb-8 p-5 rounded-2xl border animate-fade-in {{ $successMessage ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border-red-500/20 text-red-500' }}">
                            <div class="flex items-center gap-3">
                                @if ($successMessage)
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest">{{ $successMessage }}
                                    </p>
                                @else
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest">{{ $errorMessage }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (!$hasVoted && !$isLoadingFingerprint && !$isExpired)
                        <div class="space-y-4 mb-10">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-center mb-6">
                                Selecciona una Opción</p>
                            @foreach ($currentPoll['options'] as $option)
                                <button wire:click="selectOption({{ $option['id'] }})"
                                    class="w-full p-6 text-left rounded-3xl border-2 transition-all duration-300 relative overflow-hidden group/opt {{ $selectedOption == $option['id'] ? 'border-emerald-500 bg-emerald-500/10 ring-4 ring-emerald-500/10' : 'border-white/5 bg-white/5 hover:bg-white/[0.08] hover:border-white/10' }}"
                                    {{ $isVoting ? 'disabled' : '' }}>

                                    @if ($selectedOption == $option['id'])
                                        <div
                                            class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 blur-2xl -mr-12 -mt-12">
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-5 relative">
                                        <div
                                            class="w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center {{ $selectedOption == $option['id'] ? 'border-emerald-500 bg-emerald-500 shadow-lg shadow-emerald-500/20' : 'border-gray-700' }}">
                                            @if ($selectedOption == $option['id'])
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <span
                                            class="text-lg font-bold transition-all {{ $selectedOption == $option['id'] ? 'text-white' : 'text-gray-400 group-hover/opt:text-gray-200' }}">
                                            {{ $option['label'] ?? null }}
                                        </span>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Vote Button -->
                        @if ($selectedOption)
                            <button wire:click="submitVote"
                                class="w-full py-6 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-[0.2em] text-xs rounded-3xl transition-all shadow-2xl shadow-emerald-500/30 group overflow-hidden relative"
                                {{ $isVoting ? 'disabled' : '' }}>
                                <div class="relative z-10 flex items-center justify-center gap-3">
                                    @if ($isVoting)
                                        <svg class="animate-spin h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        PROCESANDO...
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        CONFIRMAR MI VOTO
                                    @endif
                                </div>
                                <div
                                    class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                </div>
                            </button>
                        @endif
                    @elseif($isExpired)
                        <div class="text-center py-16 space-y-6">
                            <div
                                class="w-20 h-20 bg-red-500/5 rounded-[2.5rem] flex items-center justify-center mx-auto border border-red-500/10">
                                <svg class="w-10 h-10 text-red-500/40" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight">Sesión Finalizada
                                </h3>
                                <p class="text-gray-500 font-medium max-w-xs mx-auto text-sm leading-relaxed">Esta
                                    encuesta ya no acepta participaciones. Por favor, continúa a la siguiente.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Navigation Footer -->
                    <div class="flex items-center justify-between mt-12 pt-8 border-t border-white/5 relative">
                        <button wire:click="previousPoll"
                            class="group flex items-center gap-3 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-white transition-all {{ $currentPollIndex == 0 ? 'opacity-20 cursor-not-allowed pointer-events-none' : '' }}"
                            {{ $currentPollIndex == 0 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>

                        <div class="flex items-center gap-4">
                            @if ($isExpired)
                                <button wire:click="skipExpiredPoll"
                                    class="px-6 py-3 bg-red-500/10 text-red-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-500/20 hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/10">
                                    Saltar Expirada
                                </button>
                            @endif

                            <button wire:click="nextPoll"
                                class="group flex items-center gap-3 px-10 py-4 rounded-[1.25rem] transition-all font-black uppercase tracking-widest text-[10px] {{ $hasVoted || $isLoadingFingerprint || $isExpired ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20 hover:bg-blue-700' : 'bg-gray-800 text-gray-600 cursor-not-allowed' }}"
                                {{ !$hasVoted && !$isLoadingFingerprint && !$isExpired ? 'disabled' : '' }}>
                                {{ $currentPollIndex == $totalPolls - 1 ? 'Finalizar Todo' : 'Siguiente Encuesta' }}
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (!$hasVoted && !$isLoadingFingerprint && $currentPoll && !$isExpired)
            <div class="mt-8 p-5 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-center gap-4">
                <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest leading-relaxed">
                    Protocolo de Seguridad: Debes confirmar tu voto antes de proceder a la siguiente fase de
                    participación.
                </p>
            </div>
        @elseif($isExpired && $currentPoll)
            <div class="mt-8 p-5 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-4">
                <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-red-500 uppercase tracking-widest leading-relaxed">
                    Alerta de Sesión: Esta encuesta ha finalizado su periodo de vigencia. Por favor, avanza a la
                    siguiente unidad disponible.
                </p>
            </div>
        @endif
    @else
        <!-- Final Summary -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 shadow-2xl overflow-hidden fade-in">
            <!-- Summary Header -->
            <div
                class="bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent px-10 py-12 border-b border-white/5 text-center">
                <div
                    class="w-20 h-20 bg-emerald-500/10 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 border border-emerald-500/20 shadow-xl shadow-emerald-500/10">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-black text-white mb-4 tracking-tight uppercase">¡Proceso Finalizado!</h2>
                <p class="text-gray-400 font-medium max-w-md mx-auto leading-relaxed">Has completado exitosamente todas
                    las encuestas activas en este ciclo de participación.</p>
            </div>

            <!-- Summary Stats -->
            <div class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div
                        class="bg-blue-500/5 rounded-3xl p-8 text-center border border-blue-500/10 group hover:bg-blue-500/10 transition-all">
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2">Unidades</p>
                        <p class="text-4xl font-black text-white">{{ $totalPolls }}</p>
                        <p class="text-[10px] font-bold text-blue-500/40 uppercase tracking-tight mt-1">Revisadas</p>
                    </div>
                    <div
                        class="bg-emerald-500/5 rounded-3xl p-8 text-center border border-emerald-500/10 group hover:bg-emerald-500/10 transition-all">
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Votos</p>
                        <p class="text-4xl font-black text-white">{{ $votedCount }}</p>
                        <p class="text-[10px] font-bold text-emerald-500/40 uppercase tracking-tight mt-1">Confirmados
                        </p>
                    </div>
                    <div
                        class="bg-purple-500/5 rounded-3xl p-8 text-center border border-purple-500/10 group hover:bg-purple-500/10 transition-all">
                        <p class="text-[10px] font-black text-purple-500 uppercase tracking-widest mb-2">Estatus</p>
                        <p class="text-4xl font-black text-white">100%</p>
                        <p class="text-[10px] font-bold text-purple-500/40 uppercase tracking-tight mt-1">Completado
                        </p>
                    </div>
                </div>

                @if ($votedCount > 0)
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-8 text-center">Tus
                        Participaciones Registradas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        @foreach ($completedSessions as $session)
                            <div
                                class="diagnostic-card bg-white/5 rounded-3xl p-8 border border-white/5 hover:bg-white/[0.08] transition-all group/card">
                                <div class="flex items-start justify-between gap-6 mb-6">
                                    <div class="flex-1 space-y-3">
                                        <h4
                                            class="text-lg font-black text-white tracking-tight uppercase leading-tight">
                                            {{ $session->poll->title }}</h4>
                                        <div
                                            class="flex items-center gap-2 text-[10px] font-black text-emerald-500/60 uppercase tracking-widest">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            REGISTRO: {{ $session->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if ($session->votes->isNotEmpty())
                                            <div class="p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                                <p
                                                    class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">
                                                    Opción: {{ $session->votes->first()->option->label }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div
                                        class="p-3 bg-white rounded-2xl shadow-2xl group-hover/card:scale-105 transition-transform">
                                        {!! $this->generateQRCode($session->uuid) !!}
                                    </div>
                                </div>
                                <button wire:click="showParticipationDetails({{ $session->id }})"
                                    class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500 bg-emerald-500/5 rounded-2xl hover:bg-emerald-500 hover:text-white transition-all border border-emerald-500/10">
                                    VER ESPECIFICACIONES TÉCNICAS
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Final Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    @if (Route::has('voting.asistent'))
                        <a href="{{ route('voting.asistent') }}"
                            class="group px-8 py-4 bg-gray-800 text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-700 hover:text-white transition-all border border-white/5 flex items-center gap-3">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            VOLVER AL CENTRO DE CONTROL
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Vote Confirmation Alert -->
    @if ($showVoteAlert)
        <div x-data="{ show: @entangle('showVoteAlert') }" x-show="show" x-cloak class="fixed inset-0 z-[100] overflow-y-auto"
            style="display: none;">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-xl transition-opacity" x-show="show"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <!-- Content -->
            <div class="flex items-center justify-center min-h-screen px-6 py-12">
                <div class="diagnostic-card bg-gray-900 border border-white/5 w-full max-w-xl rounded-[2.5rem] shadow-[0_0_100px_rgba(16,185,129,0.1)] overflow-hidden relative"
                    x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-8 scale-95" @click.stop>

                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] -mr-32 -mt-32"></div>

                    <!-- Header -->
                    <div
                        class="bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent px-10 py-10 border-b border-white/5 text-center">
                        <div
                            class="w-20 h-20 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-emerald-500/20 shadow-xl shadow-emerald-500/10 animate-bounce-subtle">
                            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-white tracking-tight uppercase">Voto Confirmado</h3>
                        <p class="text-[10px] font-black text-emerald-500/60 uppercase tracking-[0.2em] mt-2">Protocolo
                            de Participación Exitoso</p>
                    </div>

                    <!-- Body -->
                    <div class="p-10">
                        <div class="space-y-8">
                            <!-- Poll Info -->
                            <div class="space-y-4">
                                <h4
                                    class="text-lg font-bold text-white tracking-tight uppercase leading-tight text-center">
                                    {{ $voteAlertData['pollTitle'] ?? '' }}</h4>
                                <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-2xl p-4 text-center">
                                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">
                                        Tu Selección</p>
                                    <p class="text-sm font-black text-white uppercase">
                                        {{ $voteAlertData['selectedOption'] ?? '' }}</p>
                                </div>
                            </div>

                            <!-- Progress Hub -->
                            <div class="diagnostic-card bg-white/5 rounded-3xl p-8 border border-white/5">
                                <div class="flex justify-between items-center mb-6">
                                    <span
                                        class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Estatus
                                        del Ciclo</span>
                                    <span
                                        class="text-lg font-black text-white">{{ $voteAlertData['progressPercentage'] ?? 0 }}%</span>
                                </div>
                                <div
                                    class="relative w-full h-2 bg-white/5 rounded-full overflow-hidden border border-white/5 mb-6">
                                    <div class="absolute inset-y-0 left-0 bg-emerald-500 rounded-full transition-all duration-1000"
                                        style="width: {{ $voteAlertData['progressPercentage'] ?? 0 }}%"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <div class="text-lg font-black text-emerald-500">
                                            {{ $voteAlertData['completedPolls'] ?? 0 }}</div>
                                        <div class="text-[8px] font-black text-gray-600 uppercase tracking-widest">
                                            Listas</div>
                                    </div>
                                    <div class="text-center border-x border-white/5">
                                        <div class="text-lg font-black text-blue-500">
                                            {{ $voteAlertData['remainingPolls'] ?? 0 }}</div>
                                        <div class="text-[8px] font-black text-gray-600 uppercase tracking-widest">
                                            Pendientes</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-black text-purple-500">
                                            {{ $voteAlertData['totalPolls'] ?? 0 }}</div>
                                        <div class="text-[8px] font-black text-gray-600 uppercase tracking-widest">
                                            Total</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Message -->
                            @if (isset($voteAlertData['isLastPoll']) && $voteAlertData['isLastPoll'])
                                <div
                                    class="bg-purple-500/10 border border-purple-500/20 rounded-2xl p-6 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p
                                        class="text-xs font-black text-purple-400 uppercase tracking-widest leading-relaxed">
                                        Fase Final: Haz completado el 100% de las encuestas activas.
                                    </p>
                                </div>
                            @else
                                <div
                                    class="bg-blue-500/5 border border-blue-500/10 rounded-2xl p-6 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p
                                        class="text-xs font-black text-blue-400 uppercase tracking-widest leading-relaxed">
                                        Continuar: Quedan <strong>{{ $voteAlertData['remainingPolls'] ?? 0 }}
                                            unidades</strong> pendientes en este ciclo.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-white/[0.02] px-10 py-10 border-t border-white/5">
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if (isset($voteAlertData['isLastPoll']) && $voteAlertData['isLastPoll'])
                                <button wire:click="continueToNextPoll"
                                    class="w-full py-5 bg-purple-600 hover:bg-purple-700 text-white font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl transition-all shadow-2xl shadow-purple-500/20">
                                    GENERAR REPORTE FINAL
                                </button>
                            @else
                                <button wire:click="closeVoteAlert"
                                    class="flex-1 py-5 bg-gray-800 text-gray-400 font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl hover:bg-gray-700 hover:text-white transition-all border border-white/5">
                                    REVISAR
                                </button>
                                <button wire:click="continueToNextPoll"
                                    class="flex-[2] py-5 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl transition-all shadow-2xl shadow-emerald-500/20">
                                    SIGUIENTE UNIDAD
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Incluir el modal de participación -->
    @include('livewire.modals.participation')

</div>

@section('script')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Iniciando sistema avanzado de detección de IP...');

            let fingerprintGenerated = false;
            let detectedIPs = new Set();

            // Función mejorada para generar fingerprint
            function generateAdvancedFingerprint() {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    ctx.textBaseline = 'top';
                    ctx.font = '14px Arial';
                    ctx.fillText('Fingerprint Canvas', 2, 2);

                    const webglCanvas = document.createElement('canvas');
                    const gl = webglCanvas.getContext('webgl') || webglCanvas.getContext('experimental-webgl');
                    let webglInfo = 'no-webgl';
                    if (gl) {
                        const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                        if (debugInfo) {
                            webglInfo = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                        }
                    }

                    const fingerprint = [
                        navigator.userAgent || 'unknown',
                        navigator.language || 'unknown',
                        navigator.languages ? navigator.languages.join(',') : 'unknown',
                        screen.width + 'x' + screen.height + 'x' + screen.colorDepth,
                        new Date().getTimezoneOffset(),
                        canvas.toDataURL(),
                        navigator.hardwareConcurrency || 'unknown',
                        navigator.deviceMemory || 'unknown',
                        navigator.platform || 'unknown',
                        navigator.cookieEnabled ? 'cookies-enabled' : 'cookies-disabled',
                        navigator.doNotTrack || 'unknown',
                        webglInfo,
                        window.screen.availWidth + 'x' + window.screen.availHeight,
                        Date.now().toString()
                    ].join('|');

                    return btoa(fingerprint).replace(/[^a-zA-Z0-9]/g, '').substring(0, 32);
                } catch (error) {
                    console.error('❌ Error generando fingerprint avanzado:', error);
                    return 'fallback_' + Math.random().toString(36).substring(2, 15);
                }
            }

            // Función para verificar si una IP es privada
            function isPrivateIP(ip) {
                const parts = ip.split('.').map(Number);

                return (
                    // 10.0.0.0/8
                    (parts[0] === 10) ||
                    // 172.16.0.0/12
                    (parts[0] === 172 && parts[1] >= 16 && parts[1] <= 31) ||
                    // 192.168.0.0/16
                    (parts[0] === 192 && parts[1] === 168) ||
                    // 169.254.0.0/16 (APIPA)
                    (parts[0] === 169 && parts[1] === 254) ||
                    // 127.0.0.0/8 (Loopback)
                    (parts[0] === 127)
                );
            }

            // Método 1: WebRTC con múltiples servidores STUN
            function detectPrivateIPWebRTC() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 1: Detectando IP via WebRTC...');

                    try {
                        const stunServers = [
                            'stun:stun.l.google.com:19302',
                            'stun:stun1.l.google.com:19302',
                            'stun:stun2.l.google.com:19302',
                            'stun:stun3.l.google.com:19302',
                            'stun:stun4.l.google.com:19302',
                            'stun:stun.ekiga.net',
                            'stun:stun.ideasip.com',
                            'stun:stun.rixtelecom.se',
                            'stun:stun.schlund.de',
                            'stun:stunserver.org',
                            'stun:stun.softjoys.com',
                            'stun:stun.voiparound.com',
                            'stun:stun.voipbuster.com'
                        ];

                        const pc = new RTCPeerConnection({
                            iceServers: stunServers.map(url => ({
                                urls: url
                            }))
                        });

                        // Crear canal de datos
                        pc.createDataChannel('ip-detection', {
                            ordered: true
                        });

                        // Crear oferta
                        pc.createOffer()
                            .then(offer => {
                                return pc.setLocalDescription(offer);
                            })
                            .then(() => {
                                console.log('✅ Oferta WebRTC creada exitosamente');
                            })
                            .catch(error => {
                                console.error('❌ Error creando oferta WebRTC:', error);
                                resolve(null);
                            });

                        // Escuchar candidatos ICE
                        pc.onicecandidate = (event) => {
                            if (!event.candidate) return;

                            const candidate = event.candidate.candidate;
                            console.log('🔍 Candidato ICE:', candidate);

                            // Buscar IPs en el candidato
                            const ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3})/g;
                            let match;

                            while ((match = ipRegex.exec(candidate)) !== null) {
                                const ip = match[1];
                                console.log('🎯 IP encontrada:', ip);

                                detectedIPs.add(ip);

                                // Verificar si es IP privada
                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada detectada:', ip);
                                    pc.close();
                                    resolve(ip);
                                    return;
                                }
                            }
                        };

                        // Timeout más largo para WebRTC
                        setTimeout(() => {
                            console.log('⏰ Timeout WebRTC alcanzado');
                            pc.close();

                            // Buscar cualquier IP privada detectada
                            for (const ip of detectedIPs) {
                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada encontrada en timeout:', ip);
                                    resolve(ip);
                                    return;
                                }
                            }

                            resolve(null);
                        }, 8000);

                    } catch (error) {
                        console.error('❌ Error en WebRTC:', error);
                        resolve(null);
                    }
                });
            }

            // Método 2: Detección via conexión local
            function detectPrivateIPLocal() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 2: Detectando IP via conexión local...');

                    try {
                        const pc = new RTCPeerConnection({
                            iceServers: []
                        });

                        pc.createDataChannel('local-ip');

                        pc.createOffer()
                            .then(offer => {
                                // Modificar SDP para forzar detección local
                                const modifiedSDP = offer.sdp.replace(/a=ice-options:trickle\s\n/g, '');
                                return pc.setLocalDescription({
                                    type: offer.type,
                                    sdp: modifiedSDP
                                });
                            })
                            .catch(() => resolve(null));

                        pc.onicecandidate = (event) => {
                            if (!event.candidate) return;

                            const candidate = event.candidate.candidate;
                            const ipMatch = candidate.match(/([0-9]{1,3}(\.[0-9]{1,3}){3})/);

                            if (ipMatch) {
                                const ip = ipMatch[1];
                                console.log('🎯 IP local encontrada:', ip);

                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada local detectada:', ip);
                                    pc.close();
                                    resolve(ip);
                                }
                            }
                        };

                        setTimeout(() => {
                            pc.close();
                            resolve(null);
                        }, 5000);

                    } catch (error) {
                        console.error('❌ Error en detección local:', error);
                        resolve(null);
                    }
                });
            }

            // Método 3: Detección via múltiples conexiones
            function detectPrivateIPMultiple() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 3: Detectando IP via múltiples conexiones...');

                    const connections = [];
                    const foundIPs = new Set();
                    let resolved = false;

                    try {
                        // Crear múltiples conexiones
                        for (let i = 0; i < 3; i++) {
                            const pc = new RTCPeerConnection({
                                iceServers: [{
                                        urls: 'stun:stun.l.google.com:19302'
                                    },
                                    {
                                        urls: 'stun:stun1.l.google.com:19302'
                                    }
                                ]
                            });

                            connections.push(pc);
                            pc.createDataChannel(`channel-${i}`);

                            pc.createOffer()
                                .then(offer => pc.setLocalDescription(offer))
                                .catch(() => {});

                            pc.onicecandidate = (event) => {
                                if (!event.candidate || resolved) return;

                                const candidate = event.candidate.candidate;
                                const ipMatch = candidate.match(/([0-9]{1,3}(\.[0-9]{1,3}){3})/);

                                if (ipMatch) {
                                    const ip = ipMatch[1];
                                    foundIPs.add(ip);

                                    if (isPrivateIP(ip) && !resolved) {
                                        resolved = true;
                                        console.log('✅ IP privada múltiple detectada:', ip);
                                        connections.forEach(conn => conn.close());
                                        resolve(ip);
                                    }
                                }
                            };
                        }

                        setTimeout(() => {
                            if (!resolved) {
                                connections.forEach(conn => conn.close());

                                // Buscar cualquier IP privada encontrada
                                for (const ip of foundIPs) {
                                    if (isPrivateIP(ip)) {
                                        console.log('✅ IP privada encontrada en múltiples:', ip);
                                        resolve(ip);
                                        return;
                                    }
                                }

                                resolve(null);
                            }
                        }, 6000);

                    } catch (error) {
                        console.error('❌ Error en detección múltiple:', error);
                        resolve(null);
                    }
                });
            }

            // Función principal para detectar IP privada con múltiples métodos
            async function detectPrivateIPAdvanced() {
                console.log('🚀 Iniciando detección avanzada de IP privada...');

                const methods = [
                    detectPrivateIPWebRTC,
                    detectPrivateIPLocal,
                    detectPrivateIPMultiple
                ];

                // Intentar todos los métodos en paralelo
                const results = await Promise.allSettled(
                    methods.map(method => method())
                );

                // Buscar el primer resultado exitoso
                for (const result of results) {
                    if (result.status === 'fulfilled' && result.value) {
                        console.log('✅ IP privada detectada exitosamente:', result.value);
                        return result.value;
                    }
                }

                // Si no se encontró IP privada, usar la primera IP encontrada
                if (detectedIPs.size > 0) {
                    const firstIP = Array.from(detectedIPs)[0];
                    console.log('⚠️ Usando primera IP detectada:', firstIP);
                    return firstIP;
                }

                console.log('❌ No se pudo detectar ninguna IP');
                return null;
            }

            // Función principal para enviar fingerprint
            async function sendFingerprintToLivewire() {
                if (fingerprintGenerated) return;

                console.log('🔧 Generando fingerprint avanzado...');

                const fingerprint = generateAdvancedFingerprint();
                console.log('✅ Fingerprint generado:', fingerprint.substring(0, 8) + '...');

                const privateIP = await detectPrivateIPAdvanced();
                console.log('🌐 Resultado final - IP privada:', privateIP || 'No detectada');

                fingerprintGenerated = true;

                // Enviar a Livewire
                const livewireComponent = document.querySelector('[wire\\:id]');
                if (livewireComponent) {
                    const wireId = livewireComponent.getAttribute('wire:id');
                    console.log('📡 Enviando datos a Livewire:', wireId);

                    if (window.Livewire) {
                        try {
                            if (window.Livewire.find) {
                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    component.call('handleFingerprintData', fingerprint, privateIP);
                                    console.log('✅ Datos enviados via Livewire.find');
                                    return;
                                }
                            }

                            if (window.Livewire.dispatch) {
                                window.Livewire.dispatch('setDeviceFingerprint', [fingerprint, privateIP]);
                                console.log('✅ Datos enviados via Livewire.dispatch');
                                return;
                            }

                            window.dispatchEvent(new CustomEvent('setDeviceFingerprint', {
                                detail: [fingerprint, privateIP]
                            }));
                            console.log('✅ Datos enviados via CustomEvent');

                        } catch (error) {
                            console.error('❌ Error enviando a Livewire:', error);
                        }
                    } else {
                        console.error('❌ Livewire no está disponible');
                    }
                } else {
                    console.error('❌ Componente Livewire no encontrado');
                }
            }

            // Inicializar detección
            setTimeout(sendFingerprintToLivewire, 1000);

            // Reintentar si no se ha completado
            setTimeout(() => {
                if (!fingerprintGenerated) {
                    console.log('🔄 Reintentando detección...');
                    fingerprintGenerated = false;
                    detectedIPs.clear();
                    sendFingerprintToLivewire();
                }
            }, 10000);

            // Listener para cuando Livewire esté listo
            document.addEventListener('livewire:load', function() {
                console.log('⚡ Livewire cargado');
                if (!fingerprintGenerated) {
                    setTimeout(sendFingerprintToLivewire, 500);
                }
            });

            // Debug: Mostrar todas las IPs detectadas
            setTimeout(() => {
                if (detectedIPs.size > 0) {
                    console.log('📊 Todas las IPs detectadas:', Array.from(detectedIPs));
                }
            }, 12000);
        });
    </script>
@endsection
