<div>
    <div class="alert-light shadow p-2 m-2 border rounded">
        <div class="text-center">
            <div class=" font-weight-bold text-dark">Tasa de cambio del BCV</div>
            {{-- <i class="fa fa-exchange-alt fa-4x" aria-hidden="true"></i> --}}
            <img class="img-fluid" src="{{ asset('images/logo/bcv.png') }}" alt="" width="84" height="84">
        </div>
        <div class="container-fluid text-dark">
            <div class="row">
                <div class="col-2 px-0">
                    <button title="Consultar día anterior" class=" form-control btn" wire:click="downDate" wire:loading.attr="disabled">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                        {{-- - --}}
                    </button>
                </div>
                <div class="col-8 px-0 text-center">
                    <span>Fecha</span>
                    <h4 class="font-weight-bold py-0 my-0">
                        {{ ($date) ? $date->format('d-m-Y') : '' }}
                    </h4>
                </div>
                <div class="col-2 px-0">
                    <button title="Consultar día siguiente" class=" form-control btn" wire:click="upDate" wire:loading.attr="disabled">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{-- + --}}
                    </button>
                </div>
            </div>
            <hr class="py-1 my-1">
            <div class="row">
                <div class="col">
                    <div class=" text-center">
                        @if ($exchange_rate)
                            <span class=" font-weight-bold">Bs/USD</span>

                            <h4 class="card-title font-weight-bold text-success py-0 my-0"><span class="badge badge-dark" wire:loading.remove>{{f_float($exchange_rate->ammount)}}</span> </h4>
                        @else
                            <div class="text-muted small" wire:loading.remove>Aún no hay T.D.C registrada</div>

                            @admon
                                {{-- <div class="d-flex justify-content-around">
                                    <a title="Registrar Tasa de Cambio" class="btn btn-primary btn-sm" href="{{ route('administracion.configuraciones.exchange_rates.create') }}" role="button">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </a>
                                    @if ($date->format('Y-m-d') == $today->format('Y-m-d'))

                                        @if ($exchange_rate_suggested)
                                            <button title="Establecer la TDC a travéz de un servicio externo" wire:click="setExchanRateSuggested()" type="button" class="btn btn-info btn-sm">
                                                <i class="fas fa-exchange-alt"></i>
                                                <span class="font-weight-bold">Bs/USD {{ $exchange_rate_suggested ?? null}}</span>
                                            </button>
                                        @else
                                            <div class="border rounded p-1" title="No hay tasa de cambio sugerida">NH.TDC.S</div>
                                        @endif
                                    @endif
                                </div> --}}
                                <div class="btn-group btn-group-sm" role="group" aria-label="">
                                    <a title="Registrar Tasa de Cambio" class="btn btn-primary" href="{{ route('administracion.configuraciones.exchange_rates.create') }}" role="button">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Registrar TDC
                                    </a>
                                    <button title="Establecer la TDC a travéz de un servicio externo" wire:click="setExchanRateSuggested()" type="button" class="btn btn-info btn-sm">
                                        <i class="fas fa-exchange-alt"></i>
                                        TDC - S.E. (Bs/USD {{ $exchange_rate_suggested ?? null}})
                                    </button>
                                </div>
                            @endadmon
                        @endif
                    </div>
                    <div class="text-center text-muted" wire:loading>Procesando...</div>
                </div>
            </div>
        </div>
    </div>
</div>
