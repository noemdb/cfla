@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - Registrar @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_messeges.menus.edit') </div>
                {{-- FIN Menu rapido --}}

                <h3>Actualizar <span class="font-weight-bolder">Mensaje de Cobranza</span></h3>
            </div>

            <div class="card-body">

                <div class="card-body">

                    @include('administracion.elements.forms.errors')
                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($coll_messege,['route' => ['administracion.collections.coll_messeges.update', $coll_messege->id], 'method' => 'PUT', 'id'=>'form-update-collPolitical'.$coll_messege->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">

                            <h5 class="card-header pb-1 mb-1">
                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x text-dark float-right"></i>
                                Datos
                            </h5>

                            <div class="card-body p-2">

                                <div class="row">
                                    <div class="col">

                                        @include('administracion.collections.coll_messeges.form.fields')


                                        <div class="btn-group btn-group-sm btn-block">
                                            <button type="submit" class="btn-user-update btn btn-primary w-75" value="update" data-id="update" id="btn-update">
                                                <i class="far fa-save"></i>
                                                Actualizar
                                            </button>
                                            <a title="Mostrar vista previa del mensaje" class="btn-preview btn btn-info btn-sm w-25"  href="#" role="button" data-id="{{$coll_messege->id}}">
                                                <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        @include('administracion.collections.coll_messeges.partials.resumen.create')
                                    </div>
                                </div>

                            </div>
                        </div>

                    {!! Form::close() !!}

                </div>

            </div>
        </div>
    </main>

@endsection


{{-- preview --}}
@include('administracion.collections.coll_politicals.form.asistent.modals.preview')
@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('.btn-preview').click(function (e) {
                e.preventDefault();
                var row = $(this); //fila contentiva de la data
                var id = row.data('id');
                var container = '#content_preview';
                var ajaxurl = '{{route("administracion.collections.coll_messeges.preview.id", "_id_")}}'; ajaxurl = ajaxurl.replace('_id_', id);
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                })
                .done(function( result ) {
                    $(container).html(result);
                    $('#modalIdPreview').modal('toggle');
                })
                .fail(function() {
                    console.log( "error occured" );
                });

            });
        });
    </script>
@endsection
