<section class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply">

    <div class="px-4 mx-auto text-center h-full">

        @if ($competition)

            <div class="flex items-center justify-center">
                @include('general.educational.competition.board.partials.competition') 
            </div>

            @include('general.educational.competition.board.partials.debates2')
            
        @else

            <div class="flex items-center justify-center mt-10">
                @include('general.educational.competition.board.default.notfound')            
            </div>

        @endif        

    </div>

</section>