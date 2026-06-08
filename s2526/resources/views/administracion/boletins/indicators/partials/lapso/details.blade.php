<div class="p-2" role="alert">
    <div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item py-1">
                <div class="d-flex">
                    <div class="font-weight-bold">Fecha de Inicio/Fin:</div>
                    <div class="mx-2">{{f_date($lapso->finicial)}}</div>
                    <div class="mx-2">||</div>
                    <div class="mx-2">{{f_date($lapso->ffinal)}}</div>
                </div>
            </li>
            <li class="list-group-item py-1">
                <div class="d-flex">
                    <div class="font-weight-bold">Fecha Corte de Notas:</div>
                    <div class="mx-2">{{f_date($lapso->date_cutnote)}}</div>
                </div>
            </li>
        </ul>
    </div>
</div>
