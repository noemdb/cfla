
<div class="card mt-2">
    <div class="card-header pb-0 mb-0 alert-secondary">
        <h5>
            <i class="{{ $icon_menus['ayudas'] }} fa-1x text-info"></i>
            Videos Instruccionales
        </h5>
    </div>
    <div class="card-boby">

        <div class="row p-2">
            <div class="col-sm-12">

                {{-- <div class="text-center embed-responsive embed-responsive-16by9">
                    <iframe src="https://drive.google.com/file/d/1755mYOtymTcL9B0zSpABGjZg72kcgGgK/preview"></iframe>
                </div> --}}

                @includeif('profesors.modals.home.helps')
                {{-- /home/nuser/code/s2021/resources/views/profesors/modals/home/helps.blade.php --}}

            </div>
        </div>

    </div>
</div>
