<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#nav-general" role="tab" aria-controls="nav-general" aria-selected="true">General</a>
    <a class="nav-item nav-link" id="nav-user-tab" data-toggle="tab" href="#nav-user" role="tab" aria-controls="nav-user" aria-selected="false">Usuario</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
    @includeIf('admin.loginouts.show.loginout')
    <a class="btn btn-warning w-100" href="{{ route('loginouts.edit',$loginout->id) }}" taske="button">
      Actualizar
      <i class="{{$icon_menus['loginout'] ?? ''}}"></i>
    </a>
  </div>

  <div class="tab-pane fade" id="nav-user" role="tabpanel" aria-labelledby="nav-user-tab">
   
    @isset($user)

      <div class="card m-2">
        <div class="card-body">

          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-item nav-link active" id="nav-guser-tab" data-toggle="tab" href="#nav-guser" role="tab" aria-controls="nav-guser" aria-selected="true">General</a>
              <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Perfil</a>
              <a class="nav-item nav-link" id="nav-messege-tab" data-toggle="tab" href="#nav-messege" role="tab" aria-controls="nav-messege" aria-selected="false">Mensajes</a>
              <a class="nav-item nav-link" id="nav-tasks-tab" data-toggle="tab" href="#nav-tasks" role="tab" aria-controls="nav-tasks" aria-selected="false">Tareas</a>
              <a class="nav-item nav-link" id="nav-alerts-tab" data-toggle="tab" href="#nav-alerts" role="tab" aria-controls="nav-alerts" aria-selected="false">Alertas</a>
            </div>
          </nav>

          <div class="tab-content" id="nav-tabContent">

            <div class="tab-pane fade show active m-1 p-1" id="nav-guser" role="tabpanel" aria-labelledby="nav-guser-tab">
              @include('admin.users.show.user')
              <a class="btn btn-info w-100" href="{{ route('users.show',$user->id) }}" taske="button">
                Mostrar
                <i class="{{$icon_menus['user'] ?? ''}}"></i>
              </a>
            </div>

            <div class="tab-pane fade m-1 p-1" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
              @isset($profile)
                @include('admin.profiles.show.profile')
                <a class="btn btn-info w-100" href="{{ route('profiles.show',$profile->id) }}" taske="button">
                  Actualizar
                  <i class="{{$icon_menus['profile'] ?? ''}}"></i>
                </a>
              @endisset         
            </div>

            <div class="tab-pane fade m-1 p-1" id="nav-messege" role="tabpanel" aria-labelledby="nav-messege-tab">
              @isset($messeges)
                @includeIf('admin.messeges.show.messeges')
              @endisset
            </div>

            <div class="tab-pane fade m-1 p-1" id="nav-tasks" role="tabpanel" aria-labelledby="nav-tasks-tab">       
              @isset($tasks)
                @includeIf('admin.tasks.show.tasks')
              @endisset
            </div>

            <div class="tab-pane fade m-1 p-1" id="nav-alerts" role="tabpanel" aria-labelledby="nav-alerts-tab">       
              @isset($alerts)
                @includeIf('admin.alerts.show.alerts')
              @endisset
            </div>

          </div>

        </div>

      </div>

    @endisset
    
  </div>
</div>