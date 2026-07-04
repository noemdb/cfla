<div class="card mx-3 shadow">
    <div class="card-body p-0">
        <h5 class="card-title alert alert-secondary">
            @php
                $representant = $selected->representant;
                $ident = ($selected) ? $selected->id:null ;
                $exchange_ammount = ($selected) ? $selected->exchange_ammount:null ;
                $created_at = ($selected) ? $selected->created_at->format('d-m-Y'):null ;
            @endphp
            <div class="small font-weight-bold">
                {{ ($representant) ? $representant->name : null}}
                <span class="small text-muted font-weight-bold text-right">
                    <div class=""> Ident: {{ $ident ?? null }} </div>
                    <div class=""> Monto: $ {{ f_float($exchange_ammount) }} </div>
                    <div class=""> Fecha: {{ $created_at ?? null }} </div>
                </span>
            </div>
        </h5>
        <p class="card-text">
            <div class="p-2">
                @include('administracion.creditoafavors.setup.update')
            </div>
        </p>
    </div>
</div>
