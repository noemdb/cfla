@extends('administracion.layouts.dashboard.app')

@section('title') Generación de Recibos de Pagos Rápidos @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                {{-- <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.index') </div> --}}
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Generar recibo de pago rápido</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.messeges.oper_ok')

                @empty($representant_id) <h4>Seleccione representante</h4> @endempty

                {{-- <hr> --}}

                @include('administracion.receibts.form.search.representant')

                @if ($representant_id)
                    {!! Form::open(['route' => 'administracion.receibts.store', 'method' => 'POST', 'id'=>'form-collPromise-create', 'class'=>'form-signin']) !!}

                        {{Form::hidden('user_id',Auth::user()->id)}}
                        {{Form::hidden('representant_id',$representant_id)}}

                        {{Form::hidden('num_caashs',$num_caashs)}}
                        {{Form::hidden('num_changes',$num_changes)}}
                        {{Form::hidden('num_pagos',$num_pagos)}}

                        <div class="container-fluid">

                            <div class="px-2">

                                <div class="progress"> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div></div>

                                <hr>

                                @include('administracion.receibts.asistent.main')

                            </div>

                        </div>

                    {!! Form::close() !!}
                @endif



            </div>
        </div>
    </main>

@endsection


@section('stylesheet') @parent <style type="text/css"> .first-of-type { display: none; } </style> @endsection

@section('scripts')
    @parent
    <script type="text/javascript">

        $(document).ready(function(){

            var form_count = 1;
            total_forms = $("div .step_form").length; //console.log(total_forms);

            $(".clic-step").click(function(){
                step_hide_name = $(this).data('step-hide'); //console.log(step_hide_name);
                step_hide = $('#'+step_hide_name);
                step_hide.hide();

                step_show_name = $(this).data('step-show'); //console.log(step_show_name);
                step_show = $('#'+step_show_name);
                step_show.fadeIn(800).delay( 100 );

                direction = $(this).data('direction');
                if (direction=="up") { setProgressBarValue(++form_count); }
                if (direction=="down") { setProgressBarValue(--form_count);}
            });

            setProgressBarValue(form_count);
            function setProgressBarValue(value){
                // if (value>=total_forms) { form_count=1; }
                var percent = parseFloat(100 / total_forms) * value;
                percent = percent.toFixed();
                $(".progress-bar").css("width",percent+"%").html(percent+"%");
            }
        });
    </script>
@endsection
