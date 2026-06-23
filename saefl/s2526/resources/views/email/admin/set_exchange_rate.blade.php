<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Tasa de Cambio</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff;">
        <!-- Header -->
        <div style="text-align: right; margin-bottom: 30px; color: #666666; font-size: 14px;">
            San Felipe - Edo. Yaracuy, {{$toDate}}
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 30px;">
            <h1 style="color: #2c3e50; margin: 0 0 10px 0; font-size: 24px;">Notificación de Tasa de Cambio</h1>
            <div style="height: 3px; background-color: #3498db; width: 60px;"></div>
        </div>

        <!-- Content Section -->
        <div style="margin-bottom: 30px;">
            <p style="text-align: justify; margin-bottom: 20px;">
                Se le notifica que se ha <strong>registrado</strong> una nueva tasa de cambio para el día de hoy <strong>{{$toDate ?? null}}</strong>, con los siguientes detalles:
            </p>

            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Número:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">TDC-AD{{ $exchange_rate->id ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Fecha:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">{{ $exchange_rate->date ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Monto Venta:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">{{ $exchange_rate->ammount ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Fuente:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">{{ $exchange_rate->source ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Nombre:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">{{ $exchange_rate->name ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;"><strong>Observaciones:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">{{ $exchange_rate->observations ?? null}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Usuario:</strong></td>
                        <td style="padding: 8px 0;">{{ $exchange_rate->user->username ?? null}}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #666666;">
            <p style="margin: 0;">
                <strong>AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA</strong><br>
                SAN FELIPE ESTADO YARACUY, VENEZUELA<br>
                Teléfonos: 0424-5891682 / 0414-5442298<br>
                Correo electrónico: frayluisamigoyara@hotmail.com
            </p>
        </div>
    </div>
</body>
</html>
