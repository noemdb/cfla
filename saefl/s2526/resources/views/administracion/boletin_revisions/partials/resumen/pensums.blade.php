<dl class="mb-1 bd-callout bd-callout-info">
    <dt class=" alert-secondary px-1" title="Notas Finales y Número de Revisiones">Notas Finales</dt>
    @php $aprobacion = ($escala->aprobacion) ? : ''; @endphp
    @foreach($pensums as $pensum)
        @php $boletin_revisions = $estudiant->getRevisions($pensum->id); @endphp

        @php
            $asignatura = ($pensum->asignatura) ? $pensum->asignatura: null ;
            $nota = $estudiant->getNotaFinal($pensum->id);
            $nota_valor = $estudiant->getNotaFinal($pensum->id,0,false);
            $nota_pf = (is_numeric($nota)) ? str_pad($nota, 2, "0", STR_PAD_LEFT) : $nota ;
        @endphp

        <dd class="small font-weight-bold border-bottom {{ ($nota_valor < $aprobacion) ? 'alert-danger':null }}" title="{{ ($asignatura ) ? $asignatura->name : null}}">
            <span class="px-1">{{ ($asignatura ) ? $asignatura->code_sm : null}}</span>
            <span id="pagado_concepto_ammount" class="font-weight-bold float-right ">
                {{ $nota_pf }}
                {{ ( $boletin_revisions->count() > 0 ) ? '['.$boletin_revisions->count().']' : null }}
            </span>
        </dd>

    @endforeach
</dl>
