@foreach ($representant->estudiants as $estudiant)

    @php
        // $ammount_expire_bill = $estudiant->ammount_expire_bill;
        $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
        $exchange_ammount_unexpired_bill = $estudiant->exchange_ammount_unexpired_bill;
        // $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    @endphp
    @if ($exchange_ammount_expire_bill>0 || $exchange_ammount_unexpired_bill>0)

        <div class="border rounded font-weight-bold px-2 shadow-sm">

            {{ $estudiant->fullname ?? ''}}
            <div class="pl-2">

                <div class="small">
                    @if($exchange_ammount_expire_bill>0)
                        <div class="p-1 m-1 border-0">
                            @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'true'])
                        </div>
                    @endif
                    @if($exchange_ammount_unexpired_bill>0)
                        <div class="p-1 m-1 border-0">
                            @include('administracion.estudiants.partial.estudiant_unexpired_bill_state',['show_ctas' => 'true'])
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @endif

@endforeach
