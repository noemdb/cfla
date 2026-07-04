@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-success">
                <h4>
                    <i class="{{ $icon_menus['libro'] }} fa-1x text-success"></i>
                    Libro de Inscripciones Administrativas
                    <span class="float-right">
                        <a class="btn btn-dark" target="_blank" href="{{ route('administracion.administrativas.book.pdf') }}"
                            role="button">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </a>
                    </span>
                </h4>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.administrativas.book',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}

                <label for="list_pescolar" class="m-0">Peŕodo Escolar a consultar</label>
                <div class="input-group mb-3">
                    {!! Form::select('pescolar_id', $list_pescolar, $pescolar_id, [
                        'class' => 'form-control',
                        'id' => 'pescolar_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Mostrar</button>
                    </div>
                </div>

                {!! Form::close() !!}

                @if ($std_siaca_ciadm->count() > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span><strong>{{ $std_siaca_ciadm->count() ?? '0' }}</strong> estudiante(s) con inscripción
                            administrativa y sin inscripción académica</span>
                        <br>
                        @foreach ($std_siaca_ciadm as $estudiant)
                            <span class="pl-3">
                                -. {{ $estudiant->lastname }} {{ $estudiant->name }} {{ $estudiant->ci_estudiant }} ||
                                <b>{{ $estudiant->full_administrativa ?? null }}</b>
                            </span>
                            <br>
                        @endforeach
                    </div>
                @endif

                @if ($std_ciaca_siadm->count() > 0)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span>{{ $std_ciaca_siadm->count() ?? '0' }} estudiante(s) con inscripción académica y sin
                            inscripción administrativa.</span>

                        <br>
                        @foreach ($std_ciaca_siadm as $estudiant)
                            <span class="pl-3">
                                -. {{ $estudiant->lastname }} {{ $estudiant->name }} {{ $estudiant->ci_estudiant }} ||
                                <b>{{ $estudiant->full_inscripcion ?? null }}</b>
                            </span>
                            <br>
                        @endforeach
                    </div>
                @endif

                @isset($pestudios)
                    @include('administracion.administrativas.book.tab.pestudios')
                @endisset

                @isset($grados)
                    @include('administracion.administrativas.book.tab.grados')
                @endisset

                @isset($tinscripcions)
                    <h5 class="pt-1">Tipos de Inscripción</h5>
                    @include('administracion.administrativas.book.table.tinscripcions')
                @endisset

            </div>

        </div>

    </main>

@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/Chart.bundle.js') }}"></script>
    <script src="{{ asset('js/ChartFunction.js') }}"></script>
    <script src="{{ asset('js/ChartEvent.js') }}"></script>
@endsection
