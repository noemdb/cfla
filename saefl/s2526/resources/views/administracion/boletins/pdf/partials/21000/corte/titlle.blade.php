<h4 style="font-size:0.6rem;margin-top:0.2rem; padding-top:0.2rem;margin-bottom:0.2rem; padding-bottom:0.2rem;background-color:#ccc">
    <span style="font-size:0.9rem !important;">
        Informe Corte de Notas del {{strtoupper($lapso->name)}} 
    </span>
    <span>Notas consideradas hasta el: {{$lapso->date_cutnote}}</span>
    <span class="small" style="float: right !important;">
        PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}
    </span>
</h4>
