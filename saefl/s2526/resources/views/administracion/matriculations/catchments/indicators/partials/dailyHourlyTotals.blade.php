<div class="card">
    <div class="card-body">
        <h4 class="card-title alert alert-secondary">
            <!-- Distribución de registros por día-hora -->
            Distribución de registros por día.
            <small class="d-block small font-weight-bold text-muted">Incluye Fase 1 y Fase 2</small>
        </h4>
        
        <p class="card-text">
            
            @php $total = $dailyHourlyTotals->sum('total'); @endphp
            @foreach ($dailyHourlyTotals as $item)
                @php $indice = ($total>0) ? 100 * ($item->total / $total) : 0; $indice = round($indice,2); @endphp
                <!-- <div class="small text-muted">{{f_date($item->date)}} - Hora: {{$item->hour}}:00</div> -->
                <div class="small text-muted mt-2">{{f_date($item->date)}}</div>
                <div class="progress mb-0">
                    <div class="progress-bar bg-{{$arr[rand(1,7)] ?? null}} progress-bar-striped" role="progressbar" style="width: {{$indice ?? 20}}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                        {{$item->total}}
                    </div>
                </div>
            @endforeach

            <hr>

            <div class="alert alert-secondary text-right font-weight-bold">
                Total: {{$total}}
            </div>

        </p>
    </div>
</div>