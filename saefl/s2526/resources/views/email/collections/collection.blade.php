<table style="border-collapse: collapse; width: 100%;" border="1">
    <tbody>
    <tr>
    <td style="width: 100%;">
        <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate}}</p>
        {{-- <p style="text-align: right;">Factura : ES383838383</p> --}}
        <p style="text-align: right;">Deuda: $ {{f_float($representant->exchange_ammount_expire_bill)}}</p>
        <p style="text-align: right;">Vencimiento: {{$lastDate}}</p>
        <p style="text-align: justify;">Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name}}</strong></em></p>
        <p style="text-align: justify;">
            Mediante la presente carta queremos hacerle llegar el aviso de cobro de nuestros servicios
            que usted contrató con nosotros y que aun estamos a la espera de recibir su pago.
            Le agradecemos realice el ingreso en nuestra oficina o se ponga lo antes posible en contacto con nuestro departamento de
            gestión económica.
        </p>
        <p style="text-align: justify;">Un saludo afectuoso.</p>
        <p style="text-align: justify;">Atentamente,</p>
        <p style="text-align: center;">{{$autoridad2->profile_professional}} {{$autoridad2->name}}</p>
        <p style="text-align: center;">{{$autoridad2->position}}</p>
        <p style="text-align: center;"><strong>(Firma)</strong></p>
    </td>
    </tr>
    </tbody>
</table>
