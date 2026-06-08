<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debate Questions</title>
    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <style>
        /* Estilos generales */
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .list-group-item:hover {
            background-color: #f8f9fc;
        }
        .selected-answer {
            background-color: #f1f8f1 !important;
            font-weight: 500;
        }
        .page-header {
            padding: 2rem 0;
            background: linear-gradient(to right, #4e73df, #36b9cc);
            color: white;
            margin-bottom: 2rem;
            border-radius: 0.35rem;
        }

        /* Estilos para impresión */
        @media print {
            body {
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
                font-size: 12pt; /* Tamaño de fuente adecuado para impresión */
                line-height: 1.6;
                background: none;
                color: black;
            }
            .container {
                width: 100%;
                max-width: 670px; /* Ancho máximo para hoja carta */
                margin: 0 auto; /* Centrar el contenido */
                padding: 1rem; /* Márgenes internos */
                display: flex;
                flex-wrap: wrap; /* Permitir que las columnas se ajusten */
                gap: 1rem; /* Espacio entre columnas */
            }
            .row {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
            }
            .col-lg-6 {
                flex: 0 0 calc(50% - 0.5rem); /* Dos columnas con espacio entre ellas */
                max-width: calc(50% - 0.5rem);
            }
            .card {
                box-shadow: none; /* Eliminar sombras */
                border: 1px solid #ddd; /* Borde sencillo para separar elementos */
                page-break-inside: avoid; /* Evitar que el bloque se divida entre páginas */
                break-inside: avoid; /* Compatibilidad con navegadores modernos */
            }
            .card-header {
                background-color: white; /* Fondo blanco para impresión */
                border-bottom: 1px solid #ccc;
            }
            .page-header {
                background: none; /* Eliminar gradientes */
                color: black;
                padding: 1rem 0;
            }
            .list-group-item {
                background-color: white; /* Fondo blanco para impresión */
            }
            .selected-answer {
                background-color: #f1f8f1 !important;
                font-weight: bold;
            }
            h1, h4 {
                text-align: center;
                margin: 0.5rem 0;
            }
            @page {
                size: letter; /* Tamaño de papel carta */
                margin: 1cm; /* Márgenes para la hoja */
            }
        }
    </style>
</head>
<body class="bg-light">
    
    <div class="container py-5">
        <div class="table-secondary p-2 rounded">
            <h1 class="text-center mb-4 font-weight-bold ">Preguntas de la Competición, debate académico</h1>
            <h4 class="text-center mb-4 ">
                <div class="d-flex justify-content-between font-weight-bold">
                    <div>Grado/Año: {{$grado->name ?? null}}</div>
                    <div>Categria: {{$category ?? null}}</div>
                </div>
            </h4>
        </div>
        

        <div class="row">

            @forelse ($questions as $question)
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 border-1 m-2 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">Pregunta {{$loop->iteration}}</h5>
                            <div>
                                <span class="badge badge-pill badge-primary font-weight-bold p-2">  {{$question->weighting ?? null}}</span>
                                <span class="badge badge-pill badge-success font-weight-bold p-2">  {{$question->time ?? null}}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="lead mb-4">{{$question->text ?? null}}</p>
                            <ul class="list-group list-group-flush">
                                @php $options = $question->options ; @endphp
                                @forelse ($options as $option)
                                <li class="list-group-item d-flex align-items-center {{ ($option->status_option_correct) ? 'selected-answer border-left-success' : null }} ">
                                    <span class="badge {{ ($option->status_option_correct) ? 'badge-success' : ' badge-light' }} mr-3">{{ chr(64 + $loop->iteration) }}</span> {{ $option->text ?? null }}.
                                </li>
                                @empty
                                <li class="list-group-item d-flex align-items-center">
                                    No hay opciones de respuesta.
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @empty

            <div class="col-lg-6 col-md-12 mb-4">
                No hay preguntas
            </div>
                
            @endforelse
        </div>
    
    </div>

    <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script>
</body>
</html>