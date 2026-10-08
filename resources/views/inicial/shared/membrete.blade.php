{{--
    Membrete imprimible del módulo de Educación Inicial.

    PARCIAL COMPARTIDO por todos los formatos imprimibles del módulo
    (semanal, quincenal, proyecto, especial, evaluación, informe final).

    Port de `saefl/s2526/resources/views/livewire/inicial/formats/eiplanningwk/membrete.blade.php`,
    que estaba duplicado en cada carpeta `formats/*` con el mismo contenido.

    Espera `$titulo` (el nombre del documento que se imprime) más `$institucion`
    y `$pescolar`, que pasan los controladores.

    Adaptaciones frente al legacy:
      · el período académico se lee de `$pescolar?->name` (modelo) en lugar de
        `Session::get('pescolar_name')`, que en cfla no existe;
      · `$institucion` puede ser null (base vacía) y la vista degrada con un
        nombre fijo en vez de reventar con "Trying to get property of null".
--}}
<table width="100%" cellpadding="0" cellspacing="0"
    style="font-size:0.8rem;margin-bottom:0.5rem;padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" src="{{ asset('images/avatar/uecfla.jpg') }}" alt="Escudo">
            </th>
            <th>
                <div class="title"><b>República Bolivariana de Venezuela</b></div>
                <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                <div class="title"><b>{{ $institucion?->name ?? 'U.E. COLEGIO FRAY LUIS AMIGÓ' }}</b></div>
                <div style="color:#666;padding:0;margin:0;">
                    <b>Coordinación Académica</b><br>
                    <span>PERIODO ACADÉMICO {{ $pescolar?->name ?? '' }}</span>
                </div>
                <div class="title"><b>{{ $titulo ?? 'PLAN SEMANAL' }}</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" src="{{ asset('images/avatar/amigoniano.png') }}" alt="Amigoniano">
            </th>
        </tr>
    </thead>
</table>
