<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="estudiant_id" class="font-weight-bold m-0">Estudiante</label>
                <div class="input-group">
                    <div class="input-group-append" style="z-index: 0;">
                        <input type="text" class="form-control small" placeholder="CI o nombre" id="help_estudiant">
                    </div>
                    @php $name = 'estudiant_id' @endphp
                    {!! Form::select($name, $list_estudiants, null, [
                        'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                        'wire:model' => $name,
                        'id' => $name,
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'profesor_id' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_profesor, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'pensum_id' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_pensum, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'type' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_type, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'motive' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_motive, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'description' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <textarea class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" rows="3" placeholder="{{ $list_comment[$name] }}"></textarea>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'destination' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="text" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" placeholder="{{ $list_comment[$name] }}">
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'date' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="date" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" required>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'time' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="time" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" required>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_guardian' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_teacher' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_manager' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_status, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status_emergency' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
    document.addEventListener('livewire:load', function() {
        // Función para filtrar estudiantes en tiempo real
        function initializeStudentFilter() {
            const helpEstudiant = document.getElementById('help_estudiant');
            const estudiantSelect = document.getElementById('estudiant_id');

            if (helpEstudiant && estudiantSelect) {
                helpEstudiant.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const options = estudiantSelect.getElementsByTagName('option');

                    for (let i = 0; i < options.length; i++) {
                        const option = options[i];
                        const text = option.textContent.toLowerCase();
                        if (text.includes(searchTerm) || option.value === '') {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            }
        }

        // Inicializar cuando el componente se carga
        initializeStudentFilter();

        // Reinicializar cuando Livewire actualice el DOM
        document.addEventListener('livewire:update', function () {
            setTimeout(initializeStudentFilter, 100);
        });
    });

    // jQuery alternative para filtro de estudiantes
    if (typeof jQuery !== 'undefined') {
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
                $(textbox).on('input', function() {
                    var options = $(select).empty().scrollTop(0).data('options');
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search, 'gi');

                    $.each(options, function(i) {
                        var option = options[i];
                        if (option.text.match(regex) !== null || option.value === '') {
                            $(select).append(
                                $('<option>').text(option.text).val(option.value)
                            );
                        }
                    });
                    if (selectSingleMatch === true && $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                });
            });
        };

        $(document).ready(function() {
            $('#estudiant_id').filterByText($('#help_estudiant'), true);
        });
    }
</script>
@endsection
