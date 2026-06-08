{{--

'estudiant_id','coexistence','status_transport_private_vehicle','status_transport_public_vehicle','status_transport_walking','status_transport_other','vaccination_schedule','sports_potential','place_where_he_practices',

'status_illness_cardiovascular','status_illness_cancer','status_illness_lupus','status_illness_diabetes','status_illness_renal_problems','status_illness_overweight','status_illness_other','illness_other',
'status_conditions_intellectual_disability','status_conditions_motor_disability','status_conditions_visual_disability','status_conditions_hearing_impairment','status_conditions_outstanding_attitudes',
'status_conditions_autism','status_conditions_other','conditions_other','status_treated_by_specialist','specialist','status_take_medication','medication',

'mother_name','mother_lastname','mother_profession','mother_phones','mother_address','father_name','father_lastname','father_profession','father_phones','father_address',

--}}

@php
    $errorHome = ($errors->has('name') || $errors->has('code')  || $errors->has('fecha') || $errors->has('ffinal') || $errors->has('description') || $errors->has('pestudio_id') || $errors->has('grado_id') || $errors->has('seccion_id')  || $errors->has('status_adviders') || $errors->has('status')) ? true : false ;
    $errormessege = ($errors->has('subject') || $errors->has('title') || $errors->has('subtitle') || $errors->has('greeting') ) ? true : false ;
    $errorBody = ($errors->has('body') || $errors->has('insert') || $errors->has('footer') ) ? true : false ;
@endphp
<div class="container-fluid">

    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active {{ ($errorHome) ? 'text-danger' : null }}" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Detalles Generales</a>
            <a class="nav-item nav-link {{ ($errormessege) ? 'text-danger' : null }}" id="nav-messege-tab" data-toggle="tab" href="#nav-messege" role="tab" aria-controls="nav-messege" aria-selected="false">Enfermedades y condiciones especiales</a>
            <a class="nav-item nav-link {{ ($errorBody) ? 'text-danger' : null }}" id="nav-body-tab" data-toggle="tab" href="#nav-body" role="tab" aria-controls="nav-body" aria-selected="false">Padre y madre</a>
        </div>
    </nav>

    <div class="tab-content p-2 border border-top-0 rounded" id="nav-tabContent">

        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            {{-- @include('livewire.administracion.bienestar.form.partials.general') /home/nuser/code/s2223/resources/views/livewire/bienestar/student-record/form/partials/general.blade.php --}}
            @include('livewire.bienestar.student-record.form.partials.general')
        </div>

        <div class="tab-pane fade" id="nav-messege" role="tabpanel" aria-labelledby="nav-messege-tab">
            {{-- @include('livewire.administracion.bienestar.form.partials.illness') --}}
            @include('livewire.bienestar.student-record.form.partials.illness')
        </div>

        <div class="tab-pane fade" id="nav-body" role="tabpanel" aria-labelledby="nav-body-tab">
            @include('livewire.bienestar.student-record.form.partials.parents')
            {{-- @include('livewire.administracion.bienestar.form.partials.parents') --}}
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
