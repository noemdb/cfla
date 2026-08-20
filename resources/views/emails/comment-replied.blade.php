<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Te respondieron en tu comentario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #10b981;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #065f46;
        }
        .quote {
            background-color: #f3f4f6;
            border-left: 4px solid #d1d5db;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin: 16px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .reply {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 16px 0;
        }
        .reply .author {
            font-weight: bold;
            color: #065f46;
            font-size: 14px;
            margin-bottom: 6px;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>¡Tu comentario recibió una respuesta!</h2>
    </div>

    <p>Tu comentario en la actividad <strong>{{ $activityTitle }}</strong> fue respondido.</p>

    <div class="quote">
        <em>{{ $yourComment }}</em>
    </div>

    <div class="reply">
        <div class="author">{{ $authorName }}</div>
        <div>{!! $replyBody !!}</div>
    </div>

    <a href="{{ $activityUrl }}" class="button">Ver la conversación</a>

    <div class="footer">
        <p>Este correo fue enviado automáticamente. No lo respondas directamente.</p>
    </div>
</body>
</html>