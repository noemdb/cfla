
<div class="first-of-type" id="step-coll_politicals-index">

    <div class="alert alert-secondary rounded px-4 py-2" style="min-height: 25rem;">

        <div class="alert-light p-4 shadow ">
            <h4 class="px-1">Detalles Generales de la Notificación</h4>
            <div class="px-1 flex-center" style="min-height: 300px;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 flex-center">
                            @php $list_comment = $list_comment_arr['coll_politicals']; @endphp
                            @include('administracion.collections.coll_politicals.form.fields')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="step-start" data-step-hide="step-coll_politicals-index" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Registrar Promesa de Pago &#10148"data-step-show="step-coll_nivels" data-step-hide="step-coll_politicals-index" data-direction="up" />
    </div>
</div>


