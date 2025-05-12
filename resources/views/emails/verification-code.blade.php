<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Verificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
            padding: 20px;
            background-color: #f3f4f6;
            border-radius: 8px;
            margin: 20px 0;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="message">
        <h2>Código de Verificación</h2>
        <p>Tu código de verificación es:</p>
    </div>

    <div class="code">
        {{ $code }}
    </div>

    <p>Por favor, utiliza este código para verificar tu correo electrónico. Este código expirará en breve por razones de seguridad.</p>

    <p>Si no solicitaste este código, por favor ignora este mensaje.</p>
</body>
</html>
