<div class="max-w-3xl mx-auto py-8 px-4 space-y-8">

    {{-- Header --}}
    <header class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
            {{ $activity->pevaluacion->pensum->asignatura->name ?? 'Asignatura' }}
        </p>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $activity->topic ?? 'Actividad' }}
        </h1>
        @if($activity->description)
        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">{{ $activity->description }}</p>
        @endif
        <p class="mt-1 text-xs text-gray-400">
            {{ optional($activity->finicial)->format('d/m/Y') }} – {{ optional($activity->ffinal)->format('d/m/Y') }}
        </p>
    </header>

    {{-- Secciones de contenido --}}
    @foreach($sections as $section)
    <section wire:key="section-{{ $section->id }}" class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            {{ $section->title }}
        </h2>

        @foreach($section->visibleContents as $content)
        <div wire:key="content-{{ $content->id }}">
            @if($content->title)
            <h3 class="text-base font-medium text-gray-700 dark:text-gray-200 mb-2">{{ $content->title }}</h3>
            @endif

            @switch($content->type)
                @case('TEXT')
                    <div class="prose dark:prose-invert max-w-none text-sm">{!! $content->body !!}</div>
                    @break
                @case('VIDEO')
                    @if($content->media?->isLocal())
                        <video controls class="w-full rounded-xl" preload="metadata">
                            <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                        </video>
                    @elseif($content->media?->provider === 'YOUTUBE')
                        @php preg_match('/[?&]v=([^&]+)/', $content->media->external_url ?? '', $m); $vid = $m[1] ?? ''; @endphp
                        @if($vid)
                        <div class="aspect-video rounded-xl overflow-hidden">
                            <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                    class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        </div>
                        @endif
                    @endif
                    @break
                @case('IMAGE')
                    <img src="{{ $content->media?->public_url }}"
                         alt="{{ $content->title }}" class="rounded-xl max-w-full"/>
                    @break
                @case('EMBED')
                    <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        {!! $content->body !!}
                    </div>
                    @break
                @case('FILE_PREVIEW')
                    @if($content->media)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden" style="height: 600px;">
                        <iframe src="{{ $content->media->public_url }}" class="w-full h-full" loading="lazy"></iframe>
                    </div>
                    @endif
                    @break
                @case('AUDIO')
                    @if($content->media)
                    <audio controls class="w-full" preload="metadata">
                        <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                    </audio>
                    @endif
                    @break
                @case('HTML')
                    <div class="prose dark:prose-invert max-w-none">{!! $content->body !!}</div>
                    @break
                @default
                    @if($content->body)
                    <div class="prose dark:prose-invert max-w-none text-sm">{!! $content->body !!}</div>
                    @endif
            @endswitch
        </div>
        @endforeach
    </section>
    @endforeach

    {{-- Recursos descargables --}}
    @if($resources->count())
    <section class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">
            Recursos
        </h2>
        <ul class="space-y-2">
            @foreach($resources as $resource)
            <li class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                            {{ $resource->display_name }}
                        </p>
                        @if($resource->description)
                        <p class="text-xs text-gray-400 truncate">{{ $resource->description }}</p>
                        @endif
                    </div>
                </div>
                @if($activity->lmsPublication->allow_downloads)
                <a href="{{ route('student.lms.resource.download', $resource) }}"
                   class="shrink-0 ml-3 text-xs px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors">
                    Descargar
                </a>
                @endif
            </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- Enlaces externos --}}
    @if($links->count())
    <section class="space-y-2">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            Referencias y enlaces
        </h2>
        @foreach($links as $link)
        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg
                  hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-blue-500 truncate">
                    {{ $link->title }}
                </p>
                @if($link->description)
                <p class="text-xs text-gray-400 truncate">{{ $link->description }}</p>
                @endif
            </div>
        </a>
        @endforeach
    </section>
    @endif

    {{-- Volver --}}
    <div class="pt-4">
        <a href="{{ route('student.lms.home') }}"
           class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">
            ← Volver a mis actividades
        </a>
    </div>
</div>
