@if (!empty($estudiant))    
    <table width="100%" style="margin-top: 4px;">
        <tr>
            <td align="left">
                <smal style="font-size:6px;">
                    @php
                        $pescolar =  App\Models\app\Pescolar::first();
                        $ffinal = ($pescolar) ? $pescolar->ffinal : null;
                    @endphp
                    Descargue el Informe de Notas. <strong>Válido hasta el {{ f_date($ffinal) }}</strong>
                </smal>
            </td>
            <td align="left">
                {!! DNS2D::getBarcodeHTML($estudiant->boletinPdfUrl(), 'QRCODE', 1.5, 1.5) !!}                
            </td>
        </tr>        
    </table>
    
@endif