<div class="card">
    <div class="card-body">
        <div class="form-group">
            {{-- @php $readonly = (Request::is('*edit*')) ? 'readonly':null ; @endphp --}}
            @php $readonly = true; @endphp
            <label for="ci_representant" class="m-0">Número de cédula</label>
            {!! Form::text('ci_representant', old('ci_representant'), [
                'class' => 'form-control',
                $readonly,
                'placeholder' => 'Número de cédula (sólo números)',
                'id' => 'ci_representant',
                'required',
            ]) !!}
        </div>
        <div class="form-group">
            <label for="name" class="m-0">Apellidos y Nombres</label>
            {!! Form::text('name', old('name'), [
                'class' => 'form-control',
                'placeholder' => 'Apellidos y Nombres',
                'id' => 'name',
                'required',
            ]) !!}
        </div>

        <div class="form-group">
            <label for="phone" class="m-0">
                Números de Teléfono
                <span class="font-weight-bold text-muted pl-2">[Ingresa los números separados por un / ] </span>
            </label>
            {!! Form::text('phone', old('phone'), [
                'class' => 'form-control',
                'placeholder' => 'Número de Teléfono',
                'id' => 'phone',
                '',
            ]) !!}
            @if ($representant->phone_old ?? false)
                <div class="text-muted text-right"> Telef. Antíguo: <span>{{ $representant->phone_old ?? null }}</span>
                </div>
            @endif
        </div>

        <div class="form-group alert alert-success">
            @php $status_whatsapp_verify = $representant->status_whatsapp_verify ?? false; @endphp
            <label for="phone" class="m-0">
                <span>Número de Whatsapp</span> <span class="font-weight-bold text-muted pl-2">Ej: 584145457890</span>
            </label>
            <div class="input-group mb-0">
                {!! Form::text('whatsapp', old('whatsapp'), [
                    'class' => 'form-control',
                    'placeholder' => 'Número WhatsApp (Ej: 584145457890)',
                    'id' => 'whatsapp',
                    '',
                ]) !!}
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa {{ $status_whatsapp_verify ? 'fa-check text-bg-success' : 'fa-window-close text-danger' }}"
                            aria-hidden="true"></i>
                    </span>
                </div>
            </div>
            <div id="error"></div>
            <div class="text-right font-weight-bold text-{{ $status_whatsapp_verify ? 'success' : 'danger' }}">
                {{ $status_whatsapp_verify ? 'Verificado' : 'NO Verificado' }}</div>

        </div>

        <div class="form-group">
            <label for="email" class="m-0">Correo electrónico</label>
            {!! Form::text('email', old('email'), [
                'class' => 'form-control',
                'placeholder' => 'Correo electrónico',
                'id' => 'email',
                '',
            ]) !!}
        </div>

        @admin
            <div class="form-group">
                <label for="status_active" class="m-0">Estado</label>
                {!! Form::select('status_active', ['true' => 'Activo', 'false' => 'Desactivo'], old('status_active'), [
                    'class' => 'form-control',
                    'id' => 'status_active',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
            </div>
            <div class="form-group">
                <label for="user_id" class="m-0">{{ $list_comment['user_id'] }}</label>
                {!! Form::select('user_id', $user_list, old('user_id'), [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        @endadmin

        @admon
            <div class="form-group">
                <label for="status_blacklist" class="m-0">{{ $list_comment['status_blacklist'] ?? null }}</label>
                {!! Form::select('status_blacklist', ['true' => 'SI', 'false' => 'NO'], old('status_blacklist'), [
                    'class' => 'form-control form-control-sm',
                    'id' => 'status_blacklist',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        @endadmon

        @control
            <div class="form-group">
                <label for="status_adviders" class="m-0">{{ $list_comment['status_adviders'] ?? null }}</label>
                {!! Form::select('status_adviders', ['true' => 'SI', 'false' => 'NO'], old('status_adviders'), [
                    'class' => 'form-control form-control-sm',
                    'id' => 'status_adviders',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        @endcontrol

    </div>
</div>

@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
@endsection


@section('scripts')
    @parent

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const phoneInput = document.querySelector("#whatsapp");
            const form = document.querySelector("#formRepresentant");
            const errorMessage = document.querySelector("#error");

            // Validación del formato de número telefónico
            const validatePhone = (phone) => {
                const phonePattern = /^\d{11,12}$/;
                return phonePattern.test(phone);
            };

            form.addEventListener("submit", (event) => {
                const phoneValue = phoneInput.value.trim();

                if (!validatePhone(phoneValue)) {
                    event.preventDefault(); // Evita el envío del formulario si no es válido
                    errorMessage.textContent =
                        "Por favor, ingresa un número de teléfono válido (Ej: 584123456789)";
                    errorMessage.style.color = "red";
                } else {
                    errorMessage.textContent = ""; // Limpia el mensaje de error si es válido
                }
            });
        });
    </script>
@endsection
