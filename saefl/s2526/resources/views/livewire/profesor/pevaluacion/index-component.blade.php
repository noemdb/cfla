<div>
    <div class="p-2 border rounded">
        <div class="alert alert-secondary" role="alert">
            <strong>Resumen de las actividades de evaluación</strong>
        </div>

        <div class="form-group">
            <label for="title" class="m-0 font-weight-bold text-muted">Título</label>
            {!! Form::text('title', old('title'), [
                'class' => 'form-control',
                'placeholder' => 'Título',
                'id' => 'title',
                'required',
            ]) !!}
        </div>

        <ul class="list-group">
            <li class="list-group-item list-group-item-secondary font-weight-bold">

                <div class="d-flex justify-content-between">

                    <div>Indicadores de logro</div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="setCreate()">+</button>
                    </div>

                </div>

                @if ($modeCreator)
                    <div class="form-group">
                        <label for="achievement" class="m-0">Descripción</label>
                        {!! Form::textarea('achievement', old('achievement'), [
                            'class' => 'form-control',
                            'placeholder' => 'Descripción',
                            'rows' => '4',
                            'required',
                        ]) !!}
                    </div>
                    <div class="btn-group btn-block" role="group" aria-label="">
                        <button type="button" class="btn btn-primary btn-sm w-75" wire:click="save()">Guradar</button>
                        <button type="button" class="btn btn-secondary btn-sm w-25"
                            wire:click="close()">Cerrar</button>
                    </div>
                @endif

            </li>

            @forelse ($achievements as $item)
                <li class="list-group-item">

                    <div class="d-flex justify-content-between">
                        <div>Item</div>
                        <div><button type="button" class="btn btn-danger btn-sm"
                                wire:click="delete({{ $item->id }})">-</button></div>
                    </div>

                </li>

            @empty
                <li class="list-group-item disabled">No hay datos</li>
            @endforelse
            {{-- <li class="list-group-item disabled">Disabled item</li> --}}
        </ul>

        <hr>

        <div class="form-group">
            <label for="observations" class="m-0 font-weight-bold text-muted">Observación</label>
            {!! Form::textarea('observations', old('observations'), [
                'class' => 'form-control',
                'placeholder' => 'Observación',
                'rows' => '4',
                'id' => 'observations',
            ]) !!}
        </div>

        <div class="form-group">
            <label for="objetivo" class="m-0 font-weight-bold text-muted">Observación</label>
            {!! Form::textarea('objetivo', old('objetivo'), [
                'class' => 'form-control',
                'placeholder' => 'objetivo',
                'rows' => '2',
                'id' => 'objetivo',
            ]) !!}
        </div>

    </div>
</div>
