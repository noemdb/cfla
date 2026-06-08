@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready( function(){
            Swal.fire({
                icon: 'success',
                title: 'Registro de Vueltos/Devoluciones.',
                html: "{{Session::get('messenge_refund')}}",
            })
        });
    </script>
@endsection
