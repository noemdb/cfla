@extends('layouts.errors')

@section('content')
<div class="container mt-5">

    {{-- LOGO INSTITUCIONAL --}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/avatar/uecfla.jpg') }}" 
        {{-- /home/nuser/code/s2526/public/images/avatar/uecfla.jpg --}}
             alt="Logo Institucional" 
             style="max-width: 150px;">
    </div>

    {{-- TARJETA PRINCIPAL --}}
    <div class="card shadow-lg border-0" style="border-radius: 12px;">
        <div class="card-header text-white text-center" 
             style="background: #146c43; border-radius: 12px 12px 0 0;">
            <h4 class="mb-0 font-weight-bold text-uppercase">
                Acceso Restringido
            </h4>
        </div>

        <div class="card-body text-center p-4">

            <div class="mb-3">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 50px;"></i>
            </div>

            <h5 class="font-weight-bold mb-3 text-dark">
                El estudiante o representante asociado presenta compromisos administrativos con la institución.
            </h5>

            <p class="text-muted mb-4">
                Para continuar utilizando las funcionalidades del sistema 
                es necesario que el estado de cuenta se encuentre solvente.
                <br>
                Por favor comuníquese con el Departamento de Administración o Control de Estudios.
            </p>

            <a href="{{ url()->previous() }}" 
               class="btn btn-primary px-4 py-2" 
               style="border-radius: 30px; background-color:#146c43; border-color:#146c43;">
                Volver
            </a>
        </div>

    </div>

</div>
@endsection


@section('stylesheet')
@parent
<style>
    /* Sombra suave institucional */
    .card.shadow-lg {
        box-shadow: 0 7px 25px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection
