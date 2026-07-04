@extends('movile.android.layouts.app')

@section('content')

    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['catchments'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Censo Escolar</div>
            </div>
        </div>

        <div class="fw-bold">
            Registra tu interes por pertenecer a nuestra institución educativa.
        </div>

        <div class="text-start">
            
            <livewire:general.catchment.index-component :status_start="true" :status_intern="false"/>
            
        </div>        

    </div>

@endsection
