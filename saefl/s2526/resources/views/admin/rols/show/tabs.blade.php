<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">General</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="false">Usuario</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Perfíl</a>
  </li>
</ul>

<div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active pt-2" id="home" role="tabpanel" aria-labelledby="home-tab">
      @include('admin.rols.show.rol')
      <a class="btn btn-warning w-100" href="{{ route('rols.edit',$rol->id) }}" role="button">
        Actualizar
        <i class="{{$icon_menus['rol'] ?? ''}}"></i>
      </a>
  </div>

  <div class="tab-pane fade pt-2" id="user" role="tabpanel" aria-labelledby="user-tab">
    @isset($user)
      @include('admin.users.show.user')
      <a class="btn btn-warning w-100" href="{{ route('users.edit',$user->id) }}" role="button">
        Actualizar
        <i class="{{$icon_menus['user'] ?? ''}}"></i>
      </a>
    @endisset
  </div>

  <div class="tab-pane fade pt-2" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    @isset($profile)
      @include('admin.profiles.show.profile')
      <a class="btn btn-warning w-100" href="{{ route('profiles.edit',$profile->id) }}" role="button">
        Actualizar
        <i class="{{$icon_menus['profile'] ?? ''}}"></i>
      </a>
      @endisset
  </div>

</div>