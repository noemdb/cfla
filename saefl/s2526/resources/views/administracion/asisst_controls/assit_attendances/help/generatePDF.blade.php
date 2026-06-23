<table class="table" cellspacing="5" cellpadding="5" style="padding:4rem;">
    <tbody>
        <tr>
            <th align="left" style="padding:4rem;" >
                1. Clic en la barra de menús, sobre el icono
                <a class="btn btn-success btn.sm" title="Control de Asistencia" role="button">
                    <i class="{{ $icon_menus['asisst_controls'] ?? '' }} fa-1x text-light"></i>
                </a>
            </th>
            <td style="padding:4rem;"  rowspan="2" align="center">
                <div class="text-center" style="max-width: 35rem">
                    <img class="img-fluid img-thumbnail rounded shadow-sm" src="{{ asset('images/help/asisst_controls/generatePDF/1.png') }}" alt="Card image cap">
                </div>
            </td>
        </tr>
        <tr>
            <th align="left" style="padding:4rem;" > 2. Clic sobre la opción:
                <span class=" font-weight-bolder border rounded p-2">
                    <i class="{{ $icon_menus['pdf'] ?? '' }}  text-dark"></i>
                    Formato de Asistencia
                </span>
            </th>
        </tr>

        <tr>
            <th align="left" style="padding:2rem 4rem 2rem 4rem;" class=" font-weight-bold">
                3. Clic en sobre <span class=" font-weight-bold">Fecha Inicial</span> <span class=" text-muted"> para seleccionar la fecha de incio</span>
                {{-- <ul class="list-group list-group-flush">
                    <li class="list-group-item"> 3. Clic en sobre <span class=" font-weight-bold">Fecha Inicial</span> para seleccionar la fecha de incio</li>
                    <li class="list-group-item"> 4. Clic en sobre <span class=" font-weight-bold">Fecha Final</span> para seleccionar la fecha de final</li>
                    <li class="list-group-item"> 5. Clic en sobre <span class=" font-weight-bold">Horarios</span> para seleccionar el horario de trabajo</li>
                    <li class="list-group-item"> 6. Clic en sobre  <button type="button" class="btn-boletin btn btn-info"><i class="fa fa-search" aria-hidden="true"></i>Buscar</button> para buscar los marcajes</li>
                    <li class="list-group-item"> 7. Clic en sobre  <button type="button" class="btn-boletin btn btn-dark"><i class="fa fa-file-pdf" aria-hidden="true"></i></button> para generar <b>Formato de Asistencia</b></li>
                </ul> --}}
            </th>
            <td style="padding:2rem 4rem 2rem 4rem;" rowspan="4" align="center" >
                <div class="text-center" style="max-width: 35rem">
                    <img  class="img-fluid img-thumbnail rounded shadow-sm" src="{{ asset('images/help/asisst_controls/generatePDF/2.png') }}" alt="Card image cap">
                </div>
            </td>
        </tr>
        <tr>
            <th align="left" style="padding:2rem 4rem 2rem 4rem;" >
                4. Clic en sobre <span class=" font-weight-bold">Fecha Final</span> <span class=" text-muted"> para seleccionar la fecha de final</span>
            </th>
        </tr>
        <tr>
            <th align="left" style="padding:2rem 4rem 2rem 4rem;" >
                5. Clic en sobre <span class=" font-weight-bold">Horarios</span> <span class=" text-muted"> para seleccionar el horario de trabajo</span>
            </th>
        </tr>
        <tr>
            <th align="left" style="padding:2rem 4rem 2rem 4rem;" >
                6. Clic en sobre  <button type="button" class="btn-boletin btn btn-info"><i class="fa fa-search" aria-hidden="true"></i>Buscar</button> <span class=" text-muted"> para buscar los marcajes</span>
            </th>
        </tr>
        <tr>
            <th align="left" style="padding:2rem 4rem 2rem 4rem;" >
                7. Clic en sobre  <button type="button" class="btn-boletin btn btn-dark"><i class="fa fa-file-pdf" aria-hidden="true"></i></button> <span class=" text-muted"> para generar <b>Formato de Asistencia</b></span>
            </th>
        </tr>

    </tbody>
</table>
