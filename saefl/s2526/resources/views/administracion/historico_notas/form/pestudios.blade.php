<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-datos_pestudios-tab" data-toggle="tab" href="#nav-datos_pestudios" role="tab" aria-controls="nav-datos_pestudios" aria-selected="true">
            <strong>V. Plan de Estudio</strong>
        </a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active px-2" id="nav-datos_pestudios" role="tabpanel" aria-labelledby="nav-datos_pestudios-tab">

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-regulares-tab" data-toggle="tab" href="#nav-regulares" role="tab" aria-controls="nav-regulares" aria-selected="true">
                    <strong>Regulares</strong>
                </a>
                <a class="nav-item nav-link" id="nav-especiales-tab" data-toggle="tab" href="#nav-especiales" role="tab" aria-controls="nav-especiales" aria-selected="true">
                    <strong>Especiales</strong>
                </a>
            </div>
        </nav>

        <div class="tab-content pt-3 px-3" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-regulares" role="tabpanel" aria-labelledby="nav-regulares-tab">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach ($pestudio->grados as $grado)
                            @php $nav_id = 'nav_tab_regulares_'.$grado->id; @endphp
                            @php $content_id = 'content_regular_grado_'.$grado->id; @endphp
                            <a class="nav-item nav-link {{ ($loop->iteration == 1) ? 'active' : ''}}" id="{{$nav_id}}" data-toggle="tab" href="#{{$content_id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                                {{$grado->name}}
                            </a>
                        @endforeach
                    </div>
                </nav>
                <div class="tab-content border border-top-0" id="nav-tabContent">
                    @foreach ($pestudio->grados as $grado)
                        @php $nav_id = 'nav_tab_regulares_'.$grado->id; @endphp
                        @php $content_id = 'content_regular_grado_'.$grado->id; @endphp
                        <div class="tab-pane fade {{ ($loop->iteration == 1) ? ' show active ':''}}  px-4 py-2" id="{{$content_id}}" role="tabpanel" aria-labelledby="{{$nav_id}}">
                            @include('administracion.historico_notas.form.fields.hnotas.regulares')
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tab-pane fade" id="nav-especiales" role="tabpanel" aria-labelledby="nav-especiales-tab">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach ($pestudio->grados as $grado)
                            @php $nav_id = 'nav_tab_especiales_'.$grado->id; @endphp
                            @php $content_id = 'content_especiales_grado_'.$grado->id; @endphp
                            <a class="nav-item nav-link {{ ($loop->iteration == 1) ? 'active' : ''}}" id="{{$nav_id}}" data-toggle="tab" href="#{{$content_id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                                {{$grado->name}}
                            </a>
                        @endforeach
                    </div>
                </nav>
                <div class="tab-content border border-top-0" id="nav-tabContent">
                    @foreach ($pestudio->grados as $grado)
                        @php $nav_id = 'nav_tab_especiales_'.$grado->id; @endphp
                        @php $content_id = 'content_especiales_grado_'.$grado->id; @endphp
                        <div class="tab-pane fade {{ ($loop->iteration == 1) ? ' show active ':''}}  px-4 py-2" id="{{$content_id}}" role="tabpanel" aria-labelledby="{{$nav_id}}">
                            @include('administracion.historico_notas.form.fields.hnotas.especiales')
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
