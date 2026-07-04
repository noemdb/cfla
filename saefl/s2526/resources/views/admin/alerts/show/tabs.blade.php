<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#nav-general" role="tab" aria-controls="nav-general" aria-selected="true">General</a>
    <a class="nav-item nav-link" id="nav-user-tab" data-toggle="tab" href="#nav-user" role="tab" aria-controls="nav-user" aria-selected="false">Usuario</a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Perfil</a>
    <a class="nav-item nav-link" id="nav-messege-tab" data-toggle="tab" href="#nav-messege" role="tab" aria-controls="nav-messege" aria-selected="false">Mensajes</a>
    <a class="nav-item nav-link" id="nav-task-tab" data-toggle="tab" href="#nav-task" role="tab" aria-controls="nav-task" aria-selected="false">Tareas</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
    @includeIf('admin.alerts.show.alert')
    <a class="btn btn-warning w-100" href="{{ route('alerts.edit',$alert->id) }}" taske="button">
      Actualizar
      <i class="{{$icon_menus['alert'] ?? ''}}"></i>
    </a>
  </div>

  <div class="tab-pane fade show m-1 p-1" id="nav-user" role="tabpanel" aria-labelledby="nav-guser-tab">
    @includeIf('admin.users.show.simple')
  </div>

  <div class="tab-pane fade m-1 p-1" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
    @if($profile)
      @includeIf('admin.profiles.show.withimg')
    @endif         
  </div>

  <div class="tab-pane fade m-1 p-1" id="nav-messege" role="tabpanel" aria-labelledby="nav-messege-tab">
    @if($messeges)
      @includeIf('admin.messeges.show.messeges')
    @endif
  </div>

  <div class="tab-pane fade m-1 p-1" id="nav-task" role="tabpanel" aria-labelledby="nav-task-tab">       
    @if($tasks)
      @includeIf('admin.tasks.show.simples')
    @endif
  </div>

</div>