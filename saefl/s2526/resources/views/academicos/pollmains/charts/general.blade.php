@php ($chart = ['range'=>'Todos','id_chart'=>'inscri_aca_toxgenero_id'.Auth::user()->id,'urlapi'=>route('academicos.polls.general.chart'),'tipo'=>'pie','limit'=>$poll_main->id ])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('administracion.elements.canvas.chart')
    @slot('class', 'borderRBL')
    @slot('nav')
    <nav class="" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
        <a data-range="Todos" class="nav-item nav-link active" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false"></a>
    </nav>
    @endslot
    @slot('id', $chart['id_chart'])
@endcomponent
