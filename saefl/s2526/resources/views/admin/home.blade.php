@extends('admin.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
        <h4 class="page-header">
            Panel de Administración
        </h4>

        {{-- labels --}}
        @includeIf('admin.home.partials.labels')

        {{-- @includeIf('admin.home.partials.admin.labels') --}}

        {{-- listas --}}
        {{-- @includeIf('admin.home.partials.list') --}}

        {{-- graficas system --}}
        @includeIf('admin.home.partials.graphics')

        {{-- graficas common--}}
        {{-- @includeIf('admin.home.partials.graphics')--}}

    </main>

@endsection
{{-- FIN section main--}}

@section('stylesheet')
    @parent

    {{-- <link rel="stylesheet" href="{{ asset('css/timeline.css') }}"> --}}

@endsection


@section('scripts')
    @parent
    {{-- 
    <script type="text/javascript">
        swal({
          type: 'success',
          title: 'Excelente! Bienvenido',
          html: '<i class="{{ $icon_menus['sistema'] }} text-warning"></i> Administración del Sistema',
        })
    </script>
    --}}

@endsection
