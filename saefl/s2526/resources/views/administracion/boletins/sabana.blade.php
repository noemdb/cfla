@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">

                <h4>Planilla de Registro de Notas (Sabana de notas por lapso y final)</h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{ (!empty($grado->count())) ? $grado->color : 'default'}} p-2 m-2">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.boletins.form.search.sabana',[
                    'route'=>'administracion.boletins.sabana',
                    'btn_toprint'=>'true',
                    'btn_toprint_lote'=>'true'])

                @include('administracion.boletins.table.sabana')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script>
        //btn generar PDF de todos los grados del lapso seleccionado
        $(document).ready(function() {
            $('#btn_toprint_lote').click(function (e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val();
                var lapso_id = $('#lapso_id').val();

                var pestudio_ids = {{ $pestudios->pluck('id') ?? ''}}; //console.log('pestudio_ids'+pestudio_ids);
                pestudio_ids.forEach( function(valor, indice, array) {
                    var url = '{{ route('administracion.boletins.lote.sabana.pdf',["_pid_","_lid_"]) }}';
                    url = url.replace('_pid_', valor);
                    url = url.replace('_lid_', lapso_id);
                    window.open(url,'_blank');
                });


            });
        });
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
          $('#btn_toprint').click(function (e) {
              e.preventDefault();
              var grado_id = $('#grado_id').val();	//console.log(ci_estudiant);
              var seccion_id = $('#seccion_id').val();	//console.log(ci_estudiant);
              var lapso_id = $('#lapso_id').val();	//console.log(ci_estudiant);
              var url = '{{ route('administracion.boletins.sabana.pdf',["_gid_","_sid_","_lid_"]) }}';
              url = url.replace('_gid_', grado_id); url = url.replace('_sid_', seccion_id); url = url.replace('_lid_', lapso_id);
              window.open(url,'_blank');
          });
        });
    </script>
@endsection
