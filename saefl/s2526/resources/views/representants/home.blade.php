@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-12 p-1">
                    @include('representants.elements.messeges.oper_ok')
                    @includeif('representants.home.partials.indicadores')
                </div>

            </div>
        </div>

    </main>

    @include('representants.home.modals.main')

@endsection



