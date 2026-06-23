@extends('bienestars.layouts.dashboard.app')

@section('title') Planificación, Complementaria Integral @endsection

@section('main')

    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <livewire:bienestar.activity.index-component />                    
                </div>
            </div>
        </div>
    </main>
    
@endsection


@section('customeScripts')
@parent
<script>
    // Mostrar y ocultar modal desde Livewire
    window.addEventListener('show-activity-modal', () => {
        $('#activityModal').modal('show');
    });

    window.addEventListener('hide-activity-modal', () => {
        $('#activityModal').modal('hide');
    });

    // Notificación tipo toast o alert
    window.addEventListener('show-success-toast', event => {
        let message = event.detail.message;
        $('body').append(`
            <div class="alert alert-success alert-dismissible fade show fixed-top mx-auto mt-2" style="max-width: 400px; z-index: 2000;" role="alert">
                <strong><i class="fa fa-check-circle"></i></strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        setTimeout(() => { $('.alert').alert('close'); }, 2500);
    });
</script>
@endsection
