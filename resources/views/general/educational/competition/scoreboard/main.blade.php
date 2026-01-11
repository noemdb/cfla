<div class="py-8">
    <section class="max-w-7xl mx-auto px-4">
        <div class="px-4 mx-auto text-center h-full">
            @if ($competition)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    <div
                        class="diagnostic-card border border-emerald-500/10 rounded-2xl p-6 shadow-2xl backdrop-blur-md">
                        <div
                            class="mb-4 text-emerald-400 text-xs font-bold uppercase tracking-widest border-b border-emerald-500/20 pb-2">
                            Información de la Competición</div>
                        <livewire:app.general.educational.competition.scoreboard.competition-component
                            :id="$competition->id" />
                    </div>
                    <div
                        class="diagnostic-card border border-emerald-500/10 rounded-2xl p-6 shadow-2xl backdrop-blur-md">
                        <div
                            class="mb-4 text-emerald-400 text-xs font-bold uppercase tracking-widest border-b border-emerald-500/20 pb-2">
                            Estado del Debate</div>
                        <livewire:app.general.educational.competition.scoreboard.debate-component :id="$competition->id" />
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center mt-10">
                    @include('general.educational.competition.scoreboard.default.notfound')
                </div>
            @endif
        </div>
    </section>
</div>
