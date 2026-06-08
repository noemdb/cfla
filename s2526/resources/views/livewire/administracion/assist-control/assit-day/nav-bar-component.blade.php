<div>

    <nav class="table-light">
        <div class="nav nav-tabs nav-fill" id="nav-tab-assit_day-nav-bar" role="tablist">
            @foreach($assit_days as $assit_day)

                @if (empty($assit_day_id))
                    @php $active = ($loop->iteration==1) ? 'active':null ; $selected = ($loop->iteration==1) ? 'true':'false' ; @endphp
                @else
                    @php $active = ($assit_day->id == $assit_day_id) ? 'active':null ; $selected = ($assit_day->id == $assit_day_id) ? 'true':'false' ; @endphp
                @endif

                <a class="nav-item nav-link {{$active ?? null}}" id="nav-header-tab-assit_day-{{$assit_day->id}}" data-toggle="tab" href="#nav-content-assit_day-{{$assit_day->id}}" role="tab" aria-controls="nav-assit_day" aria-selected="{{$selected ?? null}}">
                    <span class="badge badge-info">{{$loop->iteration}}</span>
                    <div><b>{{$assit_day->name ?? ''}}</b></div>
                </a>
            @endforeach
            
            <a class="nav-item nav-link" id="nav-header-tab-assit_day-new" data-toggle="tab" href="#nav-content-assit_day-new" role="tab" aria-controls="nav-assit_day-new" aria-selected="false">
                <span class="badge badge-primary"><i class="fa fa-plus" aria-hidden="true"></i></span>
                <div><b>Nuevo</b></div>
            </a>
        </div>
    </nav>

</div>
