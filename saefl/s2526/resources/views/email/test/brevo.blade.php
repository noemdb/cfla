<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Correo de Prueba</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 30px; text-align: center; background-color: #0d6efd; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <h1 style="margin: 0; font-size: 24px;">¡Hola {{ $name }} 👋!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px; line-height: 1.5; color: #333333;">
                                Este es un correo de prueba enviado desde <strong>Laravel</strong> utilizando la API de <strong>Brevo</strong>.
                            </p>
                            <p style="font-size: 16px; line-height: 1.5; color: #333333;">
                                Puedes usar esta plantilla para enviar notificaciones, confirmaciones u otros correos transaccionales desde tu aplicación.
                            </p>
                            <hr style="border: none; border-top: 1px solid #dddddd; margin: 30px 0;">
                            <p style="font-size: 14px; color: #999999;">
                                Si recibiste este mensaje por error, ignóralo. No es necesario realizar ninguna acción.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; text-align: center; background-color: #f0f0f0; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                            <p style="margin: 0; font-size: 14px; color: #666666;">&copy; {{ date('Y') }} MiEmpresa. Todos los derechos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
