<div>

    @include('livewire.bot.partials.main')

    @section('livewires')
        @parent
        <script>
            Livewire.on('messegeAdded', () => {
                const element = document.getElementById("contentBot{{$area ?? null}}");
                element.scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
            })
        </script>
    @endsection

</div>

