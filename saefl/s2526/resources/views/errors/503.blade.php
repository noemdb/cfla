
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SAEFL - Actualizando...</title>

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
            .brand{
                color: #004000;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="code">
                El <span class="brand">SAEFL</span> se está actualizando...  
            </div>

            <div class="message" style="padding: 10px;">
                Añadiendo nuevas capacidades<br>
                En breve estará listo<br>
                <span class="thanks">Gracias por su paciencia</span>               
            </div>
            {{-- <br><a href="javascript:history.back()" class="atras">Ir atrás</a> --}}
        </div>
        <footer>
                <small class="muted">{{$exception->getMessage()}}</small>
        </footer>
    </body>
</html>
