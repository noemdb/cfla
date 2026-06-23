{{-- <div class="pt-1"> --}}
    <ol class="ml-1 pl-1">
        <dt>ASIGNATURAS:</dt>
        @foreach ($pensums as $pensum)
            <li>{{ $pensum->asignatura->fullname}}</dd>
        @endforeach
    </ol>
{{-- </div> --}}
