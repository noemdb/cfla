@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="p-2">

                <div class="border rounded">

                    <h4 class=" alert alert-secondary font-weight-bold">
                        Reportar pagos
                    </h4>

                    @include('elements.messeges.oper_ok')
                    {{-- @include('elements.messeges.ticketRegister') --}}

                    <div class=" px-2">

                        <div class="row">
                            <div class=" col-sm-12 col-md-9">

                                @include('representants.registropagos.form.payments.main')
                                {{-- /home/nuser/code/s2021/resources/views/representants/registropagos/form/payments/main.blade.php --}}

                            </div>
                            <div class="col-sm-12 col-md-3">
                                <span class=" font-weight-bold pt-2 mt-2">
                                    Formulario para reportar pagos.
                                </span>
                                <p class=" small">
                                    En esta sección los representantes podrán reportar los pagos realizados a las cuentas bancarias de la institución,
                                    llenando todos los datos requeridos en cada apartado (<b>Representante</b>, <b>Estudiantes</b> y <b>Transferencias</b>) y luego haciendo clic en el botón <span class=" badge badge-primary p-2">Registrar</span>
                                </p>

                                <hr>

                                @include('representants.registropagos.form.payments.partials.anyErrors')
                                {{-- /home/nuser/code/s2021/resources/views/representants/registropagos/form/payments/partials/anyErrors.blade.php --}}

                                <span class=" font-weight-bold">
                                    Guía multimedia para reportar pagos en el portal de la institución
                                </span>

                                <div class="text-center embed-responsive embed-responsive-16by9"> <iframe src="https://drive.google.com/file/d/1LIzXSPEFPS7NOU5tncLbEGcEAMzgNMk3/preview"></iframe></div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </main>

@endsection

@section('styles') <style type="text/css"> .form_fieldset:not(:first-of-type) { display: none; } </style> @endsection

@section('scripts')
    <script type="text/javascript">

        $(document).ready(function(){
            var form_count = 1, previous_form, next_form, total_forms;
            total_forms = $("fieldset").length;

            $(".next-form").click(function(){
                previous_form = $(this).parent(); console.log(previous_form);
                next_form = $(this).parent().next();
                next_form.show();
                previous_form.hide();
                setProgressBarValue(++form_count);
            });

            $(".previous-form").click(function(){
                previous_form = $(this).parent();
                next_form = $(this).parent().prev();
                next_form.show();
                previous_form.hide();
                setProgressBarValue(--form_count);
            });

            $(".home-form").click(function(){
                previous_form = $(this).parent();
                next_form = $('#home');
                next_form.show();
                previous_form.hide();
                setProgressBarValue(1);
            });

            $(".transfer-form").click(function(){
                previous_form = $(this).parent();
                next_form = $('#transfer');
                next_form.show();
                previous_form.hide();
                setProgressBarValue(5);
            });

            $(".studiant-form").click(function(){
                previous_form = $(this).parent();
                next_form = $('#studiant');
                next_form.show();
                previous_form.hide();
                setProgressBarValue(5);
            });

            setProgressBarValue(form_count);
            function setProgressBarValue(value){
                var percent = parseFloat(100 / total_forms) * value;
                percent = percent.toFixed();
                $(".progress-bar").css("width",percent+"%").html(percent+"%");
            }
        });

        $(document).ready(function(){
            $('.box').hide();
            $('#dropdown').change(function() {
            $('.box').hide();
            $('#div' + $(this).val()).show();
            });
        });

        // function validate() {
        //     $("#file_error").html("");
        //     $(".demoInputBox").css("border-color","#F0F0F0");
        //     var file_size = $('#file')[0].files[0].size;
        //     if(file_size>2097152) {
        //         $("#file_error").html("File size is greater than 2MB");
        //         $(".demoInputBox").css("border-color","#FF0000");
        //         return false;
        //     }
        //     return true;
        // }
    </script>
@endsection
