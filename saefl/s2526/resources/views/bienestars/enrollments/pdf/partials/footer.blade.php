<p></p>
<table width="100%" class="table table-striped table-hover table-sm small p-1" style="font-size:0.6rem;margin-bottom:0.1rem; padding-bottom:0.1rem;">
<tr>
    <td align="center">
        {{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}<br>
        <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
    </td>
    <td align="center">
        {{ $autoridad2->name ?? ''}} {{ $autoridad2->lastname ?? '' }}<br>
        <span class="text-muted">{{$autoridad2->position ?? ''}}</span>
    </td>
    <td class="text-muted" align="right">Sello de la Institución</td>
</tr>
</table>
{{-- <br> --}}

{{-- <p>&nbsp;</p> --}}
<footer class="text-muted" style="font-size:7px;">
    Registrado por: {{ ($student_record->user) ? $student_record->user->profile->full_name:null}} || Impreso por: {{ Auth::user()->profile->full_name ?? ''}}
    <hr>
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>
