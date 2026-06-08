{{-- 'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation' --}}

<div class="container-fluid">

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="coll_political_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['coll_political_id'] ?? '' }}</label>
                {!! Form::select('coll_political_id', $list_coll_politicals, old('coll_political_id'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @php $representant_id = (!empty($representant_id)) ? $representant_id : null ; @endphp
            @php $help_representante = (!empty($help_representante)) ? $help_representante : null ; @endphp
            <label for="representant_id" class="m-0">{{ $list_comment['representant_id'] ?? '' }}</label>
            <div class="input-group pb-3">
                <div class="input-group-append w-25" style="z-index: 0;">
                    {!! Form::text('help_representante', $help_representante, [
                        'class' => 'form-control',
                        'placeholder' => 'CI o nombre',
                        'id' => 'help_representante',
                    ]) !!}
                </div>
                {!! Form::select('representant_id', $list_representant, $representant_id, [
                    'class' => 'form-control w-75',
                    'id' => 'representant_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="date"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['date'] ?? '' }}</label>
                {!! Form::date('date', old('date'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['date'],
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="exchange_ammount"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                {!! Form::text('exchange_ammount', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['exchange_ammount'],
                    'id' => 'exchange_ammount',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="description"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
                {!! Form::text('description', old('description'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['description'],
                    'id' => 'description',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="observation"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['observation'] ?? '' }}</label>
                {!! Form::text('observation', old('observation'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['observation'],
                    'id' => 'observation',
                ]) !!}
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="status_id" class="font-weight-bold text-secondary m-0">{{$list_comment['status_id'] ?? ''}}</label>
                {!! Form::select('status_id',$list_status,old('status_id'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
            </div>
        </div>
        @if (Request::is('*edit*'))
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_messege" class="font-weight-bold text-secondary m-0">{{$list_comment['status_messege'] ?? ''}}</label>
                    {!! Form::select('status_messege',['true'=>'SI','false'=>'NO'],old('status_messege'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_call" class="font-weight-bold text-secondary m-0">{{$list_comment['status_call'] ?? ''}}</label>
                    {!! Form::select('status_call',['true'=>'SI','false'=>'NO'],old('status_call'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                </div>
            </div>
        @endif
    </div> --}}

</div>


@section('scripts')
    @parent

    <script type="text/javascript">
        // fill para el select representante
        $(function() {
            $('#representant_id').filterByText($('#help_representante'), true);
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
