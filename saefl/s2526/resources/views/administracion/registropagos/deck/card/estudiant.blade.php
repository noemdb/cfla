@php
    $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
    $ammount_expire_bill_exchange = $exchange_rate_current
        ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill
        : null;
@endphp

<div class="bd-callout bd-callout-{{ $estudiant->getInscripcion()->seccion->grado->color ?? 'default' }} h-100">
    <div class="card h-100 {{ $estudiant->status_active == 'false' ? 'border-danger' : '' }}">
        @isset($estudiant->logo)
            <img class="card-img-top"
                src="{{ isset($estudiant->logo) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}"
                alt="Card image cap">
        @endisset

        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                @include('administracion.estudiants.partials.href')
                {{-- {{$estudiant->name.' '.$estudiant->lastname}}<br> CI: {{$estudiant->ci_estudiant}} --}}
            </small>
        </div>
        @if ($estudiant->exchange_ammount_expire_bill > 0)
            <small>
                Deuda Vencida:
            </small>
            <span class="pt-0 mt-0">
                <span class="badge badge-danger">
                    {{ !empty($exchange_rate_current) ? 'Bs. ' . f_float($ammount_expire_bill_exchange) : 'STDC' }}
                </span>
                <span class="badge badge-dark">
                    $. {{ f_float($exchange_ammount_expire_bill) }}
                </span>
            </span>
        @endif
        <span class="float-right">
            @if ($estudiant->planpago)
                @admon <span class="">{!! $estudiant->administrativa->planpago->badge ?? '' !!}</span> @endadmon
                @if ($exchange_ammount_expire_bill == 0)
                    <span class="badge badge-success mt-1">SOLVENTE</span>
                @endif
            @else
                @admon<span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                @endadmon
            @endif
        </span>

        <div class="card-footer p-1">
            <p class="card-text">
                {{-- registropagos: {{$estudiant->getInscripcion()->id}} --}}
                @php
                    $representant_id = $estudiant->representant ? $estudiant->representant->id : null;
                    $ci_representant = $estudiant?->representant?->ci_representant;
                @endphp
                <a class="btn btn-info btn-sm btn-block" {{-- href="{{ route('administracion.registropagos.parcial.create',['id'=>$estudiant->id]) }}" role="button"> --}}
                    href="{{ route('administracion.registropagos.livewire.asistent', ['ci_representant' => $ci_representant]) }}">
                    {{-- href="{{ route('administracion.registropagos.asistent.representant.create',['id'=>$representant_id]) }}"> --}}
                    Registrar Pago
                </a>
            </p>
        </div>
    </div>
</div>
