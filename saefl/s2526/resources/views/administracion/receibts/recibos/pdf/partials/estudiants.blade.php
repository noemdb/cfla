
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0.1rem; padding-bottom:0.1rem;border:1px solid #ccc">
    @forelse ($estudiants as $estudiant)
        <tr>
            <th align="left" width="15%" style="padding-left: 0.4rem;word-spacing: 0em; white-space: nowrap;">Estudiante: <span style="font-size: 0.7rem !important;font-weight: normal">{{$estudiant->fullname}}</span></th>
            {{-- <td align="left" style="font-size: 0.8rem !important">{{$estudiant->fullname}}</td> --}}
            <th align="right" width="15%" style="padding-left: 0.4rem">Grado/Año: <span style="font-size: 0.8rem !important;font-weight: normal">{{$estudiant->fullinscripcion}}</span></th>
            {{-- <td align="left" style="font-size: 0.8rem !important">{{$estudiant->fullinscripcion}}</td> --}}
        </tr>
    @empty
        <tr> <td>No hay estudiantes</td> </tr>
    @endforelse
    @if ($estudiants->isNotEmpty())
        <tr>
            <th align="right" colspan="2">
                <div style="font-size: 0.7rem;text-align:right;">
                    Total de Estudiantes: <b>{{ $estudiants->count() ?? '0' }}</b>
                </div>
            </th>
        </tr>
    @endif
</table>


