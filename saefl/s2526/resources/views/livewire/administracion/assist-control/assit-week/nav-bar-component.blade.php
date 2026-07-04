<div>

    <nav class="table-light">
        <div class="nav nav-tabs nav-fill" id="nav-tab-nav-bar" role="tablist">
            @foreach($assit_weeks as $assit_week)

                @if (empty($assit_week_id))
                    @php $active = ($loop->iteration==1) ? 'active':null ; $selected = ($loop->iteration==1) ? 'true':'false' ; @endphp
                @else
                    @php $active = ($assit_week->id == $assit_week_id) ? 'active':null ; $selected = ($assit_week->id == $assit_week_id) ? 'true':'false' ; @endphp
                @endif

                <a class="nav-item nav-link {{$active ?? null}}" id="nav-header-tab-assit_week-{{$assit_week->id}}" data-toggle="tab" href="#nav-content-assit_week-{{$assit_week->id}}" role="tab" aria-controls="nav-assit_week" aria-selected="{{$selected ?? null}}">
                    <span class="badge badge-danger">{{$loop->iteration}}</span>
                    <div><b>{{$assit_week->name ?? ''}}</b></div>
                </a>
            @endforeach
        </div>
    </nav>

</div>
