<div class="text-right font-weight-bold card bd-callout bd-callout-dark">
    <p class="text-dark">Lista de preinscripciones registradas</p>
    <small class="px-1">
        @foreach ($preinscripcions as $preinscripcion)
            @php
                $estudiant = $preinscripcion->estudiant;
                $grado = $preinscripcion->grado;
            @endphp
            <span class=" font-weight-bolder">{{$loop->iteration}}</span>
            <dl class="mb-1 ">
                <dt>Estudiante: <span class="text-secondary">{{ $estudiant->fullname ?? '' }}</span></dt>
            </dl>
            <dl class="mb-1 ">
                <dt>Grado: <span class="text-secondary">{{ $grado->name ?? '' }}</span></dt>
            </dl>
            <dl class="mb-1 ">
                <dt>Estado:
                    <span class=" font-weight-bold text-primary">
                        REGISTRADA @include('representants.preinscripcions.show.info_estado')
                    </span>
                    @php
                        $count_requerimiento = 1;
                        $total_requerimiento = 10;
                    @endphp
                    @component('administracion.elements.progress.bars_xs')
                        {{-- @slot('title', 'Estado') --}}
                        @slot('actual_ammount',$count_requerimiento)
                        @slot('goal_ammount',$total_requerimiento)
                    @endcomponent

                </dt>
            </dl>
            <hr>
        @endforeach
    </small>
</div>
