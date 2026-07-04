<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Envío de Mensajes por WhatsApp</title>
    <!-- Bootstrap 4.3 CSS -->
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario para la prueba del envío de mensajes a través de la plataforma WhatsApp Messenger</h2>
        <div class="border rounded p-2">

            @if ($status)
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong>Excelente!</strong> Mensaje Enviado correctamente.
                </div>
            @endif

            <div class="alert alert-info" role="alert">
                <strong>Instruciones</strong>
            </div>
            
            <div>

                <ul class="list-group">
                    <li class="list-group-item ">
                        <strong>Paso 1:</strong> Añadir contacto <br>
                        a. Guarda el siguiente número en tu agenda de contactos: +1 415 523 8886 .<br>
                        b. Asígnale el nombre *WhatsApp Sender*.<br>
                    </li>
                    <li class="list-group-item ">
                        <div class="">
                            <strong>Paso 2:</strong> Enviar palabra clave <br>
                            a. Abre WhatsApp y selecciona el contacto *WhatsApp Sender*. <br>
                            b. Envía el siguiente mensaje exactamente como se indica: join bank-pleasure. <br>
                            c. Espera la confirmación de que estás registrado en el servicio. <br>
                        </div>                        
                    </li>

                    <li class="list-group-item ">
                        <div><strong>Paso 3:</strong> Completa y envía el siguiente formulario</div>

                        <form id="whatsappForm" class="p-3 border rounded">
                            <div class="form-group">
                                <label for="ident">Cédula del Representante <small class="text-muted">14608133</small></label>
                                <input value="{{$ident}}" type="number" name="ident" id="ident" class="form-control" placeholder="Ingrese la cédula" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Número de Teléfono <small class="text-muted">+584145752242</small></label>
                                <input value="{{$phone}}" type="tel" name="phone" id="phone" class="form-control" placeholder="Ingrese su teléfono" required>
                            </div>
                            <button type="button" id="submitButton" class="btn btn-primary btn-block">Enviar</button>
                        </form>

                    </li>

                    <li class="list-group-item ">
                        <strong>Paso 4:</strong> Verificar recepción del mensaje <br>
                        a. Una vez enviada la información, el sistema enviará un mensaje de prueba al número indicado. <br>
                        b. Asegúrate de que el mensaje haya llegado al teléfono. <br>
                    </li>
                </ul>
                
            </div>
        </div>
        

        
    </div>

    <!-- JavaScript para redirigir al usuario -->
    <script>
        document.getElementById('submitButton').addEventListener('click', function() {
            const ident = document.getElementById('ident').value;
            const phone = document.getElementById('phone').value;

            if (ident && phone) {
                // Construye la URL dinámica
                // const url = `{env('APP_URL')}/saefl/send-whatsapp/test/${ident}/${phone}`;
                const url = `{{env('APP_URL')}}/send-whatsapp/test/${ident}/${phone}`;
                window.location.href = url; // Redirigir al usuario a la ruta generada
            } else {
                alert('Por favor, complete todos los campos antes de enviar.');
            }
        });
    </script>

    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>
</body>
</html>
