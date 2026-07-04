@php $profesor=$pevaluacion->profesor; @endphp

{{ Form::hidden('pensum_id', $pensum->id) }}
{{ Form::hidden('grado_id', $grado->id) }}
{{ Form::hidden('seccion_id_old', $seccion->id) }}
{{ Form::hidden('lapso_id', $lapso->id) }}
{{ Form::hidden('profesor_id', $profesor->id) }}

<div class="form-group">
    <label for="description" class="m-0 font-weight-bold text-muted">Descripción</label>
    {!! Form::text('description', old('description'), [
        'readonly',
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'description',
        'required',
    ]) !!}
</div>

<label for="profesor_id" class="m-0 font-weight-bold text-muted">Profesor</label>
<div class="input-group-text mb-3">
    {{ $profesor->fullname ?? '' }}
</div>

@if (Request::is('*create*'))
    <label for="seccion_id_arr" class="m-0 font-weight-bold text-muted">Secciones</label>
    <div class="row mb-3">
        @foreach ($seccions as $item)
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php $checked = ($seccion->id == $item->id) ? 'checked':null; @endphp
                            {!! Form::checkbox('seccion_id[' . $item->id . ']', 'true', $checked) !!}
                        </div>
                    </div>
                    <div class="form-control">{!! Form::label('seccion_id', $item->name) !!}</div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if (Request::is('*edit*'))
    {{ Form::hidden('seccion_id', $seccion->id) }}
@endif

<div class="form-group">
    <label for="nota_type" class="m-0 font-weight-bold text-muted">Tipo de nota final</label>
    {!! Form::select('nota_type', $tipo_list, old('nota_type'), [
        'id' => 'nota_type',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group" id="div_escala_id">
    <label for="escala_id" id="label_escala_id" class="m-0 font-weight-bold text-muted">Escala de Evaluación</label>
    {!! Form::select('escala_id', $escala_list, old('escala_id'), [
        'id' => 'escala_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_baremo" class="m-0 font-weight-bold text-muted">Baremo</label>
    {!! Form::select('status_baremo', ['true' => 'SI', 'false' => 'NO'], old('status_baremo'), [
        'id' => 'status_baremo',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>


{{-- @php $description = "Planificación del ".$lapso->name; @endphp
  <div class="form-group">
    <label for="description" class="m-0 font-weight-bold text-muted">Descripción</label>
    {!! Form::text('description', $description, ['readonly','class' => 'form-control','placeholder'=>'Descripción','id'=>'Descripción','required']) !!}
  </div> --}}

{{-- <div class="form-group">
    <label for="objetivo" class="m-0 font-weight-bold text-muted">Objetivo</label>
    {!! Form::text('objetivo', old('objetivo'), ['class' => 'form-control','placeholder'=>'Objetivo','id'=>'objetivo']); !!}
  </div> --}}

{{-- <div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-muted">Observación</label>
    {!! Form::text('observations', old('observations'), ['class' => 'form-control','placeholder'=>'Observación','id'=>'observations']); !!}
  </div> --}}

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#nota_type').change(function(e) {
                switch ($(this).val()) {
                    case "ACUMULATIVA":
                        $("#div_escala_id").fadeOut();
                        $("#label_escala_id").html('Escala de la nota final');
                        $("#div_escala_id").fadeIn();
                        break;
                    case "PROMEDIADA":
                        $("#div_escala_id").fadeOut();
                        $("#label_escala_id").html('Escala de las Evaluaciones');
                        $("#div_escala_id").fadeIn();
                        break;
                    default:
                        $("#div_escala_id").fadeOut();
                        break;
                }
            });
        });
    </script>
@endsection
