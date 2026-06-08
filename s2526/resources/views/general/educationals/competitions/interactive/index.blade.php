@extends('general.layaout.main')

@section('title')
    U.E. Colegio Fray Luis Amigó
@endsection

@section('main')
    <main role="main">

        <div class="d-flex flex-column align-items-center bg-body-tertiary shadow-sm py-4">
            <div class="flex-grow-1 text-center">
                <h1 class="display-6 fw-bold mb-0" id="competicion-title">Competiciones Académicas</h1>
                <p class="text-muted small">U.E. Colegio Fray Luis Amigó</p>
            </div>
        </div>

        <livewire:general.educational.competition.interactive.index-component :token="$competition->token" />

    </main>
@endsection

@section('sweetalert')
    @parent
    <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script>
@endsection
