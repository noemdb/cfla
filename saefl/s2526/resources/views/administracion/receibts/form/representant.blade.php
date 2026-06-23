{{-- 'recibo_id', 'name', 'exchange_ammount'  --}}

<div class="container-fluid">

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
