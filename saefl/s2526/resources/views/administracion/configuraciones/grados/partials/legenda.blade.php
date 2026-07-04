<ul class="list-group">

    <li class="list-group-item list-group-item-secondary px-2 py-1 small font-weight-bold text-center">Legenda de colores<br>para los Grados</li>

    @foreach ($grados as $grado)

        <li class="list-group-item font-weight-bolder small text-uppercase py-2">
            <div class="{{ $grado->class_text_color ?? '' }}">
                &#9751; {{ $grado->name ?? ''}}
            </div>
        </li>

    @endforeach

</ul>

