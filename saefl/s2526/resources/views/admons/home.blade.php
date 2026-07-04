@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        {{-- <div class="container pt-1 w-100"> --}}
            <div class="row">

                <div class="col-sm-12">
                    @include('directors.elements.messeges.oper_ok')
                    @includeif('directors.home.partials.indicadores')
                </div>

            </div>
        {{-- </div> --}}

    </main>

    @include('directors.home.modals.main')

@endsection
