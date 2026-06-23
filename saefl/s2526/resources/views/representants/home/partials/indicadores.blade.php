<div class="card mt-2">
    <div class="card-header pb-0 mb-0 alert-secondary">
        <h4>
            <i class="{{ $icon_menus['chartline'] }} fa-1x text-danger"></i>
            Indicadores
        </h4>
    </div>
    <div class="card-boby">

        {{-- <div class="container-fluid"> --}}
        <div class="container-fluid">
            <div class="row p-2">
                <div class="col-sm-12">
                    @includeIf('representants.home.partials.labels.estudiantil')
                </div>
            </div>
            <hr>

            <div class="row p-2">
                <div class="col-sm-12">
                    @includeIf('representants.home.partials.labels.saldos')
                </div>
            </div>
            <hr>

            <div class="row p-2">
                <div class="col-sm-12">
                    @includeIf('representants.home.partials.labels.registropagos')
                </div>
            </div>
            <hr>
        </div>

        {{-- <div class="row p-1">
            <div class="col-sm-12">
                @include('representants.charts.controls.evaluacions.actividades')
            </div>
        </div>
        <hr class=" py-1"> --}}

    </div>
</div>

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
@endsection
