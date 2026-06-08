@php $estudiants = $representant->estudiants;@endphp
<div class="p-1 flex-center" style="min-height: 300px;">
    <div class="alert">
        {{-- <div class="alert alert-secondary"> --}}
            <h6 class=" font-weight-bold">Puedes leer el siguente texto:</h6>
        {{-- </div> --}}
        <div class="alert alert-info text-muted" style="font-size: 1rem !important">
            <h6>
                Saludos Cordiales, disculpe la interrupción, se le esta llamando de parte de la administración de
                <b>Colegio Fray Luis Amigó sede de San Felipe - Estado Yaracuy</b>,
                sería tan amable de contestar algunas preguntas? seré breve y muy puntual.
            </h6>
        </div>
        <hr class="p-0 m-0">
        <div class="alert alert-success shadow ">
            <h4 class="text-center"> Es usted <b class="text-uppercase">{{ $representant->name ?? ''}}</b> ? </h4>
        </div>
        <div class="pl-4 text-right">
            Su{{ ($estudiants->count() > 1) ? 's' : null}} representando{{ ($estudiants->count() > 1) ? 's son' : ' es'}}:
            @foreach ($estudiants as $estudiant)
            <div>{{ $estudiant->shortname ?? '' }}</div>
            @endforeach
        </div>
    </div>
</div>
