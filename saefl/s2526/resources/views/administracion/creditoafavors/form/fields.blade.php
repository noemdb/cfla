<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class=" d-block">
                <label for="exchange_ammount"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                <div class="form-group">
                    {!! Form::text('exchange_ammount', old('exchange_ammount'), [
                        'class' => 'form-control',
                        'placeholder' => $list_comment['exchange_ammount'],
                        'required',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <label for="representant_id" class="m-0">Representante</label>
            <div class="input-group pb-3">
                <div class="input-group-append" style="z-index: 0;">
                    {!! Form::text('help_representante', $help_representante, [
                        'class' => 'form-control',
                        'placeholder' => 'CI del representante',
                        'id' => 'help_representante',
                    ]) !!}
                </div>
                {!! Form::select('representant_id', $list_representant, old('representant_id'), [
                    'class' => 'form-control',
                    'id' => 'representant_id',
                    'required' => 'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

        </div>
    </div>
</div>

@section('scripts')
    @parent

    <script type="text/javascript">
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
