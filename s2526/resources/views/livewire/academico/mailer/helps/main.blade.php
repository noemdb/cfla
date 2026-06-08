<nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-create-tab" data-toggle="tab" href="#nav-create" role="tab" aria-controls="nav-create" aria-selected="true">Registrar</a>
            <a class="nav-item nav-link" id="nav-update-tab" data-toggle="tab" href="#nav-update" role="tab" aria-controls="nav-update" aria-selected="false">Actualizar</a>
            <a class="nav-item nav-link" id="nav-delete-tab" data-toggle="tab" href="#nav-delete" role="tab" aria-controls="nav-delete" aria-selected="false">Eliminar</a>
            <a class="nav-item nav-link" id="nav-preview-tab" data-toggle="tab" href="#nav-preview" role="tab" aria-controls="nav-preview" aria-selected="false">Vista previa</a>
            <a class="nav-item nav-link" id="nav-show-tab" data-toggle="tab" href="#nav-show" role="tab" aria-controls="nav-show" aria-selected="false">Destinatarios</a>
            <a class="nav-item nav-link" id="nav-queue-tab" data-toggle="tab" href="#nav-queue" role="tab" aria-controls="nav-queue" aria-selected="false">Crear cola</a>
        </div>
</nav>

<div class="tab-content p-2 border border-top-0 rounded" id="nav-tabContent">

    <div class="tab-pane fade show active" id="nav-create" role="tabpanel" aria-labelledby="nav-create-tab">
        <h4 class="alert alert-secondary">            
            Registrar nuevo mensaje.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/create.png') }}">
    </div>  
    <div class="tab-pane fade show" id="nav-update" role="tabpanel" aria-labelledby="nav-update-tab">
        <h4 class="alert alert-secondary">            
            Actualizar mensaje.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/update.png') }}">
    </div>     

    <div class="tab-pane fade show" id="nav-delete" role="tabpanel" aria-labelledby="nav-delete-tab">
        <h4 class="alert alert-secondary">            
            Eliminar mensaje.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/delete.png') }}">
    </div>  
        
    <div class="tab-pane fade show" id="nav-preview" role="tabpanel" aria-labelledby="nav-preview-tab">
        <h4 class="alert alert-secondary">            
            Vista previa del mensaje.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/preview.png') }}">
    </div>  

    <div class="tab-pane fade show" id="nav-show" role="tabpanel" aria-labelledby="nav-show-tab">
        <h4 class="alert alert-secondary">            
            Vista previa del mensaje.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/show.png') }}">
    </div> 

    <div class="tab-pane fade queue" id="nav-queue" role="tabpanel" aria-labelledby="nav-show-tab">
        <h4 class="alert alert-secondary">            
            Crear la cola de correos automatizados.
        </h4>
        <img class="card-img-top px-2 border rounded" src="{{ asset('images/help/mailer/queue.png') }}">
    </div>  
        
</div>  