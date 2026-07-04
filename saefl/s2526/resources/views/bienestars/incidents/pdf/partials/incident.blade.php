<table  cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0">
    <thead  class="thead-inverse"  align="left">
        <tr>
            <th align="center">Incidencia <span style="font-size: 0.8rem">[{{ $incident->code ?? null}}]</span></th>
            {{-- <th align="center">Acuerdo(s)</th> --}}
        </tr>
    </thead>
    <tbody>
        <tr style="vertical-align: top">
            <td style="vertical-align: top">@include('livewire.bienestar.estudiant.pdf.partials.incidencias.messege')</td>
            {{-- <td style="width: 50%;vertical-align: top">@includeWhen($incident->status_notify_agreement,'livewire.bienestar.estudiant.pdf.partials.incidencias.agreement')</td> --}}
        </tr>

        @if ($incident->status_notify_agreement)
            <tr  class="thead-inverse" style="vertical-align: top">
                <th align="center">Acuerdo(s) <span style="font-size: 0.8rem">[{{ $incident->code ?? null}}]</span></th>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    @include('livewire.bienestar.estudiant.pdf.partials.incidencias.agreement')
                </td>
            </tr>
        @endif
        @if ($incident->status_notify_close)
            <tr  class="thead-inverse" style="vertical-align: top">
                <th align="center">Cierre de la incidencia <span style="font-size: 0.8rem">[{{ $incident->code ?? null}}]</span></th>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    @include('livewire.bienestar.estudiant.pdf.partials.incidencias.cierre')
                </td>
            </tr>
        @endif
    </tbody>
</table>
