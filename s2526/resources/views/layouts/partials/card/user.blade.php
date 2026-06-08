<div class="card bd-callout bd-callout-{{{ Auth::user()->is_active=="Activo" ? 'primary' : 'default' }}}"
    style="margin-top: 0rem !important;">

    <div class="text-center">
        <img class="card-img-top" style="width: 6rem; margin-left: auto; margin-right: auto;"
            src="{{ (isset($profile->url_img)) ? asset($profile->url_img) : asset('images/avatar/user_default.png') }}"
            alt="Card image cap">
    </div>

    <div class="card-body">
        <h5 class="card-title text-center">
            <b>{{ Auth::user()->username }}</b>
        </h5>


        <div class="card-text bd-callout bd-callout-{{Auth::user()->class}}">
            <small>
                <table class="table table-sm mb-1">
                    <tr>
                        <th scope="row"><i class="fas fa-user"></i></th>
                        <td class="text-muted">{{ Auth::user()->profile->full_name }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="far fa-envelope"></i></th>
                        <td class="text-muted">{{ Auth::user()->email }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="far fa-address-book"></i></th>
                        <td class="text-muted">
                            {{ Auth::user()->getUserRol() }}
                        </td>
                    </tr>
                </table>
            </small>
        </div>

    </div>

</div>