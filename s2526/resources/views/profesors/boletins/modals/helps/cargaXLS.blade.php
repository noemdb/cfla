<!-- Button trigger modal -->
<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modelId">
    <i class="{{ $icon_menus['ayuda'] ?? ''}} fa-1x"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archivo XLS de ejemplo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="p-1">
                    <table class="table table table-bordered">
                        <thead>
                            <tr class="table-light">
                                <th>&nbsp;</th>
                                <th class=" text-left">Email</th>
                                <th class=" text-left">Nota</th>
                            </tr>
                            <tr class="table-secondary">
                                <th>&nbsp;</th>
                                <th>A</th>
                                <th>B</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-bold table-secondary">1</td>
                                <td>naiara.zoe.blanco.porras@uefrayluisamigosf.com</td>
                                <td>15</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">2</td>
                                <td>naia.noa.peña.terrazas@uefrayluisamigosf.com</td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td class=" table-secondary">3</td>
                                <td>dario.andrés.esteve.gonzález@uefrayluisamigosf.com</td>
                                <td>18</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">4</td>
                                <td>patricia.manuela.duarte.sanz@uefrayluisamigosf.com</td>
                                <td>05</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">5</td>
                                <td>alberto.rosario.caraballo.avilés@uefrayluisamigosf.com</td>
                                <td>20</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">...</td>
                                <td>...</td>
                                <td>...</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">...</td>
                                <td>...</td>
                                <td>...</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold table-secondary">...</td>
                                <td>...</td>
                                <td>...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
