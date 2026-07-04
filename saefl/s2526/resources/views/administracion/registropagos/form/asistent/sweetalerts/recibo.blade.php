@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready( function(){
            Swal.fire({
                icon: 'question',
                title: 'Imprimir recibo',
                html: '<a href="{{ route('administracion.representants.recibo.pdf',$pago_combinado_id) }}" target="_blank">Imprimir recibo</a>',
            })
        });
    </script>
@endsection

{{-- html: 'Click en el botón <b>Ok</b> para continuar con otro registro de pago de éste representante. <hr> <p>Sí es necesario, realice el ajuste con los datos precargados</p>'+{{ (Session::has('messenge_refund')) ? Session::get('messenge_refund') : null}}, --}}