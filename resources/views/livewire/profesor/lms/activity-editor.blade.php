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
        <div class="bg-slate-800/50 border border-slate-700 rounded-lg overflow-hidden"
             wire:key="section-{{ $section['id'] }}">
            <div class="flex items-center justify-between px-4 py-2 bg-slate-700/30">
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
                <div class="px-4 py-2 flex items-start gap-3"
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
            <div class="px-4 py-2 bg-slate-900/30 border-t border-slate-700"
                 x-data="{ tab: 'text' }">
                <div class="flex gap-2 mb-2">
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
    <section class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 space-y-3">
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
    <section class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 space-y-3">
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
    <section class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 space-y-3">
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

    {{-- ─── Comentarios de estudiantes ──────────────────────────────── --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 space-y-3">
        <div class="flex items-center gap-4 mb-2">
            <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
                Comentarios de estudiantes
            </h2>
            <div class="flex gap-1">
                <button wire:click="$set('commentsTab', 'pending')"
                        class="px-3 py-1 text-xs font-medium rounded-lg transition-colors
                               {{ $commentsTab === 'pending' ? 'bg-amber-500/10 text-amber-400' : 'text-slate-400 hover:text-slate-300' }}">
                    Pendientes
                </button>
                <button wire:click="$set('commentsTab', 'approved')"
                        class="px-3 py-1 text-xs font-medium rounded-lg transition-colors
                               {{ $commentsTab === 'approved' ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:text-slate-300' }}">
                    Aprobados
                </button>
            </div>
        </div>

        @forelse($activityComments as $comment)
            <div wire:key="ac-{{ $comment->id }}"
                 class="flex gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700/50">
                <x-lms.user-avatar :user="$comment->user" size="md" ring="ring-2 ring-slate-600/60" fallback="bg-slate-700 text-slate-300" />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-200">
                            {{ $comment->user?->profile?->firstname ?? $comment->user?->name ?? '—' }}
                        </span>
                        <span class="text-[10px] text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->replies->isNotEmpty())
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 text-[10px] font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                {{ $comment->replies->count() }} {{ $comment->replies->count() === 1 ? 'respuesta' : 'respuestas' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-300 mt-1">{{ $comment->body }}</p>

                    {{-- Réplicas del profesor (contexto del hilo) --}}
                    @if($comment->replies->isNotEmpty())
                        <div class="mt-2 ml-2 pl-3 border-l-2 border-emerald-500/30 space-y-2">
                            @foreach($comment->replies as $reply)
                                <div wire:key="reply-{{ $reply->id }}">
                                    <div class="flex items-center gap-2">
                                        <x-lms.user-avatar :user="$reply->user" size="xs" ring="ring-2 ring-emerald-500/20" fallback="bg-emerald-500/10 text-emerald-300" />
                                        <span class="text-[10px] font-semibold text-emerald-300">
                                            {{ $reply->user?->profile?->firstname ?? '—' }}
                                        </span>
                                        <span class="text-[9px] font-bold uppercase text-emerald-400/70 bg-emerald-500/10 px-1 rounded">Profesor</span>
                                        <span class="text-[10px] text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>

                                        {{-- Acciones del autor/admin (mejora #4) --}}
                                        @if(auth()->id() === $reply->user_id || auth()->user()->is_admin)
                                            <span class="flex items-center gap-1.5">
                                                <button wire:click="openActivityEditReply({{ $reply->id }})"
                                                        class="text-[9px] font-semibold text-slate-400 hover:text-emerald-400 transition-colors">
                                                    Editar
                                                </button>
                                                <button wire:click="confirmActivityDeleteReply({{ $reply->id }})"
                                                        class="text-[9px] font-semibold text-slate-400 hover:text-red-400 transition-colors">
                                                    Borrar
                                                </button>
                                            </span>
                                        @endif
                                    </div>

                                    @if($activityEditReplyId === $reply->id)
                                        <div class="mt-1 space-y-1">
                                            <textarea wire:model="activityEditReplyBody" rows="2" maxlength="1000"
                                                      class="w-full bg-slate-900 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm
                                                             placeholder-slate-500 resize-none transition-all"></textarea>
                                            @error('activityEditReplyBody') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                                            <div class="flex gap-2 justify-end">
                                                <button wire:click="$set('activityEditReplyId', null)"
                                                        class="px-2 py-1 text-[10px] text-slate-400 hover:text-slate-300 transition-colors">
                                                    Cancelar
                                                </button>
                                                <button wire:click="saveActivityEditReply" wire:loading.attr="disabled"
                                                        class="px-3 py-1 text-[10px] font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">
                                                    Guardar
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-300 mt-0.5">{{ $reply->body }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Botón Responder --}}
                    <button wire:click="openActivityReply({{ $comment->id }})"
                            class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Responder
                    </button>

                    {{-- Form de réplica inline --}}
                    @if($activityReplyToCommentId === $comment->id)
                        <div wire:key="activity-reply-form-{{ $comment->id }}" class="mt-2 space-y-1">
                            <textarea wire:model="activityReplyBody" rows="2" maxlength="1000"
                                      placeholder="Escribe tu respuesta…"
                                      class="w-full bg-slate-900 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm
                                             placeholder-slate-500 resize-none transition-all"></textarea>
                            @error('activityReplyBody') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                            <div class="flex gap-2 justify-end">
                                <button wire:click="$set('activityReplyToCommentId', null)"
                                        class="px-3 py-1 text-[11px] text-slate-400 hover:text-slate-300 transition-colors">
                                    Cancelar
                                </button>
                                <button wire:click="saveActivityReply" wire:loading.attr="disabled"
                                        class="px-3 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">
                                    Enviar réplica
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @if($commentsTab === 'pending')
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="approveActivityComment({{ $comment->id }})"
                                class="px-2 py-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 rounded transition-colors"
                                title="Aprobar">
                            ✓
                        </button>
                        <button wire:click="confirmActivityReject({{ $comment->id }})"
                                class="px-2 py-1 text-[10px] font-bold text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded transition-colors"
                                title="Rechazar">
                            ✕
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-500 text-center py-4">
                @if($commentsTab === 'pending') No hay comentarios pendientes en esta actividad.
                @else No hay comentarios aprobados en esta actividad.
                @endif
            </p>
        @endforelse

        @if($activityComments && $activityComments->count() > 0 && $commentsTab === 'pending')
            <a href="{{ route('app.profesors.lms.comments', ['activityFilter' => $activity->id]) }}"
               class="text-xs text-emerald-400 hover:underline mt-2 inline-block">
                Ver todos en moderación →
            </a>
        @endif

        {{-- Modal de rechazo inline --}}
        @if($activityRejectCommentId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
             wire:click.self="$set('activityRejectCommentId', null)">
            <div class="bg-slate-800 border border-slate-600 rounded-xl shadow-2xl max-w-md w-full mx-4 p-5 space-y-3"
                 wire:click.stop>
                <h3 class="text-sm font-bold text-white">Rechazar comentario</h3>
                <textarea wire:model="activityRejectReason" rows="3"
                          placeholder="Motivo del rechazo (opcional)…"
                          class="w-full bg-slate-900 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm
                                 placeholder-slate-500 resize-none transition-all"
                          maxlength="500"></textarea>
                @error('activityRejectReason') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('activityRejectCommentId', null)"
                            class="px-3 py-1.5 text-xs text-slate-400 hover:text-slate-300 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="rejectActivityComment"
                            class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-lg">
                        Rechazar
                    </button>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>
