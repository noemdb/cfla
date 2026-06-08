<div>
    
    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-8 col-md-6">

                @switch($mode)
                    @case('question') @include('livewire.general.educational.competition.debate.question') @break
                    @case('option') @include('livewire.general.educational.competition.debate.option') @break                       
                    @case('editQuestion') @include('livewire.general.educational.competition.debate.question') @break                       
                    @case('editOption') @include('livewire.general.educational.competition.debate.option') @break                       
                @endswitch               

            </div>

            <div class="col-sm-4 col-md-6">

                @include('livewire.general.educational.competition.debate.partials.questions')

            </div>
            
        </div>

    </div>

    <div wire:loading> 
        <div class="position-relative">
            <div class="position-absolute bottom-0 end-0">
                <div class="clearfix">
                    <div class="spinner-border float-end" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>    
        </div>
    </div>

</div>
