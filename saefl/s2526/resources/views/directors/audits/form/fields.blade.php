  {{ Form::hidden('pevaluacion_id', $pevaluacion->id) }}
  {{ Form::hidden('nota_type', $pevaluacion->nota_type) }}
  {{ Form::hidden('nota_max', $pevaluacion->escala->maximo) }}

  {!! Form::hidden('nota_ctr', '1') !!}


  @php $escala_name = (!empty($pevaluacion->escala->id)) ? $pevaluacion->escala->name : null; @endphp
  @php $escala_id = (!empty($pevaluacion->escala->id)) ? $pevaluacion->escala->id : null; @endphp

  <div class="form-group">
      <label for="description" class="m-0 font-weight-bold text-muted">Descripción / Estrategía</label>
      {!! Form::text('description', old('description'), [
          'class' => 'form-control',
          'placeholder' => 'Descripción / Estrategía',
          'id' => 'description',
          'required',
      ]) !!}
  </div>

  <label for="escala_id" class="m-0 font-weight-bold text-muted">Escala</label>
  <div class="input-group mb-3">
      @switch($pevaluacion->nota_type)
          @case('ACUMULATIVA')
              {!! Form::select('escala_id', $escala_list, old('escala_id'), [
                  'class' => 'form-control',
                  'placeholder' => 'Seleccione',
                  'required',
              ]) !!}
          @break

          @case('PROMEDIADA')
              {!! Form::text('escala_name', $escala_name, [
                  'class' => 'form-control',
                  'placeholder' => 'Escala',
                  'id' => 'escala_name',
                  'readonly',
              ]) !!}
              {!! Form::hidden('escala_id', $escala_id) !!}
          @break

          @default
      @endswitch
  </div>

  <div class="form-group pb-1">
      {!! Form::label('fecha', 'Fecha', ['class' => 'm-0 font-weight-bold text-muted']) !!}
      {!! Form::text('fecha', old('fecha'), [
          'class' => 'form-control datepicker',
          'placeholder' => 'Fecha',
          'id' => 'fecha',
          'required' => 'required',
          'maxlength' => '10',
      ]) !!}
  </div>

  {{-- <div class="form-group">
    <label for="objetivo" class="m-0 font-weight-bold text-muted">Objetivo</label>
    {!! Form::text('objetivo', old('objetivo'), ['class' => 'form-control','placeholder'=>'Objetivo','id'=>'objetivo']); !!}
  </div>

  <div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-muted">Observación</label>
    {!! Form::text('observations', old('observations'), ['class' => 'form-control','placeholder'=>'Observación','id'=>'observations']); !!}
  </div> --}}

  @section('scripts')
      @parent

      <script src="{{ asset('js/accounting.min.js') }}"></script>

      <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js') }}"></script>
      <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js') }}"></script>

      <script type="text/javascript">
          $('.datepicker').datepicker({
              format: "yyyy-mm-dd",
              language: "es",
              autoclose: true,
              startView: 2
          });
      </script>
  @endsection

  @section('stylesheet')
      @parent

      <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

      <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
  @endsection
