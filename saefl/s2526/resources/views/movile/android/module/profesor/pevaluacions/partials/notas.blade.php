<div wire:key="pevaluacion">
    <ul class="list-group px-1">
        {{-- <li class="list-group-item list-group-item-secondary">
            {{$lapso_active->name}}
        </li> --}}
        <li class="list-group-item px-1">
            <div class="p-1" id="nav-profesor-pevaluacion-{{$lapso_active->id}}">
                @php $lapso = $lapso_active; @endphp
                @php $pevaluacions = $profesor->pevaluacions->where('lapso_id',$lapso->id);@endphp
                @include('movile.android.module.profesor.pevaluacions.partials.main')
            </div>
        </li>
    </ul>
</div>








{{-- <div class="p-2" wire:key="pevaluacion">

    @php $items = $lapsos->where('id',$lapso_active->id) @endphp

    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
            @foreach($lapsos as $lapso)
                <button class="nav-link p-1 {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-profesor-pevaluacion-tab-{{$lapso->id}}" data-bs-toggle="tab" data-bs-target="#nav-profesor-pevaluacion-{{$lapso->id}}" type="button" role="tab" aria-controls="nav-profesor-pevaluacion-{{$lapso->id}}" aria-selected="true">{{$lapso->code_sm}}</button>
            @endforeach
        </div>
    </nav>
    <div class="tab-content border border-top-0" id="nav-tabContent">
        
        @foreach($lapsos as $lapso)
            <div class="tab-pane fade show {{($loop->iteration==$lapso_active->id) ? 'show active':''}} p-2" id="nav-profesor-pevaluacion-{{$lapso->id}}" role="tabpanel" aria-labelledby="nav-profesor-pevaluacion-tab" tabindex="{{$loop->index}}">
                @php $pevaluacions = $profesor->pevaluacions->where('lapso_id',$lapso->id); @endphp
                @include('movile.android.module.profesor.pevaluacions.partials.main')
            </div>
        @endforeach
    </div>    

</div> --}}
