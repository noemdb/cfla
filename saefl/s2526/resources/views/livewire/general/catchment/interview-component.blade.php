<div> 

    @if ($this->modeIndex) @include('livewire.general.catchment.interview.index') @endif
    @if ($this->modeCreate) @include('livewire.general.catchment.interview.create') @endif

    @if ($status_save)
        <hr>
        @php $url = env('APP_URL')."/general/catchments/paper/id/".$interview_id; @endphp
        <div class="text-end">
            <a name="" id="" class="btn btn-link btn-sm" href="{{ $url ?? null}}" role="button" target="_BLANK">Ver planilla</a>        
        </div>
    @endif

</div>
