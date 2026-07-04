<table class="table table-sm small" style="padding-top:0.4rem;font-size:0.6rem !important">
    <thead class="thead-inverse">
        {{-- <tr><th colspan="{{ $lapsos->count() ?? null}}">Sub Areas de Formación</th></tr> --}}
        <tr>
            <th>Sub Áreas de Formación</th>
            @foreach ($lapsos as $lapso)
                <th>{{$lapso->code_sm ?? null}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>

        @php $pensum_sub_areas = $grado->getPensumSubAreas(1); @endphp
        @foreach ($pensum_sub_areas as $pensum)
            
            <tr>
                <td>{{$pensum->grupo_estable_name}}</td>
                @foreach ($lapsos as $lapso)
                    @php
                        $estudiant_id = $estudiant->id;
                        $seccion_id = $seccion->id;
                        $lapso_id = $lapso->id;
                        $nota = $pensum->GetNota($estudiant_id,$seccion_id,$lapso_id,0);
                    @endphp
                    <th>{{$nota ?? null}}</th>
                @endforeach
            </tr>
        @endforeach
        

        
        {{-- @foreach ($pensum_sub_areas as $pensum)
            @php $asignatura = $pensum->asignatura; @endphp
            <tr><td>{{($asignatura) ? $asignatura->name :null}}</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        @endforeach --}}
    </tbody>
</table>
