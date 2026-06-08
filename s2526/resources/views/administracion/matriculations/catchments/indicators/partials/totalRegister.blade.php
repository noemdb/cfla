<div class="card">
    <div class="card-body">
        <h4 class="card-title alert alert-secondary">
            <div class="d-flex justify-content-between align-items-center">
                @php $total = $catchments->count(); @endphp
                <div>Total de registros</div>
                <div class="badge badge-primary badge-pill">{{$total}}</div>
            </div>
            
        </h4>
        <p class="card-text">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @php $total = $catchments->whereNull('grade')->count(); @endphp
                    <span class=" font-weight-bold">Fase 1:</span> 
                    <h5><span class="badge badge-danger badge-pill">{{$total}}</span></h5>                              
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @php $total = $catchments->whereNotNull('grade')->count(); @endphp
                    <span class=" font-weight-bold">Fase 2:</span> 
                    <h5><span class="badge badge-success badge-pill">{{$total}}</span></h5>                              
                </li>
            </ul>
        </p>
    </div>
</div>