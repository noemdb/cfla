{{-- 'user_id','name','code','description','pestudio','seccion_id','grado_id','fecha', 'subject','title','subtitle','greeting','body','footer',  'status','status_adviders', --}}

@php
    $errorHome = ($errors->has('name') || $errors->has('code')  || $errors->has('fecha') || $errors->has('ffinal') || $errors->has('description') || $errors->has('pestudio_id') || $errors->has('grado_id') || $errors->has('seccion_id')  || $errors->has('status_adviders') || $errors->has('status')) ? true : false ;
    // $errorGrupo = ($errors->has('pestudio_id') || $errors->has('grado_id') || $errors->has('seccion_id')  || $errors->has('status_adviders') || $errors->has('status')) ? true : false ;
    $errormessege = ($errors->has('subject') || $errors->has('title') || $errors->has('subtitle') || $errors->has('greeting') ) ? true : false ;
    $errorBody = ($errors->has('body') || $errors->has('insert') || $errors->has('footer') ) ? true : false ;
@endphp
<div class="container-fluid">

    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active {{ ($errorHome) ? 'text-danger' : null }}" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Detalles Generales</a>
            <a class="nav-item nav-link {{ ($errormessege) ? 'text-danger' : null }}" id="nav-messege-tab" data-toggle="tab" href="#nav-messege" role="tab" aria-controls="nav-messege" aria-selected="false">Mensaje</a>
            <a class="nav-item nav-link {{ ($errorBody) ? 'text-danger' : null }}" id="nav-body-tab" data-toggle="tab" href="#nav-body" role="tab" aria-controls="nav-body" aria-selected="false">Cuerpo y despedida</a>
        </div>
    </nav>

    <div class="tab-content p-2 border border-top-0 rounded" id="nav-tabContent">

        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            @include('livewire.administracion.mailer.form.partials.general')
        </div>


        <div class="tab-pane fade" id="nav-messege" role="tabpanel" aria-labelledby="nav-messege-tab">

            @include('livewire.administracion.mailer.form.partials.messege')

        </div>

        <div class="tab-pane fade" id="nav-body" role="tabpanel" aria-labelledby="nav-body-tab">

            @include('livewire.administracion.mailer.form.partials.body')

        </div>

    </div>

</div>


{{--

<div class="row">

    <div class="col">
    </div>

    <div class="col">
    </div>

</div>


--}}
