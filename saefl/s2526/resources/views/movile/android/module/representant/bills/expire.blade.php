@if ($expire_bill_pendientes->isNotEmpty())
    <div class="alert-secondary rounded">
        <span class="small font-weight-bold text-muted text-uppercase">
            CUOTA(S) PENDIENTE(S)
        </span>
    </div>
    <div class=" pl-1 ml-1 text-danger">
        <ol class="ml-1 pl-1">
            @foreach ($expire_bill_pendientes as $expire_bill)
            <dt class=" font-weight-bold">-. {{ $expire_bill['expire_bill_name'] ?? '' }}</dt>
            @endforeach
        </ol>
    </div>
@endif
