<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">General</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="false">Usuario</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Roles</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active pt-2" id="home" role="tabpanel" aria-labelledby="home-tab">
      @include('admin.profiles.show.profile')
      <a class="btn btn-warning w-100" href="{{ route('profiles.edit',$profile->id) }}" role="button">
        Actualizar
        <i class="{{$icon_menus['profile'] ?? ''}}"></i>
      </a>
  </div>

  <div class="tab-pane fade pt-2" id="user" role="tabpanel" aria-labelledby="user-tab">
      @php ($user = $profile->user)
      @include('admin.users.show.user')
      <a class="btn btn-warning w-100" href="{{ route('users.edit',$user->id) }}" role="button">
        Actualizar
        <i class="{{$icon_menus['user'] ?? ''}}"></i>
      </a>
  </div>

  <div class="tab-pane fade pt-2" id="contact" role="tabpanel" aria-labelledby="contact-tab">
      @php ($rols = $profile->user->rols)
      @include('admin.rols.show.rols')
  </div>

</div>