<table class="table-sm" style="font-size:0.6rem">
    <tr>
        <td width="60%" class="text-left">
            <strong>
                Núm. de Horas Comunitarias: {{ $estudiant->hours_completed ?? '' }}
            </strong>
        </td>
        <td width="40%" class="text-left">
            <strong>
                Núm. de Pases entregados: {{ !empty($estudiant->count_passes) ? $estudiant->count_passes : 0 }}
            </strong>
        </td>
    </tr>
</table>