<div class=" h-100 border float-right w-25">
    <div class=" font-weight-bold text-center">
        Reparaciones al campo status_pay conceptos cancelados
    </div>
    <div id="container"></div>
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            var count = {{ $cuentaxpagars->count() ?? 0 }}
            var start = 1;
            var size = count < 12 ? count : 12;
            fix_status_pay(start,size,count);
         });

        function fix_status_pay(start=1,size=12,count=12){
            var container = '#container'; //console.log(container);

            var ajaxurl = '{{ route("fix_status_pay", ["start"=>"_start_","size"=>"_size_"] )}}'; //console.log(ajaxurl);

            ajaxurl = ajaxurl.replace('_start_', start).replace('_size_', size); //console.log(ajaxurl);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){

                    $(container).append(data);

                    start = start + size ;
                    if ( start < count ) {
                        size = (start+size) > count ? (count - start) : 12;
                        setTimeout ( fix_status_pay(start, size, count), 60000 );
                    }

                }
            });
        }
    </script>

@endsection
