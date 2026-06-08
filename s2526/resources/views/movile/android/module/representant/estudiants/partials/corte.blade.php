<div class="table-responsive table-sm small">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col">Momento</th>
                <th scope="col">Corte</th>
            </tr>
        </thead>
        <tbody>
            @php $date = Carbon\Carbon::now()->format('Y-m-d'); @endphp
            {{-- @php $enabled_bill = ($exchange_ammount_expire_bill<=0) ? true : false @endphp --}}
            @foreach ($lapsos as $lapso)
            @php $enabled_date = ($date >= $lapso->ffinal) ? true : false; @endphp
            <tr class="">
                <td scope="row small">
                    <div class="">{{$lapso->code_sm}}</div>
                    <span class="small">{{f_date($lapso->date_cutnote)}}</span>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center h-100">
                        @php $enabled_date = ($date >= $lapso->ffinal) ? true : false; @endphp
                        <a target="_blank" class="btn btn-{{ ( $lapso->getStatusEnableCorte($estudiant->id)) ? $lapso->class : 'secondary disabled' }} btn-sm" title="{{$lapso->name_public ?? ''}}"
                            href="{{ $route=route('representants.boletins.corte.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                            {!! $lapso->id ?? '' !!}
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{--
<div class="d-flex">
    <div class="p-2 flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="text-muted small mx-1 text-center ">Momentos</div>
    </div>
    <div class="p-2">
        <div class="d-flex justify-content-center">
            <div class="btn-group w-100 mx-1" role="group" aria-label="Basic example">
                @php $date = Carbon\Carbon::now()->format('Y-m-d'); @endphp
                @php $enabled_bill = ($exchange_ammount_expire_bill<=0) ? true : false @endphp
                @foreach ($lapsos as $lapso)
                    @php $enabled_date = ($date >= $lapso->ffinal) ? true : false; @endphp
                    <a target="_blank" class="btn btn-{{ ( $lapso->getStatusEnableCorte($estudiant->id)) ? $lapso->class : 'secondary disabled' }}" title="{{$lapso->name_public ?? ''}}"
                        href="{{ $route=route('representants.boletins.corte.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                        {!! $lapso->id ?? '' !!}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

--}}
