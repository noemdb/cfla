<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formato de Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sub-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .logo {
            width: 70px;
            height: 70px;
        }

        .logo-large {
            width: 100px;
            height: 70px;
        }

        .table-header {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }

        .table-header td {
            vertical-align: middle;
        }

        .table-content {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-content th,
        .table-content td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .section {
            margin-top: 5px;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .divider {
            border-top: 1px solid black;
            margin: 15px 0;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- Encabezado con logos -->
    <table class="table-header">
        <tr>
            <td width="12%">
                <img class="logo" src="{{ public_path('image/avatar/uecfla.jpg') }}">
            </td>
            <td width="76%">
                <div class="header">{{ $institution->name }}</div>
                <div class="sub-header">DIRECCIÓN ACADÉMICA</div>
            </td>
            <td width="12%">
                <img class="logo-large" src="{{ public_path('image/avatar/amigoniano.png') }}">
            </td>
        </tr>
    </table>

    <hr class="divider">

    <!-- Contenido -->
    <div class="header">Formato de Registro</div>

    <div class="section">
        <p><strong>Nombre del Estudiante:</strong> {{ $name }} {{ $lastname }}</p>
        <p><strong>Fecha de Nacimiento:</strong> {{ $date_birth }}</p>
        <p><strong>Representante:</strong> {{ $name_representant }}</p>
        <p><strong>Cédula:</strong> {{ $ci_representant }}</p>
        <p><strong>WhatsApp:</strong> {{ $phone_representant }}</p>
        <p>
            <strong>Grado/Año Seleccionado:</strong> {{ $grado_id }}
            @switch($grado_id)
                @case(22)
                    1er Grupo Inicial
                @break

                @case(23)
                    2do Grupo Inicial
                @break

                @case(24)
                    3er Grupo Inicial
                @break

                @case(1)
                    1er Grado
                @break

                @case(2)
                    2do Grado
                @break

                @case(3)
                    3er Grado
                @break

                @case(4)
                    4to Grado
                @break

                @case(5)
                    5to Grado
                @break

                @case(6)
                    6to Grado
                @break

                @case(12)
                    1er Año
                @break

                @case(13)
                    2do Año
                @break

                @case(14)
                    3er Año
                @break

                @case(10)
                    4to Año
                @break

                @case(11)
                    5to Año
                @break
            @endswitch
        </p>

        <div>
            Te invitamos cordialmente a visitar nuestro colegio en el día y horario de tu conveniencia, entre el 1 y el 10 de abril . Durante este período, se estarán llevando a cabo las actividades propias del Censo Escolar 2025 - 2026 . ¡Esperamos contar con tu presencia para conocernos mejor y acompañarte en este importante proceso!
        </div>
    </div>

    <div class="text-center">
        <p>Atte.</p>
        <p><strong>DIRECCIÓN ACADÉMICA</strong></p>
    </div>

    <hr class="divider">

    <!-- Directivos -->
    <h4 class="sub-header">Directivos de la Institución</h4>
    <div class="section">
        <p><strong>{{ $autoridad1->profile_professional }} {{ $autoridad1->fullname }}</strong></p>
        <p>{{ $autoridad1->position }}</p>
        <p><strong>{{ $autoridad2->profile_professional }} {{ $autoridad2->fullname }}</strong></p>
        <p>{{ $autoridad2->position }}</p>
    </div>

    <hr class="divider">

    <div class="text-center section">
        <h3 class="sub-header">Escanea el código QR para descargar el PDF</h3>
        <img src="{{ $qrCode }}" width="200">
    </div>

    <hr class="divider">

    <!-- Pie de página -->
    <footer class="footer">
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE, YARACUY, VENEZUELA<br>
        Teléfonos: 0424-5891682 - 0414-5442298 | Correo: frayluisamigoyara@hotmail.com
    </footer>

</body>

</html>
