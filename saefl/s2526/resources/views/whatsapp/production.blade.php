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

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <strong>Excelente!</strong> Mensaje Enviado correctamente. 
            </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong><div>{{ session('error') }}</div></strong>  
                </div>
            @endif

            <div class="alert alert-info" role="alert">
                <strong>Formulario</strong>
            </div>
            
            <div>

                <div>Completa y envía el siguiente formulario</div>

                <form id="formRepresentant" class="p-3 border rounded" action="{{route('whatsapp.meta.template.custom.production')}}">
                    <div class="form-group">
                        <label for="ident">Cédula del Representante <small class="text-muted">16483834</small></label>
                        <input value="{{$ident}}" type="number" name="ident" id="ident" class="form-control" placeholder="Ingrese la cédula" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Número de Teléfono <small class="text-muted">Ej: 584141560309</small></label>
                        <input value="{{$phone}}" type="number" name="phone" id="phone" class="form-control" placeholder="Ingrese su teléfono (584141560309)" required>
                        <p id="error"></p>
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary btn-block">Enviar</button>
                </form>
                
            </div>
        </div>
        

        
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const phoneInput = document.querySelector("#phone");
            const form = document.querySelector("#formRepresentant");
            const errorMessage = document.querySelector("#error");
        
            // Validación del formato de número telefónico
            const validatePhone = (phone) => {
                const phonePattern = /^58(4\d{9})$/; // 58 seguido de 4 y 9 dígitos
                return phonePattern.test(phone);
            };
        
            form.addEventListener("submit", (event) => {
                const phoneValue = phoneInput.value.trim(); // Elimina espacios en blanco
        
                if (!validatePhone(phoneValue)) {
                    event.preventDefault(); // Evita el envío del formulario si no es válido
                    errorMessage.textContent = "Por favor, ingresa un número de teléfono válido (Ej: 584141560309)";
                    errorMessage.style.color = "red";
                    phoneInput.style.borderColor = "red"; // Resalta el campo con error
                } else {
                    errorMessage.textContent = ""; // Limpia el mensaje de error si es válido
                    phoneInput.style.borderColor = ""; // Elimina el resaltado del borde
                }
            });
        
            // Limpia el mensaje de error al escribir en el campo
            phoneInput.addEventListener("input", () => {
                errorMessage.textContent = "";
                phoneInput.style.borderColor = "";
            });
        });
    </script>           

    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>
</body>
</html>
