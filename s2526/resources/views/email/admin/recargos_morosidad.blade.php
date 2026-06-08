<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] }}</title>
</head>
<body>
    <h2>{{ $data['subject'] }}</h2>
    <p>Fecha: {{ $data['toDate'] }}</p>
    <p><strong>Se han generado {{ $data['cantidad_nuevos'] }} nuevos recargos por morosidad.</strong></p>

    <h3>Resumen de ejecución:</h3>
    <pre style="background:#f5f5f5; padding:10px; border:1px solid #ddd;">
{{ $data['log_output'] }}
    </pre>

    <p>Este es un mensaje automático. Por favor, no responda.</p>
</body>
</html>