<div class="form-group">
    <label for="pestudio_id" class="m-0 font-weight-bold text-secondary">Plan de Estudio</label>
    {!! Form::select('pestudio_id', $list_pestudio, old('pestudio_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="code" class="m-0 font-weight-bold text-secondary">Código (Hasta 10 caracteres)</label>
    {!! Form::text('code', old('code'), [
        'maxlength' => '10',
        'class' => 'form-control',
        'placeholder' => 'Código',
        'id' => 'code',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="code_sm" class="m-0 font-weight-bold text-secondary">Abreviación (Sólo dos letras)</label>
    {!! Form::text('code_sm', old('code_sm'), [
        'maxlength' => '4',
        'class' => 'form-control',
        'placeholder' => 'Abreviación',
        'id' => 'code_sm',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="name" class="m-0 font-weight-bold text-secondary">Nombre</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => 'Nombre',
        'id' => 'name',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="order" class="m-0 font-weight-bold text-secondary">Orden</label>
    {!! Form::selectRange('order', 1, 12, old('order'), ['class' => 'form-control', 'placeholder' => '']) !!}

</div>

<div class="form-group">
    <label for="hour_t_week" class="m-0 font-weight-bold text-secondary">Número de horas teóricas dictadas en la
        semana</label>
    {{-- {!! Form::select('hour_t_week',$arr_number,old('hour_t_week'),['class'=>'form-control','placeholder'=>'Seleccione','required']) !!} --}}
    {!! Form::selectRange('hour_t_week', 0, 10, old('hour_t_week'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="hour_t_week" class="m-0 font-weight-bold text-secondary">Número de horas prácticas dictadas en la
        semana</label>
    {{-- {!! Form::select('hour_p_week',$arr_number,old('hour_p_week'),['class'=>'form-control','placeholder'=>'Seleccione','required']) !!} --}}
    {!! Form::selectRange('hour_p_week', 0, 10, old('hour_p_week'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

{{-- {!!Form::select('size', ['L' => 'Large', 'S' => 'Small']) !!}
{!! Form::selectRange('number', 10, 20) !!}
{!! Form::date('name', \Carbon\Carbon::now()->format('Y-m-d'),$attributes = ['class'=>'form-control']) !!} --}}

@component('administracion.elements.forms.check')
    @slot('name', 'enable_academic_index')
    @slot('id', 'enable_academic_index')
    @slot('value', $asignatura->enable_academic_index)
    @slot('label', 'Tomada en cuenta para índice o promedio académico')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'enable_lost_regulation')
    @slot('id', 'enable_lost_regulation')
    @slot('value', $asignatura->enable_lost_regulation)
    @slot('label', 'Sujeta a pérdida por reglamento')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'enable_official_doc')
    @slot('id', 'enable_official_doc')
    @slot('value', $asignatura->enable_official_doc)
    @slot('label', 'Mostrar en documentos oficiales')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'enable_repairable')
    @slot('id', 'enable_repairable')
    @slot('value', $asignatura->enable_repairable)
    @slot('label', 'Reparable')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'enable_grupo_estable')
    @slot('id', 'enable_grupo_estable')
    @slot('value', $asignatura->enable_grupo_estable)
    @slot('label', 'Contiene grupo estable')
@endcomponent

<div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-secondary">Observaciones</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones',
        'id' => 'observations',
    ]) !!}
</div>

<div class="form-group">
    <label for="prelacions" class="m-0 font-weight-bold text-secondary">Prelaciones</label>
    {!! Form::text('prelacions', old('prelacions'), [
        'class' => 'form-control',
        'placeholder' => 'Prelaciones',
        'id' => 'prelacions',
    ]) !!}
</div>

@section('scripts')
    @parent

    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function(e) {
                //alert('123');
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');
                console.log(name);
                var checked = $(this).prop('checked');
                console.log(checked);
                var input = '[name=' + name + ']';
                $(input).val(checked);
                console.log($(input).val());
            });
        });
        $('#code').keyup(function() {
            this.value = this.value.toUpperCase();
            console.log(this.value);
        });
        $('#code_sm').keyup(function() {
            this.value = this.value.toUpperCase();
            console.log(this.value);
        });
    </script>
@endsection
