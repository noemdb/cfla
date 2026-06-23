@extends('bienestars.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    @includeif('bienestars.home.partials.indicadores')
                </div>
            </div>
        </div>
    </main>

@endsection


