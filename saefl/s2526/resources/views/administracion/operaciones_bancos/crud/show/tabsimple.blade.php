<div class="card m-1 p-1">
  <div class="card-body p-1 m-1">

    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-guser-tab" data-toggle="tab" href="#nav-guser" role="tab" aria-controls="nav-guser" aria-selected="true">General</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Perfil</a>
        <a class="nav-item nav-link" id="nav-messege-tab" data-toggle="tab" href="#nav-messege" role="tab" aria-controls="nav-messege" aria-selected="false">Mensajes</a>
        <a class="nav-item nav-link" id="nav-alert-tab" data-toggle="tab" href="#nav-alert" role="tab" aria-controls="nav-alert" aria-selected="false">Alertas</a>
      </div>
    </nav>

    <div class="tab-content" id="nav-tabContent">

      <div class="tab-pane fade show active m-1 p-1" id="nav-guser" role="tabpanel" aria-labelledby="nav-guser-tab">
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

      <div class="tab-pane fade m-1 p-1" id="nav-alert" role="tabpanel" aria-labelledby="nav-alert-tab">       
        @if($alerts)
          @includeIf('admin.alerts.show.simples')
        @endif
      </div>

    </div>

  </div>

</div>