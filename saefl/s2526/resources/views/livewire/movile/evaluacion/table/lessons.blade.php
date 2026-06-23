<div>

    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Contenido</th>
                <th>Comentario/Observación</th>
                <th>Acción</th>
                <!-- Agrega las demás columnas según tus necesidades -->
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $item)
                @php $pevaluacion = $item->pevaluacion; @endphp
            <tr>
                <td title="{{ $item->content }}">
                    <div class="text-muted border-bottom">{{ ($pevaluacion) ? $pevaluacion->microname : null }}</div>
                    <div class="text-muted border-bottom">{{ ($item->planned) ? f_date($item->planned) : null }}</div>
                    <div class="text-truncate text-wrap">{{ $item->title }}</div>
                    <div class="text-truncate text-wrap">{{$item->content ?? null}}</div>
                </td>
                <td title="{{ $item->comments }}">
                    <div class="text-truncate text-wrap">                        
                        <span class=" fw-bold">Comentario:</span> {{ $item->comments }}
                    </div>
                    <hr class="m-1 p-1">
                    <div class="text-truncate text-wrap"><span class=" fw-bold">Observación:</span> {{ $item->observations }}</div>
                </td>
                <td>
                    {{-- <fieldset {{ ($item->observations) ? 'disabled' : null}}>                        --}}
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-warning" wire:click="edit({{$item->id}})">
                                {{-- <i class="fas fa-pen" aria-hidden="true"></i> --}}
                                <i class="fa fa-plus-square" aria-hidden="true"></i>
                            </button>
                        </div>
                    {{-- </fieldset>  --}}
                </td>
                <!-- Muestra las demás propiedades de la lección en las columnas correspondientes -->
            </tr>
            @empty

                <tr>
                    <td colspan="3">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $lessons->links() }}
</div>