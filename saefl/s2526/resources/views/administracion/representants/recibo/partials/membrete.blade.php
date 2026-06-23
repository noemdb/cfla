<table cellpadding="0" cellspacing="0"
    style="width: 100%; font-size:0.6rem;margin-bottom:0.1rem; padding-bottom:0.1rem;border:1px solid #ccc">
    <thead>
        <tr>
            <th>
                <img width="40px" height="40px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
            </th>
            <th class="no_wrap">
                <div><b>República Bolivariana de Venezuela</b></div>
                <div><b>Ministerio del Poder Popular para la Educación</b></div>
                <div><b>{{ $institucion->name }}</b></div>
                <div class="text-muted pt-0 pb-0"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th>
                <img width="85px" height="40px" class="card-img-top"
                    src="{{ asset('images/avatar/amigoniano.png') }}">
            </th>
        </tr>
        <tr>
            <th scope="row" align="right" colspan="3" style=" font-size:0.6rem;">
                N°: <span>{{ $registro_pago_combinado->correlative }}</span> ||
                Fecha: <span>{{ $registro_pago_combinado->created_at->format('d-m-Y') }}</span> ||
                Año Escolar: <span>{{ Session::get('pescolar_name') }}</span>
            </th>
        </tr>
    </thead>
</table>
