@extends('bienestars.layouts.dashboard.app')

@section('main')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Mensajes Enviados</h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endif

                    @livewire('bienestar.resend.email-list')

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
