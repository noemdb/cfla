@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="row">

            <div class="col-sm-12">
                @includeif('academicos.home.main')
            </div>

        </div>

    </main>

    @include('academicos.home.modals.main')

@endsection


