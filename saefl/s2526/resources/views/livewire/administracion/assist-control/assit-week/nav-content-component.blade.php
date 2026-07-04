<div>
    <div class="tab-content border border-top-0 table-light" id="nav-tabContent-assit_week">
        @foreach($assit_weeks as $assit_week)

            @if (empty($assit_week_id))
                @php $active = ($loop->iteration==1) ? 'active':null ; @endphp
            @else
                @php $active = ($assit_week->id == $assit_week_id) ? 'active':null ; @endphp
            @endif
            <div class="tab-pane fade show {{$active ?? null}} shadow" id="nav-content-assit_week-{{$assit_week->id}}" role="tabpanel" aria-labelledby="nav-header-assit_week-tab-{{$assit_week->id}}">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <livewire:administracion.assist-control.assit-week.index-component :id="$assit_week->id" :key="'assit-week-index-'.$assit_week->id" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
