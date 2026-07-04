<div>
    <div class="tab-content border border-top-0 table-light" id="nav-tabContent-assit_day">
        @foreach($assit_days as $assit_day)

            @if (empty($assit_day_id))
                @php $active = ($loop->iteration==1) ? 'active':null ; @endphp
            @else
                @php $active = ($assit_day->id == $assit_day_id) ? 'active':null ; @endphp
            @endif
            <div class="tab-pane fade show {{$active ?? null}} shadow" id="nav-content-assit_day-{{$assit_day->id}}" role="tabpanel" aria-labelledby="nav-header-assit_day-tab-{{$assit_day->id}}">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            {{-- {{$assit_day}} --}}
                            <livewire:administracion.assist-control.assit-day.index-component :id="$assit_day->id" :key="'assit-day-index-'.$assit_day->id" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
