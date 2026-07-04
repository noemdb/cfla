<div class="card">
    <div class="card-body">
        <h4 class="card-title alert alert-secondary">
            Distribución por institución de procedencia, <b>sólo para 1er Grado.</b>
        </h4>
        <p class="card-text">

            @php $total = $institutionOriginTotalsForGrade->sum(); @endphp
            @foreach ($institutionOriginTotalsForGrade->sortDesc() as $key => $item)
                @php $indice = ($total>0) ? 100 * ($item / $total) : 0; $indice = round($indice,2); @endphp
                <div class="text-uppercase">{{$key}}</div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-{{$arr[rand(1,7)] ?? null}} progress-bar-striped" role="progressbar" style="width: {{$indice ?? 20}}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">{{$item}}</div>
                </div>
            @endforeach

            <hr>

            <div class="alert alert-secondary text-right font-weight-bold">
                Total: {{$total}}
            </div>

        </p>
    </div>
</div>