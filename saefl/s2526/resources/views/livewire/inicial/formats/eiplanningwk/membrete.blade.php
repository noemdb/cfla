<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.5rem; padding-bottom:0.2rem; ">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
            </th>
            <th>
                <div class="title"><b>República Bolivariana de Venezuela</b></div>
                <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="text-muted pt-0 pb-0">
                    <b>Coordinación Académica</b> <br>
                    <span>PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}</span>
                </div>
                <div class="title"><b>PLAN SEMANAL</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
            </th>
        </tr>
    </thead>
</table>
