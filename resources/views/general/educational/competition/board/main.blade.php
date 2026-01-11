<section class="py-8">
    <div class="px-4 mx-auto text-center h-full">
        @if ($competition)
            <div class="flex items-center justify-center mb-10">
                @include('general.educational.competition.board.partials.competition')
            </div>

            <div class="diagnostic-card border border-emerald-500/10 rounded-2xl p-6 shadow-2xl backdrop-blur-md">
                @include('general.educational.competition.board.partials.debates2')
            </div>
        @else
            <div class="flex items-center justify-center mt-10">
                @include('general.educational.competition.board.default.notfound')
            </div>
        @endif
    </div>
</section>
