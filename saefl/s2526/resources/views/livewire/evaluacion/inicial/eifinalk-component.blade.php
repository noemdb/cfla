<div>
    <table class="table table-striped table-hover table-sm small p-1">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Profesor</th>
                {{-- <th>Lapso</th> --}}
                {{-- <th>Sección</th> --}}
                {{-- <th>Título</th> --}}
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($eifinalks as $item)
                @php
                    $estudiant = $item->estudiant;
                    $profesor = $item->profesor;
                @endphp
                <tr>
                    <td>{{ $estudiant->fullname ?? '—' }}</td>
                    <td>{{ $profesor->fullname ?? '—' }}</td>
                    <td>
                        @foreach ($lapsos as $lapso)

                        <a href="{{ route('evaluacions.eifinalks.print-all-for-lapso', ['estudiant' => $estudiant, 'lapso' => $lapso->id]) }}"
                            class="btn btn{{ $estudiant->hasEifinalkForLapso($lapso->id) ? null : '-outline'}}-{{$lapso->class}} btn-sm"
                            title="Ver informe"
                            target="_blank">
                            {{$lapso->id}}
                        </a>

                        @endforeach

                        
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay registros disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @section('livewireScriptsCustome')
    @parent

    <script>
        window.addEventListener('show-format', event => {
            const id = event.detail.id;
            // Aquí puedes redirigir, abrir PDF en nueva pestaña, o mostrar modal
            // Ejemplo para descargar:
            window.open(`/evaluacions/eifinalks/format/${id}`, '_blank');
        });
    </script>
        
    @endsection   



</div>

