@extends('admin.layouts.dashboard.app')

@section('main')

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">

        <div class="card bd-callout bd-callout-primary mt-2 ">

            <div class="card-header">

                <h2>

                    Nuevo Mensaje

                        {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                    @include('admin.messeges.menus.create')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h2>

            </div>

            <div class="card-body">

            @include('admin.messeges.forms.create')

            </div>

        </div>

    </main>

@endsection

@section('scripts')
    @parent

    {{-- <script src="{{ asset("js/models/messeges/create.js") }}"></script> --}}

@endsection