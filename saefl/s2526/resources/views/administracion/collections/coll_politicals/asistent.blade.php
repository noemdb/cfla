@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - Asistente @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Asistente para el registro de Políticas de Cobranza</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open(['route' => 'administracion.collections.coll_politicals.fullStore', 'method' => 'POST', 'id'=>'form-collPolitical-create', 'class'=>'form-signin']) !!}

                    <div class="container-fluid">

                        <div class="px-2">

                            <div class="progress"> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div></div>

                            <hr>

                            @include('administracion.collections.coll_politicals.form.asistent.main')

                        </div>

                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection


@section('stylesheet') @parent <style type="text/css"> .first-of-type { display: none; } </style> @endsection

@section('scripts')
    @parent
    <script type="text/javascript">

        $(document).ready(function(){
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            var form_count = 1, previous_form, next_form, total_forms;
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
