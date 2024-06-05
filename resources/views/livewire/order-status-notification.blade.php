<div>
    @script
        <script>
            Echo.channel(`orders`)
            .listen('OrderShipped', (e) => {
                console.log(e.order.name);
            });
        </script>
    @endscript
</div>
