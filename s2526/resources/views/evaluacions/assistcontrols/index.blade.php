@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-1 p-1">

            <div class="alert alert-secondary mb-1 pb-1">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('evaluacions.pases.menus.index') --}}
                </div>
                <h3>
                    Asistencia de los docentes                  
                </h3>
                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2">{{$pestudio->name ?? null}}</span> ||
                    @endforeach
                </div>
            </div>

            <div class="card-body m-1 p-1">

                @include('evaluacions.assistcontrols.form.format')
                @include('evaluacions.assistcontrols.table.format')

            </div>

        </div>

    </main>

@endsection