<div class="form-group">
    <label for="planpago_id" class="m-0 font-weight-bold text-secondary">{{ $list_comment['planpago_id'] ?? '' }}</label>
    {!! Form::select('planpago_id', $list_planpago, old('planpago_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="name" class="m-0 font-weight-bold text-secondary">{{ $list_comment['name'] ?? '' }}</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['name'],
        'id' => 'name',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="type" class="m-0 font-weight-bold text-secondary">{{ $list_comment['type'] ?? '' }}</label>
    {!! Form::select('type', ['GENERAL' => 'GENERAL', 'INDIVIDUAL' => 'INDIVIDUAL'], old('type'), [
        'class' => 'form-control',
        'id' => 'type',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group" id="estudiant_id_ctr">
    <div class="input-group pb-3">
        <div class="input-group-append" style="z-index: 0;">
            {!! Form::text('help_estudiant', $help_estudiant, [
                'class' => 'form-control small',
                'placeholder' => 'CI o nombre',
                'id' => 'help_estudiant',
            ]) !!}
        </div>
        {!! Form::select('estudiant_id', $list_estudiant, old('estudiant_id'), [
            'class' => 'form-control',
            'id' => 'estudiant_id',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
</div>

<div class="form-group pb-1">
    <label for="date_expiration"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_expiration'] ?? '' }}</label>
    {!! Form::date('date_expiration', old('date_expiration'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['date_expiration'],
        'id' => 'date_transaction',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="date_calendar_start"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_calendar_start'] ?? '' }}</label>
    {!! Form::date('date_calendar_start', old('date_calendar_start'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['date_calendar_start'],
        'id' => 'date_calendar_start',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="date_calendar_end"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_calendar_end'] ?? '' }}</label>
    {!! Form::date('date_calendar_end', old('date_calendar_end'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['date_calendar_end'],
        'id' => 'date_calendar_end',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="description"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['description'] ?? '' }}</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description'],
        'id' => 'description',
        'required',
    ]) !!}
</div>


<div class="form-group pb-1">
    <label for="observations"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['observations'] ?? '' }}</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['observations'],
        'id' => 'observations',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="status_bad" class="m-0 font-weight-bold text-secondary">{{ $list_comment['status_bad'] ?? '' }}</label>
    {!! Form::select('status_bad', ['true' => 'SI', 'false' => 'NO'], old('status_bad'), [
        'class' => 'form-control',
        'id' => 'status_bad',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $("#type").change(function() {
                var type = $(this).val(); //console.log(type);console.log('gradoByseccion/'+type);
                if (type == "INDIVIDUAL") {
                    $("#estudiant_id_ctr").show();
                } else {
                    $("#estudiant_id_ctr").hide();
                }
            });
        });
    </script>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        $(function() {
            $('#estudiant_id').filterByText($('#help_estudiant'), true);
        });

        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
            return this.each(function() {
                var select = this;
                var options = [];
                $(select).find('option').each(function() {
                    options.push({
                        value: $(this).val(),
                        text: $(this).text()
                    });
                });
                $(select).data('options', options);
                $(textbox).bind('change keyup', function() {
                    var options = $(select).empty().scrollTop(0).data('options');
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search, 'gi');

                    $.each(options, function(i) {
                        var option = options[i];
                        if (option.text.match(regex) !== null) {
                            $(select).append(
                                $('<option>').text(option.text).val(option.value)
                            );
                        }
                    });
                    if (selectSingleMatch === true &&
                        $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                });
            });
        };
    </script>
@endsection
