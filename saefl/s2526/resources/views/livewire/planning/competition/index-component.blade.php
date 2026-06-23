<div>
    <div class="font-weight-bold small text-muted" wire:loading>Procesando...</div>

    <div class="border-bottom mb-2 pb-2">
        <div class="d-flex flex-wrap">
            @php
                $fields = [
                    'pestudio_id' => ['options' => $list_pestudio, 'placeholder' => 'Planes de Estudio'],
                    'grado_id' => ['options' => $list_grado, 'placeholder' => 'Grado/Año'],
                    'category' => ['options' => $list_category, 'placeholder' => 'Categoría', 'id' => 'category'],
                    'paginate' => ['options' => ['10' => '10', '20' => '20', '50' => '50', '100' => '100'], 'placeholder' => 'Seleccione'],
                ];
            @endphp

            @foreach ($fields as $name => $field)
                <div class="p-2 flex-grow-1">
                    @php $model = $name @endphp
                    {!! Form::select(
                        $model,
                        $field['options'],
                        old($model, $name === 'paginate' ? $paginate : null),
                        array_merge(
                            ['wire:model' => $model, 'class' => 'form-control w-100', 'placeholder' => $field['placeholder']],
                            isset($field['id']) ? ['id' => $field['id']] : []
                        )
                    ) !!}
                </div>
            @endforeach

            <div class="btn-group" role="group" aria-label="Button group">
                <a id="" class="btn btn-dark" href="{{ route('plannings.competitions.index') }}" role="button" title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                <a target="_blank" class="btn btn-danger {{ (isset($grado_id)) ? null : 'disabled' }}"
                    href="{{ route('plannings.competitions.batch.pdf', [
                        'grado_id' => $grado_id ?? 'select_grade'
                    ]) }}" role="button">
                    <i class="{{ $icon_menus['crud'] ?? '' }} fa-1x"></i>
                </a>
            </div>

        </div>
    </div>


    @php
        $class['iteration']="";
        $class['name']= "";
        $class['action']="";
    @endphp


    <table width="100%" class="table table-striped table-hover table-sm small p-1">
        <thead>
            <tr>
                <th class="{{ $class['iteration'] ?? ''}}">N</th>
                <th class="{{ $class['name'] ?? ''}}">Nombre</th>
                <th class="{{ $class['name'] ?? ''}}">N.Preguntas</th>
                <th class="{{ $class['name'] ?? ''}}" title="Preguntas con categoría inconsistente">Inconsistencias</th>
                <th class="{{ $class['action'] ?? ''}}">Acciones</th>
            </tr>
        </thead>

        <tbody>

            @forelse($pensums as $item)

                @php $key = Str::random().'-'.$item->id; @endphp

                <tr data-id="{{$item->id}}">
                    <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                    <td class="{{ $class['name'] ?? ''}}"> {{$item->asignatura_name ?? ''}}</td>
                    <td class="{{ $class['name'] ?? ''}}"> {{$item->questions->count() ?? ''}}</td>
                    <td class="{{ $class['name'] ?? ''}}">
                        @if(($item->inconsistency_count ?? 0) > 0)
                            <span class="badge badge-danger" title="{{ $item->inconsistency_count }} preguntas con prefijo de categoría incorrecto">
                                {{ $item->inconsistency_count }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="{{ $class['action'] ?? ''}}">
                        <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$item->id}}">
                            <a target="_blank" class="btn btn-dark btn-sm {{ (!$category || $category === 'DEBATE') ? 'disabled' : '' }}"
                                href="{{ route('plannings.competitions.cards.pdf', [
                                    'pensum_id' => $item->id,
                                    'category'  => ($category && $category !== 'DEBATE') ? $category : 'select_category'
                                ]) }}" role="button" title="PDF Tarjetas">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                            <a target="_blank" class="btn btn-light btn-sm {{ (!$category || $category === 'DEBATE') ? 'disabled' : '' }}"
                                href="{{ route('plannings.competitions.list.pdf', [
                                    'pensum_id' => $item->id,
                                    'category'  => ($category && $category !== 'DEBATE') ? $category : 'select_category'
                                ]) }}" role="button" title="PDF Listado">
                                <i class="{{ $icon_menus['crud'] ?? '' }} fa-1x"></i>
                            </a>
                            <button class="btn btn-secondary btn-sm" wire:click="openQuestions({{ $item->id }})" title="Gestionar Banco de Preguntas">
                                <i class="fas fa-list-ol"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#cloneModal" wire:click="setClonePensum({{ $item->id }})"
                                title="Clonar preguntas">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" wire:click="fixCategoryByPensum({{ $item->id }})"
                                title="Corregir categorías inconsistentes"
                                onclick="return confirm('¿Corregir categorías con prefijo incorrecto en este pensum?')">
                                <i class="fas fa-wrench"></i>
                            </button>
                        </div>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="5" align="center">
                        <strong>No hay datos</strong>
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    @include('livewire.planning.competition.modal.clone')
    @include('livewire.planning.competition.modal.questions')

</div>
