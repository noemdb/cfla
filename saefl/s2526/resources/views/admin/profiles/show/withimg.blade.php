@isset($profile)

    <div class="container">

        <div class="row">

            <div class="col-sm-3 align-self-top">

                <img alt="{{$profile->user->username}}" class="img-thumbnail img-rounded" src="{{ (isset($profile->url_img)) ? asset($profile->url_img) : asset('images/avatar/user_default.png') }}">

            </div>

            <div class="col-sm-9">
              
                {{-- Partial con los tabs de usuario (perfiles, roles) --}}
                @include('admin.profiles.show.profile')

            </div>
        </div>

    </div>

@endisset