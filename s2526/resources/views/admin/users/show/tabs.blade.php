<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">General</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Perfíl</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Roles</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active pt-2" id="home" role="tabpanel" aria-labelledby="home-tab">
      @include('admin.users.show.user')
      <a class="btn btn-warning w-100" href="{{ route('users.edit',$user->id) }}" role="button">
        Actualzar
        <i class="{{$icon_menus['user'] ?? ''}}"></i>
      </a>
  </div>

  <div class="tab-pane fade pt-2" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      @isset($user->profile)
        @php ($profile = $user->profile)
        @include('admin.profiles.show.profile')
        <a class="btn btn-warning w-100" href="{{ route('profiles.edit',$profile->id) }}" role="button">
          Actualzar
          <i class="{{$icon_menus['profile'] ?? ''}}"></i>
        </a>
      @endisset
  </div>
  
  <div class="tab-pane fade pt-2" id="contact" role="tabpanel" aria-labelledby="contact-tab">
    @if($user->rols->count()>0)
      @include('admin.rols.show.rols')
    @endif
  </div>
</div>