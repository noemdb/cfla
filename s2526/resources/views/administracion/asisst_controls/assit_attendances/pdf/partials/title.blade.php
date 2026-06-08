<div style="font-size:0.9rem">
    Formato de Asistencias.
    @if ($cargo) <span style="float: right;font-size:0.7rem;font-weight: bold;"> &nbsp; Cargo: {{$cargo->name ?? null}}</span> @endif
    @if ($assit_schedule) <span style="float: right;font-size:0.7rem;font-weight: bold;"> &nbsp; Horario: {{$assit_schedule->name ?? null}} ||</span>  @endif
    {{-- @if ($assit_schedule) <span>Horario: {{$assit_schedule->name ?? null}}</span> @endif --}}
    <br>
    <span class="d-block p-0 m-0 ">
        PERIODO ACADÉMICO {{ Session::get('pescolar_name') }} ||
        Fecha inicial: {{$finicial->format('d-m-Y')}} ||
        Fecha Final: {{$ffinal->format('d-m-Y')}}
    </span>
</div>
