@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4>
                    <i class="{{ $icon_menus['xls'] ?? '' }}  text-success"></i>
                    Carga de notas por evaluación/lapso/asignatura - XLS
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @includeWhen(!empty($notas_new), 'administracion.boletins.modal.notas_new')

                @include('administracion.boletins.partials.search_xls', [
                    'route' => 'administracion.boletins.carga.xls.post',
                ])

                {!! Form::open([
                    'route' => 'administracion.boletins.store.xls',
                    'method' => 'POST',
                    'id' => 'form-nota',
                    'class' => 'form-nota pb-2',
                    'role' => 'form-signin',
                ]) !!}

                {{ Form::hidden('grado_id', $grado_id, ['id' => 'grado_id']) }}
                {{ Form::hidden('lapso_id', $lapso_id, ['id' => 'lapso_id']) }}
                {{ Form::hidden('seccion_id', $seccion_id, ['id' => 'seccion_id']) }}
                {{ Form::hidden('pensum_id', $pensum_id, ['id' => 'pensum_id']) }}

                @if (!empty($list_evaluacions->count()))
                    <div class="form-group">
                        <label for="evaluacion_id" class="m-0 font-weight-bold">Evaluación - [Fecha de aplicación]
                            Descripción</label>
                        {!! Form::select('evaluacion_id', $list_evaluacions, $evaluacion_id, [
                            'class' => 'form-control',
                            'id' => 'evaluacion_id',
                            'placeholder' => 'Seleccione',
                            'required' => 'required',
                        ]) !!}
                    </div>
                @endif

                <div class="row">

                    <div class="col">
                        @php
                            $ci_not_founds = collect();
                            $nota_out_ranges = collect();
                        @endphp
                        @include('administracion.boletins.table.carga_xls')
                    </div>

                    @if (!empty($ci_not_founds->count()) || !empty($nota_out_ranges->count()))
                        <div class="col-4">
                            @include('administracion.boletins.table.carga_xls.errores')
                        </div>
                    @endif

                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </main>
@endsection
