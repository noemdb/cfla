@include('administracion.elements.forms.errors')

@include('administracion.elements.messeges.oper_ok')

{!! Form::model($institucion,['route' => ['administracion.configuraciones.institucionupdate', $institucion->id], 'method' => 'PUT', 'id'=>'form-update-institucion', 'role'=>'form']) !!}

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-item nav-link active" id="nav-generales-tab" data-toggle="tab" href="#nav-generales" role="tab" aria-controls="nav-generales" aria-selected="true">Generales</a>
      <a class="nav-item nav-link" id="nav-location-tab" data-toggle="tab" href="#nav-location" role="tab" aria-controls="nav-location" aria-selected="false">Localización</a>
      <a class="nav-item nav-link" id="nav-bills-tab" data-toggle="tab" href="#nav-bills" role="tab" aria-controls="nav-bills" aria-selected="false">Facturación</a>
      <a class="nav-item nav-link" id="nav-others-tab" data-toggle="tab" href="#nav-others" role="tab" aria-controls="nav-others" aria-selected="false">Otros</a>
    </div>
  </nav>
  <div class="tab-content border border-top-0" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-generales" role="tabpanel" aria-labelledby="nav-generales-tab">
        <div class="p-2">
        @include('administracion.configuraciones.institucion.form.generales',$institucion)
        </div>
    </div>
    <div class="tab-pane fade" id="nav-location" role="tabpanel" aria-labelledby="nav-location-tab">
        <div class="p-2">
        @include('administracion.configuraciones.institucion.form.location',$institucion)
        </div>
    </div>
    <div class="tab-pane fade" id="nav-bills" role="tabpanel" aria-labelledby="nav-bills-tab">
        <div class="p-2">
        @include('administracion.configuraciones.institucion.form.bills',$institucion)
        </div>
    </div>
    <div class="tab-pane fade" id="nav-others" role="tabpanel" aria-labelledby="nav-others-tab">
        <div class="p-2">
        @include('administracion.configuraciones.institucion.form.others',$institucion)
        </div>
    </div>
  </div>


    @if (Auth::user()->isAdmin())
        <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-institucion-{{$institucion->id}}">
            <i class="far fa-save"></i>
            Actualizar
        </button>
    @endif

{!! Form::close() !!}

@section('scripts')
    @parent

    <script type="text/javascript">

        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                //alert('123');
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                var input = '[name='+name+']';
                $(input).val(checked); console.log($(input).val());
            });
        });

        $(document).ready(function() {
            if ( {{(Auth::user()->isAdmin()) ? 0:1}} ) {
                $('#form-update-institucion').find('input, textarea, button, select').attr('disabled','disabled');
            }

        });

    </script>

@endsection
