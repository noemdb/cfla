@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['registrar_pago'] }} fa-1x text-danger"></i>
                    Asistente - Registro de Pago
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">
                        @include('administracion.registropagos.menus.crud')
                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.registropagos.asistent',
                    'method' => 'GET',
                    'class' => '',
                    'id' => 'form-registropago-search',
                ]) !!}
                <div class="input-group">
                    {!! Form::text('search', $search, [
                        'class' => 'form-control',
                        'placeholder' => 'Buscar nombre o cédula del representante',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @if ($representants->isNotEmpty())
                    @include('administracion.registropagos.deck.representants')
                @endif
            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
