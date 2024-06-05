<div>
    <div class="text-start">

        <div class="mb-2 border-b-2 border-gray-600">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white ">
                {{($debate) ? $debate->name : null }}        
            </h3>
            <small class="block text-sm ">{{$debate->description}}</small>
        </div>

        @include('livewire.app.general.educational.competition.dashboard.partials.questions')
        
    </div>
</div>