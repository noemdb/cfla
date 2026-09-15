@props(['entry'])

@php
    $severity = $entry->event_severity;

    [$iconBg, $iconPath] = match ($severity) {
        'critical' => [
            'bg-red-500/15 text-red-400',
            'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
        ],
        'alert' => [
            'bg-orange-500/15 text-orange-400',
            'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0',
        ],
        'warning' => [
            'bg-yellow-500/15 text-yellow-400',
            'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ],
        'debug' => [
            'bg-gray-500/15 text-gray-300',
            'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
        ],
        default => [
            'bg-emerald-500/15 text-emerald-400',
            'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        ],
    };

    $userLabel = $entry->subject_identifier
        ?: ($entry->subject_type ? class_basename($entry->subject_type).($entry->subject_id ? ' #'.$entry->subject_id : '') : 'Sistema');

    $objectLabel = $entry->object_identifier
        ?: ($entry->object_type ? class_basename($entry->object_type).($entry->object_id ? ' #'.$entry->object_id : '') : null);

    $metadata = is_array($entry->metadata) ? $entry->metadata : [];
@endphp

{{-- Tarjeta de evento: flex-col + overflow-hidden + footer con mt-auto.
     Sin <a> envolvente (evita enlaces anidados inválidos) y con padding por slot. --}}
<article {{ $attributes->class(['flex h-full flex-col overflow-hidden rounded-xl border border-white/10 bg-gray-900/60 shadow-sm backdrop-blur-md transition-colors hover:border-white/20']) }}>
    <header class="flex items-start gap-3 border-b border-white/5 px-4 py-3">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $iconBg }}" aria-hidden="true">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
            </svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-gray-100">{{ $entry->title }}</p>
            <p class="mt-0.5 text-[11px] text-gray-500">
                {{ $entry->created_at?->format('H:i:s') }}
                <span class="mx-1 text-gray-600">·</span>
                <span class="font-mono">{{ $entry->event_type }}</span>
            </p>
        </div>
        <x-binnacle.badge :value="$severity" kind="severity" />
    </header>

    <div class="flex-1 space-y-3 px-4 py-3">
        <div class="flex flex-wrap items-center gap-1.5">
            <x-binnacle.badge :value="$entry->event_category" kind="category" />
        </div>

        {{-- Meta: usuario / objeto --}}
        <dl class="grid grid-cols-1 gap-x-4 gap-y-2 text-[11px] sm:grid-cols-2">
            <div class="min-w-0">
                <dt class="font-semibold uppercase tracking-wider text-gray-500">Usuario</dt>
                <dd class="mt-0.5 flex items-center gap-1.5 text-gray-200">
                    <svg class="h-3.5 w-3.5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="truncate">{{ $userLabel }}</span>
                </dd>
            </div>
            @if($objectLabel)
                <div class="min-w-0">
                    <dt class="font-semibold uppercase tracking-wider text-gray-500">Objeto</dt>
                    <dd class="mt-0.5 flex items-center gap-1.5 text-gray-200">
                        <svg class="h-3.5 w-3.5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="truncate">{{ $objectLabel }}</span>
                    </dd>
                </div>
            @endif
        </dl>

        @if($entry->description)
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Descripción</p>
                <p class="mt-0.5 text-sm text-gray-300">{{ $entry->description }}</p>
            </div>
        @endif

        @if($entry->event_type === 'model_updated' && $entry->changed_fields)
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Campos modificados</p>
                <div class="mt-1 flex flex-wrap gap-1.5">
                    @foreach($entry->changed_fields as $field)
                        <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-2 py-0.5 font-mono text-[11px] text-gray-400">{{ $field }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $scalarMetadata = collect($metadata)->filter(fn ($v) => is_scalar($v) || is_null($v))->take(6);
        @endphp
        @if($scalarMetadata->isNotEmpty())
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Metadata</p>
                <div class="mt-1 flex flex-wrap gap-1.5">
                    @foreach($scalarMetadata as $key => $value)
                        <span class="inline-flex items-center gap-1 rounded-full border border-white/10 bg-white/5 px-2 py-0.5 font-mono text-[10px] text-gray-400">
                            <span class="text-gray-500">{{ $key }}:</span>
                            <span>{{ is_bool($value) ? ($value ? 'sí' : 'no') : ($value ?? '—') }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <footer class="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-white/5 px-4 py-2 font-mono text-[11px] text-gray-500">
        @if($entry->ip_address)
            <span class="inline-flex items-center gap-1">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ $entry->ip_address }}
            </span>
        @endif
        @if($entry->request_method)
            <span class="break-all">{{ $entry->request_method }} {{ \Illuminate\Support\Str::limit((string) $entry->request_url, 48) }}</span>
        @endif
        @if($entry->request_id)<span>req: {{ $entry->request_id }}</span>@endif
        @if($entry->session_id)<span>sesión: {{ \Illuminate\Support\Str::limit((string) $entry->session_id, 12) }}</span>@endif
    </footer>
</article>
