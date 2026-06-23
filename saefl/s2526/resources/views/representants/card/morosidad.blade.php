@php $late_payment = $representant->late_payment; @endphp
@php $indice = round((100 * $late_payment), 1); @endphp
@php $class = ($late_payment > 0) ? 'danger' : 'success'; @endphp

<div class="container h-100">
    <div class="row align-items-center h-100">
        <div class="col-12 mx-1">

            <div class="jumbotron jumbo-info w-100 text-center alert-{{ $class }}">
                <h3 class="d-block mb-1 text-{{ $class }}">
                    <span class="font-weight-bold">{{ $indice ?? '' }}</span><span class="small">%</span>
                </h3>

                <h6 class="font-weight-bolder text-{{ $class }}">
                    Índice de Morosidad
                </h6>
            </div>

        </div>
    </div>
</div>
