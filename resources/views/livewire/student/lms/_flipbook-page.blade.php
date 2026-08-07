@props([
    'section' => null,
    'paginationInfo' => null
])

@php
    // Variables de acento replicadas de activity-view.blade.php (design §6.3):
    // auto-contenido por página, evita acoplar a _content-renderer.
    $sectionUpper = mb_strtoupper($section->title ?? '');
    $accentColor = 'mint';
    $accentDot = 'bg-emerald-500';
    $accentRing = 'ring-emerald-500/20';
    $badgeLabel = null;
    $badgeClass = '';

    if (preg_match('/\b(INICIO|INTRODUCCI[OÓ]N|APERTURA|BIENVENIDA|PRESENTACI[OÓ]N)\b/', $sectionUpper)) {
        $badgeLabel = 'INICIO';
        $badgeClass = 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20';
        $accentColor = 'blue';
        $accentDot = 'bg-blue-500';
        $accentRing = 'ring-blue-500/20';
    } elseif (preg_match('/\b(DESARROLLO|ACTIVIDAD|CONTENIDO|EXPLICACI[OÓ]N|EJERCICIO|PR[AÁ]CTICA|AN[AÁ]LISIS|PROFUNDIZACI[OÓ]N|REFLEXI[OÓ]N|LECTURA|TEMA)\b/', $sectionUpper)) {
        $badgeLabel = 'DESARROLLO';
        $badgeClass = 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30';
        $accentColor = 'mint';
        $accentDot = 'bg-emerald-500';
        $accentRing = 'ring-emerald-500/20';
    } elseif (preg_match('/\b(CIERRE|CONCLUSI[OÓ]N|RESUMEN|EVALUACI[OÓ]N|REPASO|S[IÍ]NTESIS|FINAL|RETROALIMENTACI[OÓ]N)\b/', $sectionUpper)) {
        $badgeLabel = 'CIERRE';
        $badgeClass = 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20';
        $accentColor = 'amber';
        $accentDot = 'bg-amber-500';
        $accentRing = 'ring-amber-500/20';
    }
@endphp

@php
    // Variables de paginación: extraer información del chunk actual
    $isFirstChunk = $paginationInfo['isFirstChunk'] ?? true;
    $isLastChunk = $paginationInfo['isLastChunk'] ?? true;
    $contents = $paginationInfo['contents'] ?? $section->visibleContents;
    $chunkIndex = $paginationInfo['chunkIndex'] ?? 0;
    $totalChunks = $paginationInfo['totalChunks'] ?? 1;
@endphp

<div class="stf__item">
    @php
        // Página izquierda del pliego → el lomo queda a la derecha; página derecha → lomo a la izquierda.
        $isLeftPage = ($loop->index % 2 === 0);
    @endphp
    <div class="relative flex flex-col h-full p-4 sm:p-6 md:p-8 bg-[#fcfaf4] dark:bg-[#23211b]">

        {{-- Sombra del lomo: da profundidad de libro abierto sobre el pliegue central --}}
        <span aria-hidden="true"
              class="pointer-events-none absolute inset-y-0 w-1/3 {{ $isLeftPage ? 'right-0 bg-[linear-gradient(to_left,rgba(31,28,20,0.12),transparent_85%)]' : 'left-0 bg-[linear-gradient(to_right,rgba(31,28,20,0.12),transparent_85%)]' }}"></span>

        {{-- Section header (misma identidad visual que el scroll) --}}
        <div class="flex items-center gap-1.5 pb-2 border-b border-emerald-200 dark:border-gray-700/40">
            <span class="w-0.5 h-4 rounded-full {{ $accentDot }} shrink-0"></span>
            <h2 class="text-sm sm:text-[13px] font-display font-bold text-gray-900 dark:text-white flex-1 min-w-0 leading-snug">
                {{ $section->title }}
                @if(!$isFirstChunk)
                    <span class="ml-2 continuation-indicator">(Continúa)</span>
                @endif
            </h2>
            @if($badgeLabel)
                <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0 rounded-full text-[9px] font-semibold uppercase tracking-wider {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            @endif
        </div>

        {{-- Contenido de la sección (paginado), vía partial compartido en mode='book' --}}
        <div class="mt-2 space-y-1 flex-1 min-h-0 overflow-y-auto">
            @foreach($contents as $idx => $content)
                @include('livewire.student.lms._content-renderer', [
                    'content' => $content,
                    'mode' => 'book',
                    'stepNum' => $idx + 1,
                    'isLast' => $loop->last,
                    'sectionId' => $section->id,
                ])
            @endforeach
        </div>

        {{-- Pie de página: el @include hereda $loop del @foreach($sections) de activity-view --}}
        <div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-700/40 flex items-center justify-between text-[10px] font-semibold text-gray-400 dark:text-gray-500">
            <div class="flex items-center space-x-2">
                <span>Página {{ $loop->iteration }} de {{ $loop->count }}</span>
                @if(!$isLastChunk)
                    <span class="continuation-indicator">(Continúa)</span>
                @endif
            </div>
            <span class="uppercase tracking-wider">{{ $accentColor }}</span>
        </div>
    </div>
</div>
