<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Preguntas</title>
    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <style>
        /* Estilos generales */
        body {
            font-size: 14px;
        }
        .table th {
            background-color: #f8f9fc;
            text-align: center;
        }

        /* Estilos para preguntas y opciones */
        .question-text {
            font-size: 18px; /* Aumentado en 2px */
            font-weight: bold;
        }
        .option-text {
            font-size: 17px; /* Aumentado en 2px */
        }
        .option-correct {
            font-weight: bold;
            color: #28a745; /* Verde para la opción correcta */
        }

        /* Estilos de impresión */
        @media print {
            body {
                font-size: 12pt;
                line-height: 1.6;
                color: black;
            }
            .container {
                width: 100%;
                max-width: 700px;
                margin: 0 auto;
                padding: 1rem;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
            }
            .table th, .table td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            .question-text {
                font-size: 16pt !important; /* Aumentado en 2pt para impresión */
            }
            .option-text {
                font-size: 15pt !important; /* Aumentado en 2pt para impresión */
            }
            .option-correct {
                font-weight: bold;
                color: green !important;
            }
            h1, h4 {
                text-align: center;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-light">

    <div class="container py-4">
        <div class="table-secondary p-3 rounded">
            <h1 class="text-center mb-3 font-weight-bold">Preguntas de la Competición por Grado/Año- Debate Académico</h1>
            <h4 class="text-center mb-3">
                <div class="d-flex justify-content-between font-weight-bold">
                    <div>Grado/Año: {{$grado->name ?? 'N/A'}}</div>
                </div>
            </h4>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pregunta</th>
                    <th>Opciones</th>
                    <th>Ponderación</th>
                    <th>Tiempo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions->groupBy('category') as $category => $groupedQuestions)
                <tr>
                    <td colspan="5" class="bg-gray-200 font-bold text-left px-3 py-2">
                        Categoría: {{ $category ?? 'Sin categoría' }}
                    </td>
                </tr>

                @foreach ($groupedQuestions->chunk(5) as $chunk)
                    @foreach ($chunk as $question)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="question-text">{!! $question->text ?? 'Pregunta no disponible' !!}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($question->options as $option)
                                        <li class="option-text {{ $option->status_option_correct ? 'option-correct' : '' }}">
                                            <strong>{{ chr(64 + $loop->iteration) }}.</strong>
                                            {!! $option->text ?? 'Opción no disponible' !!}
                                            @if ($option->status_option_correct)
                                                ✔
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center">{{ $question->weighting ?? 'N/A' }}</td>
                            <td class="text-center">{{ $question->time ?? 'N/A' }}</td>
                        </tr>
                    @endforeach

                    {{-- Línea separadora entre chunks --}}
                    <tr>
                        <td colspan="5" class="bg-gray-100 text-center py-2">
                            &nbsp;
                        </td>
                    </tr>
                @endforeach
            @endforeach

            </tbody>
        </table>
    </div>

    <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script>
</body>
</html>
