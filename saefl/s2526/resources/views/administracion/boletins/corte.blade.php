@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.index')
                </div>

                <h4><b class="text-dark">Informe de Corte de Notas</b> por lapso</h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.boletins.form.search.corte',[
                    'route'=>'administracion.boletins.corte',
                    'required_seccion'=>true,
                    'btn_toprint_lote'=>'true'])

                @include('administracion.boletins.table.corte')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script>
        //btn generar PDF de todos los grados del lapso seleccionado
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
          $('#btn_toprint_lote').click(function (e) {
              e.preventDefault();
              var grado_id = $('#grado_id').val();	//console.log(ci_estudiant);
              var seccion_id = $('#seccion_id').val();	//console.log(ci_estudiant);
              var lapso_id = $('#lapso_id').val();	//console.log(ci_estudiant);
              var url = '{{ route("administracion.boletins.boletin.lote.corte.pdf",["_gid_","_sid_","_lid_"]) }}';
              url = url.replace('_gid_', grado_id); url = url.replace('_sid_', seccion_id); url = url.replace('_lid_', lapso_id);
              window.open(url,'_blank');
          });
        });
    </script>
@endsection
