<table cellpadding="2" cellspacing="2" width="100%" border="1"  class="table-list">
    <thead>
        <tr>
        <th align="left">
            VII. Observaciones
        </th>
        </tr>
    </thead>
    <tbody>
        <tr>          
            <td class="" align="left">
                <div>{{ ($seccion) ? $seccion->comment_final : null}}</div>
                @php $count = 0; @endphp
                @foreach ($estudiants as $estudiant)
                    @if (isset($estudiant->obs_resumen_final))
                        @php $count++; @endphp
                        <div>{{$estudiant->obs_resumen_final}}</div>
                    @endif
                @endforeach
                @for ($i = $count; $i < 3; $i++)
                    <p>&nbsp;</p>
                @endfor
            </td>
        </tr>
    </tbody>
</table>
