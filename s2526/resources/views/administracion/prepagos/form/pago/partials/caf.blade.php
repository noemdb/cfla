@php $cafs = $representant->creditos_disponibles @endphp

@if (!empty($cafs->count()))

    <dl class="pl-2">

        {{-- <dt class="font-weight-bolder py-1">CAF</dt> --}}


        @foreach ($cafs as $caf)

            @php $ammont = $caf->credito_ammount; @endphp

            @if ($ammont > 0)

                <dd class="pl-1 pb-0">

                    <div class="input-group py-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php $name = 'cafs['.$estudiant->id.']['.$caf->id.']'; @endphp
                                {{ Form::checkbox($name, $ammont, true,['class'=>'text-danger']) }}

                            </div>
                        </div>
                        <div class="form-control">
                            <span class="small"> GEN: {{ f_date($caf->created_at) }} </span>
                            <span class="small badge badge-light float-right pt-1"> Bs. {{ f_float($ammont) ?? ''}} </span>
                        </div>
                    </div>

                </dd>

            @endif

        @endforeach

    </dl>

@else

    <span class="small text-muted"> No hay disponibles </span>

@endif
