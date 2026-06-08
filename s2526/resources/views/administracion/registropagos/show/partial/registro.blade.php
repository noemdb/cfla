@admin
    <dl class="mt-1 mb-1 pt-1">
        <dt>IDENTIFICADOR</dt>
        <dd>
            <span id="credito_a_ammount" class="">
                {{$registropago->id ?? ''}}
            </span>
        </dd>
    </dl>
@endadmin
<dl class="mb-1">
    <dt>ESTUDIANTE</dt>
    <dd>
        <span id="credito_a_ammount" class="">
            {{$registropago->estudiant->fullname ?? ''}}
        </span>
    </dd>
</dl>

<dl class="mb-1">
    <dt>CUENTA POR COBRAR</dt>
    <dd>
        <span id="credito_a_ammount" class="">
            {{$registropago->cuentaxpagar->name ?? ''}}
        </span>
    </dd>
</dl>

<dl class="mb-1">
    <dt>OBSERVACIÓN</dt>
    <dd>
        <span id="ingreso_observations" class="">
            {{$registropago->ingreso->ingreso_observations ?? ''}}
        </span>
    </dd>
</dl>

<dl class="mb-1">
    <dt>USUARIO</dt>
    <dd>
        <span id="credito_a_ammount" class="">
            {{$registropago->user->username ?? ''}}
        </span>
    </dd>
</dl>

<dl class="mb-1">
    <dt>FECHA DE REGISTRO</dt>
    <dd>
        <span id="credito_a_ammount" class="">
            {{-- {{$registropago->created_at ?? ''}} --}}
            {{ (isset($registropago->created_at)) ? f_date($registropago->created_at) : '' }}
            {{-- {{ (isset($registropago->created_at)) ? Carbon\Carbon::parse($registropago->created_at)->format('d-m-Y h:m:s') : '' }} --}}
        </span>
    </dd>
</dl>
{{-- <dl class="mb-1">
    <dt>updated_at</dt>
    <dd>
        <span id="credito_a_ammount" class="">
            {{$registropago->updated_at ?? ''}}
        </span>
    </dd>
</dl> --}}