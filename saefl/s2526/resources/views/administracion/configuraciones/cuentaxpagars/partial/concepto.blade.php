<div class=" bd-callout bd-callout-{{ ($cuentaxpagar->status_edit) ? 'success':'dark' }} h-100">
    <div class="card p-0 h-100 bg-light">
        <div class="card-header p-1">
            <h6 class="font-weight-bold p-0 m-0 text-right">
                <span>Cuentas de cobro</span><br>
            </h6>
        </div>
        <div class="card-body p-1">
            @if (!empty($cuentaxpagar->conceptopagos->count()))
                @foreach ($cuentaxpagar->conceptopagos as $conceptopago)
                    <dl class="mb-1 border-bottom">
                        <dd class="">
                            <span class=" text-muted">{{$conceptopago->nomconceptopago->name ?? ''}}</span>
                            {{-- <span class="float-right"> --}}
                                <span id="credito_a_ammount" class="badge badge-light">
                                    Bs.{{f_float($conceptopago->concepto_ammount)}}
                                </span>
                            {{-- </span> --}}
                        </dd>
                    </dl>
                @endforeach
                <p class="font-weight-bold pt-2">
                    TOTAL <span class="badge badge-dark float-right">Bs. {{f_float($cuentaxpagar->conceptopagos->sum('concepto_ammount'))}}</span>
                </p>
            @endif

            @if ($cuentaxpagar->type == "INDIVIDUAL")
                @php $estudiant = ($cuentaxpagar->estudiant) ? $cuentaxpagar->estudiant : null ; @endphp
                @php $representant = ($estudiant) ? $estudiant->representant : null ; @endphp
                <hr>
                <div class="">  
                    <ul class="pl-2 ml-2">
                        <li><span class="font-weight-bold">Representante:</span><br>{{ $representant->name ?? null}}</li>
                        <li><span class="font-weight-bold">CI:</span><br>{{ $representant->ci_representant ?? null}}</li>
                        <hr>
                        <li><span class="font-weight-bold">Estudiante:</span><br>{{ $estudiant->fullname ?? null}}</li>
                        <li><span class="font-weight-bold">CI:</span><br>{{ $estudiant->ci_estudiant ?? null}}</li>
                    </ul>                  
                    
                </div>

            @endif
        </div>
        @if (!$cuentaxpagar->status_edit)
            {{-- <hr> --}}
            <small class="text-muted font-weight-bold pt-2">Existen pagos registrados relacionados a ésta cuenta.</small>
        @endif
    </div>
</div>
