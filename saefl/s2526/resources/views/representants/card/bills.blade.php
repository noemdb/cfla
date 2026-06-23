@if ($expire_bill_pendientes->isNotEmpty())
    <div class="alert-secondary rounded">
        <span class="small font-weight-bold text-muted text-uppercase">
            CUOTA(S) PENDIENTE(S)
        </span>
    </div>
    <div class=" pl-1 ml-1 text-danger">
        <ol class="ml-1 pl-1">
            @foreach ($expire_bill_pendientes as $expire_bill)
                @php 
                    $ammount_exchange = round($expire_bill['ammount'],2);
                    $expire_bill_name = $expire_bill['expire_bill_name'];
                @endphp
                @if($ammount_exchange > 0)
                <dt class=" font-weight-bold">-. {{ $expire_bill['expire_bill_name'] ?? '' }} <small class="text-muted">USD {{$ammount_exchange}}</small></dt>
                @endif
            @endforeach
        </ol>
    </div>

@endif
