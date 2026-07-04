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
            <th class="{{ $class['name'] ?? ''}}">N.Pregunstas</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody>

        @forelse($pensums as $item)

            @php $key = Str::random().'-'.$item->id; @endphp

            <tr data-id="{{$item->id}}" class="{{($item->id == $pensum_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['name'] ?? ''}}"> {{$item->asignatura_name ?? ''}}</td>
                <td class="{{ $class['name'] ?? ''}}"> {{$item->questions->count() ?? ''}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$item->id}}">
                        <button class="btn btn-info btn-sm" wire:click="setModeQuestions({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Preguntas"><i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i></button>
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


