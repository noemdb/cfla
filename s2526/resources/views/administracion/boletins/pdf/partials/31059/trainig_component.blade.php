<table class="table table-sm small" style="marging-top:0.2rem;padding-top:0.2rem;font-size:0.6rem !important">
    <thead class="thead-inverse">
        {{-- <tr><th colspan="{{ $lapsos->count() ?? null}}">Sub Areas de Formación</th></tr> --}}
        <tr>
            <th align="left">{{$pestudio->description_trainig_component ?? null}}</th>
            @foreach ($lapsos as $lapso)
            {{-- @if ($lapso->id <= $lapso_id)                 --}}
                <th>{{$lapso->code_sm ?? null}}</th>
            {{-- @endif --}}
            @endforeach
        </tr>
    </thead>
    <tbody>

        @php $sub_areas = $estudiant->getSubAreas(1); @endphp
        @foreach ($sub_areas as $sub_area)
            
            <tr>
                <td>[{{$sub_area->code_sm}}] {{$sub_area->name}}</td>
                @foreach ($lapsos as $lapso)
                    @if ($lapso->id <= $lapso_id)
                        @php
                            $estudiant_id = $estudiant->id;
                            $seccion_id = $seccion->id;
                            $nota = $sub_area->GetNota($estudiant_id,$seccion_id,$lapso->id,0);
                        @endphp
                        <th>{{$nota ?? null}}</th>
                    @else
                        <th>&nbsp;</th>
                    @endif
                @endforeach
            </tr>

        @endforeach
        
    </tbody>
</table>
