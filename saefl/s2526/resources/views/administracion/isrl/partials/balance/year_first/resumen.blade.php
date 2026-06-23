<h4>
    Información Relevante
</h4>
<table class="table table-striped table-hover text-muted table-sm">
    <thead>
        <tr>
            <th>Concepto</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Estudiantes considerados</th>
            <td>{{ $estudiants->count() }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Estudiantes con planes benéficos</th>
            <td>{{ $estudiants_plan_beneficos->count() }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Anualidades</th>
            <td>
                Matrícula/Inscripción
                <span class="float-right font-weight-bold">$ 18</span>
            </td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Mensualidades (Cuotas)</th>
            <td>
                @foreach ($cuentaxpagars as $cuentaxpagar)
                    <div class="">
                        @php $name = ucfirst_accents($cuentaxpagar->name) @endphp
                        {{-- <span class="text-capitalize"> --}}
                            {{-- <span class="text-lowercase"> --}}
                                {{$name ?? '' }}
                            {{-- </span> --}}
                        {{-- </span> --}}
                        <span class="float-right font-weight-bold">$ 18</span>
                    </div>
                @endforeach
            </td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Monto ingresado a Banco por Concepto de Cobranzas</th>
            <td>
                <span class=" font-weight-bold text-nowrap">
                    {{-- $ {{ f_float($rettotal_exchange_ammount_ingresos) }} || Bs {{ f_float($rettotal_ammount_ingresos) }} --}}
                    $ {{ f_float($total_exchange_ammount_pagos) }} || Bs {{ f_float($total_ammount_pagos) }}
                </span>
            </td>
        </tr>
    </tbody>
</table>
