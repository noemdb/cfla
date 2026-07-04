
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Server Error</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .code {
                border-right: 2px solid;
                font-size: 26px;
                padding: 0 15px 0 15px;
                text-align: center;
                color: darksalmon;
            }
            .muted {
                border-right: 2px solid;
                font-size: 7px;
                padding: 0 15px 0 15px;
                text-align: center;
                color: #636b6f;
            }
            .atras {
                border-right: 2px solid;
                font-size: 14px;
                padding: 0 15px 0 15px;
                text-align: center;
                color: #636b6f;
            }

            .message {
                font-size: 18px;
                text-align: center;
            }
            .thanks {
                color: dodgerblue;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="code">
                Error interno del sistema
            </div>

            <div class="message" style="padding: 10px;">
                El Administrador está trabajando en ello<br> <span class="thanks">Gracias por su paciencia</span>
            </div>
            <br><a href="javascript:history.back()" class="atras">Ir atrás</a>
        </div>
        <footer>
                <small class="muted">{{$exception->getMessage()}}</small>
        </footer>
    </body>
</html>
