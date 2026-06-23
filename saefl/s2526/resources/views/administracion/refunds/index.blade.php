@extends('administracion.layouts.dashboard.app')
@section('title') Vueltos - Devoluciones - {{ Auth::user()->rol ?? '' }} @endsection
@section('main')

    <main role="main" class="col-md-10 col-lg-10">
        <livewire:administracion.refund.index-component :representant_id="$representant_id" :registro_pago_combinado_id="$registro_pago_combinado_id" :credito_a_favor_id="$credito_a_favor_id"/>
    </main>

@endsection

@section('scripts')
    @parent
@endsection
