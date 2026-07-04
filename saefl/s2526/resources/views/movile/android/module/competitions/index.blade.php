@extends('movile.android.layouts.app')

@section('content')

        <div class="content py-2 px-1">

            <div class=" d-flex d-flex justify-content-center px-2 py-2">
                <div>
                    <i class="{{ $icon_menus['competitions'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                    <div class="small text-muted fw-bold">Competiciones</div>
                </div>
            </div>

            <livewire:movile.competition.index-component />            

        </div>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
