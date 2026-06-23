@if (!empty($ci_not_founds->count()) || !empty($nota_out_ranges->count()))
    <div class="small">

        <h6 class=" text-muted text-right">INCONCISTENCIAS</h6>

        @if (!empty($ci_not_founds->count()))

            <ul class="list-group">
                <li class="list-group-item list-group-item-danger">EMAIL NO ENCONTRADAS</li>
                @foreach ($ci_not_founds as $item)
                    <li class="list-group-item">
                        <span class=" font-weight-bold">{{ $item['gsemail'] }}</span>
                        {{ $item['fullname'] }}
                    </li>
                @endforeach
            </ul>
        @endif

        @if (!empty($nota_out_ranges->count()))

        <hr>

            <ul class="list-group">
                <li class="list-group-item list-group-item-warning">
                    NOTA ENCONTRADA FUERA DE LA ESCALA DE EVALUACION
                    [Mínimo: {{ $minimo ?? '' }} || Máximo: {{ $maximo ?? '' }}]
                </li>
                @foreach ($nota_out_ranges as $item)
                    <li class="list-group-item">
                        <span class=" font-weight-bold">
                            {{ $item['gsemail'] ?? '' }}
                        </span>
                        {{ $item['fullname'] ?? '' }}
                        <span class=" text-muted">
                            Nota XSL: {{ $item['nota'] ?? ''}}
                        </span>
                    </li>
                @endforeach
            </ul>

        @endif
    </div>
@endif
