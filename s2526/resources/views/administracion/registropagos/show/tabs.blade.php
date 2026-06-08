<nav class="pt-1 mt-1">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link pt-2 active" id="nav-tab01-tab" data-toggle="tab" href="#nav-tab01" role="tab" aria-controls="nav-tab01" aria-selected="true" title="Rgistro de pago">Registro</a>
        <a class="nav-item nav-link pt-2" id="nav-tab07-tab" data-toggle="tab" href="#nav-tab07" role="tab" aria-controls="nav-tab07" aria-selected="false" title="Pago">Transacción</a>
        <a class="nav-item nav-link pt-2" id="nav-tab02-tab" data-toggle="tab" href="#nav-tab02" role="tab" aria-controls="nav-tab02" aria-selected="false" title="Pago">Pagado</a>
        <a class="nav-item nav-link pt-2" id="nav-tab05-tab" data-toggle="tab" href="#nav-tab05" role="tab" aria-controls="nav-tab05" aria-selected="false" title="Creditos a favor a aplicar">Creditos</a>
        <a class="nav-item nav-link pt-2" id="nav-tab03-tab" data-toggle="tab" href="#nav-tab03" role="tab" aria-controls="nav-tab03" aria-selected="false" title="Conceptos a cancelar">Conceptos</a>
        <a class="nav-item nav-link pt-2" id="nav-tab04-tab" data-toggle="tab" href="#nav-tab04" role="tab" aria-controls="nav-tab04" aria-selected="false" title="Descuentos a aplicar">Descuentos</a>        
        <a class="nav-item nav-link pt-2" id="nav-tab06-tab" data-toggle="tab" href="#nav-tab06" role="tab" aria-controls="nav-tab04" aria-selected="false" title="Descuentos a aplicar">Otros</a>        
    </div>
</nav>

<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active pl-2 pr-2" id="nav-tab01" role="tabpanel" aria-labelledby="nav-tab01-tab">
        @include('administracion.registropagos.show.partial.registro')               
    </div> 

    <div class="tab-pane fade" id="nav-tab07" role="tabpanel" aria-labelledby="nav-tab07-tab">      
      @if ($registropago->pagos)
        @php
          $ingreso = $registropago->ingreso;
        @endphp
        @include('administracion.registropagos.show.partial.ingreso')
      @endif      
    </div>

    <div class="tab-pane fade" id="nav-tab02" role="tabpanel" aria-labelledby="nav-tab02-tab">      
      @if ($registropago->pagos)
        @php
          $pagos = $registropago->pagos;
        @endphp
        @include('administracion.registropagos.show.partial.pagos')
      @endif      
    </div>

    <div class="tab-pane fade" id="nav-tab05" role="tabpanel" aria-labelledby="nav-tab05-tab">
      @if ($registropago->creditoaplicados)
        @php
          $creditoaplicados = $registropago->creditoaplicados;
        @endphp
        @include('administracion.registropagos.show.partial.creditoaplicados')
      @endif
    </div> 

    <div class="tab-pane fade" id="nav-tab03" role="tabpanel" aria-labelledby="nav-tab03-tab">
        @if ($registropago->conceptocancelados)
          @php
            $conceptocancelados = $registropago->conceptocancelados;
          @endphp
          @include('administracion.registropagos.show.partial.conceptocancelados')
        @endif
    </div> 

    <div class="tab-pane fade" id="nav-tab04" role="tabpanel" aria-labelledby="nav-tab04-tab">
        @if ($registropago->descuentoaplicados)
          @php
            $descuentoaplicados = $registropago->descuentoaplicados;
          @endphp
          @include('administracion.registropagos.show.partial.descuentoaplicados')
        @endif
    </div> 
        
    <div class="tab-pane fade" id="nav-tab06" role="tabpanel" aria-labelledby="nav-tab06-tab">
        {{-- @include('administracion.registropagos.form.fields.descuento') --}}
    </div>     
</div>  