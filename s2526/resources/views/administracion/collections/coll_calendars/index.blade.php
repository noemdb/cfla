@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - Calendario @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">

                <h3><span class="font-weight-bolder">Políticas de Cobranza - Calendario</span></h3>

            </div>

            <div class="card-body">

                <livewire:administracion.cobranza.coll-calendar.index-component />                

            </div>

        </div>
        
    </main>

@endsection


