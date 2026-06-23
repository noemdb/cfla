<div class="card-body p-1 m-1">
    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
            <a class="nav-item nav-link show active text-left" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                Accesos al SAEFL por Usuarios/Roles/Meses.
            </a>
            <a class="nav-item nav-link text-left" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                Intensidad del uso de la base de datos (BD) por Usuarios/Roles/Meses.
            </a>
        </div>
    </nav>
    <div class="tab-content border border-top-0" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="p-3">
                @include('academicos.audits.charts.partials.loginouts')
            </div>
        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="p-3">
                @include('academicos.audits.charts.partials.logdbs')
            </div>
        </div>
    </div>
</div>
