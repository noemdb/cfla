<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6">
            @include('controls.charts.audits.usages.logdbsrols')

        </div>
        <div class="col-xl-6">
            @include('controls.charts.audits.usages.logdbsmonths')

        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 offset-xl-2">
            @include('controls.charts.audits.usages.logdbsusers')
        </div>
    </div>
</div>
