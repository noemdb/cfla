<div class="panel panel-info">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-3">
                <i class="fa fa-user fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
                <div class="huge">{{ Auth::user()->id }}</div>
                <div><strong>{{ Auth::user()->username }}</strong></div>
            </div>
        </div>
    </div>
</div>