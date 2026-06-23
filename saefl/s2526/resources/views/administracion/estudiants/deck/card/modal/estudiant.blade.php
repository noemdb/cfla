  @php
      $ammount_expire_bill = $estudiant->ammount_expire_bill;
      $ammount_no_expire_bill = $estudiant->ammount_no_expire_bill;
      $border_class = ($ammount_expire_bill>0) ? 'danger' : 'success' ;
      $border_class = "border border-".$border_class." rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0";
      $color = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->seccion->grado->color : null ;
      $class_callout = "bd-callout bd-callout bd-callout-".$color;
  @endphp

  <div class="{{$class_callout ?? 'default'}} h-100 " id="card_estudiant_id_{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}">
    <div class="card h-100 {{$border_class ?? ''}}">
      <div class="row no-gutters">

        <div class="col-md-4 pr-1 pt-1 pb-1" style="; width: 50px !important;">

            {{-- @include('administracion.estudiants.deck.button.estudiant') --}}
            <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

        </div>

        <div class="col-md-8">

          <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
              <p class="align-text-top">
                @admin
                <span class=" d-block">ID: {{$estudiant->id ?? ''}}</span>
                @endadmin
                <span> {{$estudiant->fullname}}<br>CI: {{$estudiant->ci_estudiant}}</span><br>
                <span class="text-dark">
                  {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}}
                  {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                </span>
                @if (!empty($estudiant->retiro->id))
                    <span class=" d-block">Retiro (Administrativo/Académico) {{$estudiant->created_ap ?? ''}}</span>
                @endif
              </p>
            </small>
            @admon
                <div class="dropdown-divider mb-0 pb-0 mb-0"></div>

                @if (empty($estudiant->administrativa->planpago_id))
                    <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                @else
                    {!! $estudiant->administrativa->planpago->badge ?? '' !!}
                    @if ($ammount_expire_bill==0)
                        <span class="badge badge-success mt-1">SOLVENTE</span>
                    @else
                        {{-- <span class="badge badge-danger mt-1">{{ f_float($ammount_expire_bill) }}</span> --}}
                    @endif
                @endif

                @php
                $representant = $estudiant->representant;
                $total_credito = ($representant->total_credito>0) ? $representant->total_credito : null ;
                $total_abono = ($representant->total_abono>0) ? $representant->total_abono : null ;
                $saldo_a_favor = $total_credito+$total_abono;
                @endphp

                <div class="d-block">
                @if (isset($total_credito))
                    <span title="Monto total de los créditos a favor disponibles" class="badge badge-info">Bs. {{f_float($total_credito) ?? ''}}</span>
                @endif
                @if (isset($total_abono))
                    <span title="Monto total de los abonos disponibles" class="badge badge-secondary">Bs. {{f_float($total_abono) ?? ''}}</span>
                @endif
                <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
                @if ($saldo_a_favor>0)
                    <span title="Saldo a favor disponibles" class="badge badge-light">Bs. {{f_float($saldo_a_favor) ?? ''}}</span>
                @endif
                </div>

            @endadmon

          </div>
        </div>



      </div>

      <div class="card-footer p-1">



            @admon
                @if($ammount_expire_bill<>0)
                    <div class="p-1 m-1 border-0">
                        @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'true'])
                    </div>
                @endif
            @endadmon
        </div>

    </div>

  </div>
