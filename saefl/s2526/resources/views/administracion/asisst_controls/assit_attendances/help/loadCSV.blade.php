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
                    <img class="img-fluid img-thumbnail rounded shadow-sm" src="{{ asset('images/help/asisst_controls/loadCSV/1.png') }}" alt="Card image cap">
                </div>
            </td>
            </tr>
            <tr>
            <th align="left" style="padding:4rem;" > 2. Clic sobre la opción:
                <span class=" font-weight-bolder border rounded p-2">
                    <i class="{{ $icon_menus['csv'] ?? '' }}  text-success"></i>
                    Cargar Marcajes
                </span>
            </th>
        </tr>

        <tr>
            <th align="left" style="padding:4rem;" >
                3. Clic en sobre <span class=" font-weight-bold">[Seleccionar archivo CSV]</span>
            </th>
            <td style="padding:4rem;"  rowspan="2" align="center" >
                <div class="text-center" style="max-width: 35rem">
                    <img  class="img-fluid img-thumbnail rounded shadow-sm" src="{{ asset('images/help/asisst_controls/loadCSV/2.png') }}" alt="Card image cap">
                </div>
            </td>
        </tr>
        <tr>
            <th align="left" style="padding:4rem;" > 4. Clic sobre el icono:
                <button class="btn btn-info my-2 my-sm-0" type="button">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
            </th>
        </tr>

        <tr>
            <th align="left" style="padding:4rem;" >
                5. Clic sobre el icono:
                    <button type="button" class="btn-boletin btn btn-primary">
                        <i class="fa fa-save" aria-hidden="true"></i>
                        Guardar
                    </button>
            </th>
            <td style="padding:4rem;"  align="center" >
                <div class="text-center" style="max-width: 35rem">
                    <img  class="img-fluid img-thumbnail rounded shadow-sm" src="{{ asset('images/help/asisst_controls/loadCSV/3.png') }}" alt="Card image cap">
                </div>
            </td>
        </tr>


    </tbody>
</table>
