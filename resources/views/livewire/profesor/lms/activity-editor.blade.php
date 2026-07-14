<div class="w-full mx-auto py-8 px-4 space-y-6">

    {{-- Header con estado de publicación --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('app.profesors.lms.lesson.wizard') }}"
               class="p-2 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-lg transition-all"
               title="Volver al asistente">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-semibold text-white">
                    {{ $activity->topic ?? 'Actividad sin título' }}
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $activity->pevaluacion->pensum->asignatura->name ?? '' }}
                    · {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                </p>
            </div>
        </div>

        @php $pub = $activity->lmsPublication; @endphp
        <span @class([
            'px-3 py-1 rounded-full text-xs font-medium',
            'bg-emerald-500/15 text-emerald-400' => $pub?->status === 'PUBLISHED',
            'bg-amber-500/15 text-amber-400'     => $pub?->status === 'SCHEDULED',
            'bg-slate-500/15 text-slate-400'     => !$pub || $pub->status === 'DRAFT',
            'bg-red-500/15 text-red-400'         => $pub?->status === 'ARCHIVED',
        ])>
            {{ $pub ? match($pub->status) {
                'PUBLISHED' => 'Publicado',
                'SCHEDULED' => 'Programado',
                'ARCHIVED'  => 'Archivado',
                default     => 'Borrador',
            } : 'Sin publicar' }}
        </span>
    </div>

    {{-- Secciones de contenido --}}
    <section class="space-y-4">
        <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
            Contenido de la lección
        </h2>

        @foreach($sections as $section)
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden"
             wire:key="section-{{ $section['id'] }}">
            <div class="flex items-center justify-between px-4 py-3 bg-slate-700/30">
                <span class="font-medium text-slate-200">{{ $section['title'] }}</span>
                <div class="flex gap-2">
                    <button wire:click="toggleSectionVisibility({{ $section['id'] }})"
                            class="text-xs px-2 py-1 rounded
                                   {{ $section['is_visible'] ? 'text-emerald-400' : 'text-slate-500' }}">
                        {{ $section['is_visible'] ? 'Visible' : 'Oculto' }}
                    </button>
                    <button wire:click="$set('editingSectionId', {{ $section['id'] }})"
                            class="text-xs text-slate-400 hover:text-white">
                        + Bloque
                    </button>
                    <button wire:click="deleteSection({{ $section['id'] }})"
                            wire:confirm="¿Eliminar esta sección y todo su contenido?"
                            class="text-xs text-red-400 hover:text-red-300">
                        Eliminar
                    </button>
                </div>
            </div>

            <div class="divide-y divide-slate-700/50">
                @foreach($section['contents'] as $content)
                <div class="px-4 py-3 flex items-start gap-3"
                     wire:key="content-{{ $content['id'] }}">
                    <span class="mt-0.5 text-xs px-2 py-0.5 rounded bg-slate-700 text-slate-300 shrink-0">
                        {{ $content['type'] }}
                    </span>
                    <div class="min-w-0">
                        @if($content['title'])
                        <p class="text-sm font-medium text-slate-200">{{ $content['title'] }}</p>
                        @endif
                        @if($content['type'] === 'TEXT')
                        <p class="text-sm text-slate-400 truncate">
                            {{ \Illuminate\Support\Str::limit(strip_tags($content['body'] ?? ''), 80) }}
                        </p>
                        @elseif($content['media'])
                        <p class="text-sm text-slate-400">{{ $content['media']['original_name'] ?? '' }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($editingSectionId === $section['id'])
            <div class="px-4 py-3 bg-slate-900/30 border-t border-slate-700"
                 x-data="{ tab: 'text' }">
                <div class="flex gap-2 mb-3">
                    <button @click="tab='text'"
                            :class="tab==='text' ? 'text-emerald-400 border-emerald-400' : 'text-slate-400 border-transparent'"
                            class="text-xs pb-1 border-b-2 transition-colors">
                        Texto
                    </button>
                    <button @click="tab='file'"
                            :class="tab==='file' ? 'text-emerald-400 border-emerald-400' : 'text-slate-400 border-transparent'"
                            class="text-xs pb-1 border-b-2 transition-colors">
                        Archivo
                    </button>
                </div>

                <div x-show="tab === 'text'" class="space-y-2">
                    <input wire:model="contentTitle"
                           placeholder="Título del bloque (opcional)"
                           class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                                  text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                    <textarea wire:model="contentBody" rows="4"
                              placeholder="Escribe el contenido de este bloque…"
                              class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                                     text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </textarea>
                    <div class="flex gap-2">
                        <button wire:click="addTextContent({{ $section['id'] }})"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                            Agregar bloque
                        </button>
                        <button wire:click="$set('editingSectionId', null)"
                                class="px-3 py-1.5 text-slate-400 hover:text-white text-xs">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach

        <div class="flex gap-2">
            <input wire:model="newSectionTitle"
                   wire:keydown.enter="addSection"
                   placeholder="Nueva sección (ej: Introducción)…"
                   class="flex-1 bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <button wire:click="addSection"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg">
                + Sección
            </button>
        </div>
    </section>

    {{-- Recursos descargables --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
                Recursos descargables
            </h2>
            <button wire:click="$toggle('showResourceForm')"
                    class="text-xs text-emerald-400 hover:text-emerald-300">
                {{ $showResourceForm ? 'Cancelar' : '+ Recurso' }}
            </button>
        </div>

        @foreach($activity->lmsResources()->where('is_visible', true)->with('media')->get() as $res)
        <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-slate-200">{{ $res->display_name }}</span>
                <span class="text-xs text-slate-500">{{ $res->media?->size_for_humans }}</span>
            </div>
            <button wire:click="deleteResource({{ $res->id }})"
                    wire:confirm="¿Eliminar este recurso?"
                    class="text-xs text-red-400 hover:text-red-300">Eliminar</button>
        </div>
        @endforeach

        @if($showResourceForm)
        <div class="space-y-2 pt-2 border-t border-slate-700/50">
            <input wire:model="resourceName" placeholder="Nombre del recurso"
                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <input wire:model="resourceFile" type="file"
                   class="block w-full text-sm text-slate-400
                          file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                          file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600"/>
            <button wire:click="uploadResource"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                Subir recurso
            </button>
        </div>
        @endif
    </section>

    {{-- Enlaces externos --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
                Enlaces externos
            </h2>
            <button wire:click="$toggle('showLinkForm')"
                    class="text-xs text-emerald-400 hover:text-emerald-300">
                {{ $showLinkForm ? 'Cancelar' : '+ Enlace' }}
            </button>
        </div>

        @foreach($activity->lmsLinks()->where('is_visible', true)->get() as $link)
        <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span class="text-sm text-slate-200 truncate">{{ $link->title }}</span>
                <span class="text-xs text-slate-500">{{ $link->link_type }}</span>
            </div>
            <button wire:click="deleteLink({{ $link->id }})"
                    wire:confirm="¿Eliminar este enlace?"
                    class="text-xs text-red-400 hover:text-red-300">Eliminar</button>
        </div>
        @endforeach

        @if($showLinkForm)
        <div class="space-y-2 pt-2 border-t border-slate-700/50">
            <input wire:model="linkTitle" placeholder="Título del enlace"
                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <input wire:model="linkUrl" placeholder="URL (https://…)"
                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <div class="flex gap-2">
                <select wire:model="linkType"
                        class="bg-slate-800 border border-slate-600 text-slate-200 rounded-lg px-3 py-1.5 text-sm">
                    <option value="REFERENCE">Referencia</option>
                    <option value="VIDEO">Video</option>
                    <option value="TOOL">Herramienta</option>
                    <option value="DOCUMENT">Documento</option>
                    <option value="OTHER">Otro</option>
                </select>
                <button wire:click="addLink"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                    Agregar enlace
                </button>
            </div>
        </div>
        @endif
    </section>

    {{-- Publicación --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 space-y-3">
        <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">Publicación</h2>
        <div class="flex items-center gap-3">
            <label class="text-sm text-slate-300">Publicar el:</label>
            <input wire:model="publishAt" type="datetime-local"
                   class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-1.5 text-sm text-slate-200
                          focus:border-emerald-500 focus:outline-none"/>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
            <input wire:model="allowDownloads" type="checkbox"
                   class="rounded border-slate-600 bg-slate-800 text-emerald-500"/>
            Permitir descarga de recursos
        </label>
        <button wire:click="publishActivity"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg font-medium">
            {{ $pubStatus === 'PUBLISHED' ? 'Actualizar publicación' : 'Publicar actividad' }}
        </button>
    </section>
</div>
