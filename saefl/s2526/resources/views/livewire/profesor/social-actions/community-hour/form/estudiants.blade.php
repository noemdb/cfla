
<table class="table table-striped table-inverse table-sm small">    
    <thead class="thead-inverse">
        <tr>
            <th>N</th>
            <th>Identificador</th>
            <th>Estudiante</th>
            <th>Duración (Hrs)</th>
            {{-- <th>Acción</th> --}}
        </tr>
    </thead>
    <tbody>
        
        @forelse ($estudiants as $index => $estudiant)
            
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $estudiant->ci_estudiant }}</td>
                <td>{{ $estudiant->fullname }}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group" wire:key="btn-group-crud-{{$estudiant->id}}">
                        @php $name = 'select_range_'.$estudiant->id; $model = "asistents.".$index.".duration" @endphp
                        {!! Form::selectRange($name,0,$duration,old($name),['class'=>'form-control','wire:model.defer'=>$model,'placeholder'=>'Seleccione']);!!}
                    </div>    
                </td>

            </tr>
        @empty

            <tr>
                <th colspan="3" align="center">No hay datos</th>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- {{ $estudiants->links() }} --}}