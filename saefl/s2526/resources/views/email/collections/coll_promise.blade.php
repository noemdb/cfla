<div style="margin: 0.5rem; padding: 0.5rem; border: 1px #000 solid">

    <h2 style="text-align: center">ACTA COMPROMISO</h2>

    <hr>

    <table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0">
        <tbody>
            <tr>
                <td style="width: 100%;">

                    <div style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$coll_promise->created_at->format('d-m-Y')}}</div>

                    <div style="text-align: right;">

                        @if ($representant->exchange_ammount_expire_bill > 0)
                            <div> Deuda: $ {{f_float($representant->exchange_ammount_expire_bill)}} </div>
                        @else
                            <div><strong>SOLVENTE</strong></div>
                        @endif

                    </div>

                    <div style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name}}</strong> C.I. {{$representant->ci_representant}}</em>
                    </div>

                    @php $address = $representant->address;  @endphp
                    @if ($address)
                        <div style="text-align: justify;">
                            Domiciliado en: <em><strong>{{$address}}</strong></em>
                        </div>
                    @endif

                    <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                        <span style="margin-bottom: 0rem">Representante de:</span>
                        <div style="padding-left: 1rem; margin-top: 0rem">
                            @php $estudiants = $representant->estudiants; @endphp
                            @foreach ($estudiants as $estudiant)
                            <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                            @endforeach
                        </div>
                    </div>

                    <div style="text-align: justify;">
                        El día de hoy {{$coll_promise->created_at->format('d-m-Y')}} a las {{$coll_promise->created_at->format('h:i')}} se completó el registro de su <b>Acta Compromiso</b>,
                        motivado al incumplimiento con los pagos de las mensualidades correspondientes a la educación de
                        su{{($estudiants->isEmpty()) ? null:'s' }} representado{{($estudiants->isEmpty()) ? null:'s' }} según lo establecido en el artículo 55 ejusdem,
                        ya que los padres, madres y representantes tiene la obligación inmediata de garantizar dicho derecho.
                    </div>

                    <div style="text-align: justify;">
                        Ha quedado registrada la siguiente información:
                    </div>
                    <ul>
                        <li>-. La razón del incumplimiento: {{$coll_promise->description}}</li>
                        <li>-. Fecha de la cancelación total: {{f_date($coll_promise->date)}}</li>
                        <li>-. Monto: {{f_float($coll_promise->exchange_ammount)}} *</li>
                    </ul>

                    <div style="text-align: justify;">
                        En caso de incumplir con el compromiso adquirido, el caso seguirá su curso a ser remitido al CMDNNA San Felipe - Yaracuy
                    </div>

                    <div>&nbsp;</div>

                    <div style="text-align: center;">
                        __________________________________________<br>
                        {{$representant->name}} <br>
                        C.I. {{$representant->ci_representant}}
                    </div>


                    <small style="float: right">* Montos expresados en divisas</small>

                </td>

            </tr>
        </tbody>
    </table>

</div>
