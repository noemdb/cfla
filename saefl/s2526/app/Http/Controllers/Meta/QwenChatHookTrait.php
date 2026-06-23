<?php

namespace App\Http\Controllers\Meta;

use App\Models\app\Agentia\PromptContext;
use App\Models\app\Meta\WebhookMessage;
use App\Services\QwenService;

trait QwenChatHookTrait
{
    protected $code = "webhook-qwen";
    public array $messages = ['user' => 'qwen', 'text' => "Espero obtener mucha ayuda, el texto que generes lo usaré responsablemente."];
    public array $contents;
    public String $responseMessage;

    public function getPromptContext($from)
    {
        $this->contents[] = ["role" => "system", "content" => "
        Eres un asistente (Nombre: Agente SAEFL) muy útil, responde como tal a mensajes enviados en la plataforma whatsapp. Eres un asistente automático del colegio. Solo puedes responder con la información contenida en el contexto proporcionado. Si no tienes certeza absoluta sobre una información (por ejemplo, si un estudiante ya fue aceptado), debes indicarlo claramente. Nunca asumas, completes ni infieras información que no esté escrita explícitamente en el contexto dado. Si la respuesta depende de otra etapa del proceso, indica cuál es la siguiente fase sin confirmar resultados."];

        $this->contents[] = [
            "role" => "system",
            "content" => "La siguiente información contenida en una estructura JSON es de la institución educativa: " . json_encode([
                "Rol" => [
                    "nombre" => "Asistente Autorizado SAEFL",
                    "prompt_de_control" => "Eres un asistente automático del colegio. Solo puedes responder con la información contenida en el contexto proporcionado. Si no tienes certeza absoluta sobre una información (por ejemplo, si un estudiante ya fue aceptado), debes indicarlo claramente. Nunca asumas, completes ni infieras información que no esté escrita explícitamente en el contexto dado. Si la respuesta depende de otra etapa del proceso, indica cuál es la siguiente fase sin confirmar resultados."
                ],
                "institucion" => [
                    "nombre" => "Colegio Fray Luis Amigó",
                    "fundacion" => "1958",
                    "caracter" => "Privado, sin fines de lucro, católico",
                    "carisma" => "Amigoniano",
                    "mision" => "Formar integralmente niños, adolescentes y jóvenes para la vida en sociedad y la transformación social mediante el uso eficiente de la pedagogía amigoniana.",
                    "vision" => "Ser reconocido como una institución de excelencia centrada en valores cristianos, innovación y sinergia educativa.",
                    "direccion" => [
                        "director_general" => "Fray Ezequiel Sierra",
                        "direccion_academica" => "Escarleth López"
                    ]
                ],
                "programas_educativos" => [
                    "modelo_pedagogico" => "Aprendizaje basado en proyectos",
                    "reformas_curriculares" => [
                        "2017 - Ministerio de Educación de Venezuela",
                        "2023 - Ministerio de Educación de Venezuela"
                    ],
                    "metodologia" => "STEM",
                    "areas_de_formacion" => 200,
                    "evaluacion" => "Diferentes instrumentos de evaluación",
                    "actividades_complementarias" => ["Servicio comunitario"],
                    "planes_educativos" => [
                        [
                            "nombre" => "Plan de Educación Inicial",
                            "descripcion" => "Desarrollo integral de niños de 4 a 6 años con énfasis en habilidades cognitivas, motoras y socioemocionales."
                        ],
                        [
                            "nombre" => "Plan de Educación Primaria",
                            "descripcion" => "Formación académica en áreas fundamentales, desarrollo de valores y fomento del pensamiento crítico en estudiantes de 1° a 6° grado."
                        ],
                        [
                            "nombre" => "Plan de Educación Media General - Ciencias y Tecnología",
                            "descripcion" => "Preparación académica en el área de ciencias y tecnología, con enfoque en innovación, pensamiento lógico y aplicación práctica del conocimiento."
                        ],
                        [
                            "nombre" => "Plan de Formación en Valores Amigonianos",
                            "descripcion" => "Programa transversal basado en la espiritualidad de Fray Luis Amigó, promoviendo la formación humana y cristiana."
                        ]
                    ]
                ],
                "procesos_administrativos" => [
                    "inscripcion" => [
                        "requisitos" => [
                            "Registro en el censo escolar",
                            "Partida de nacimiento",
                            "Informe de evaluación",
                            "Constancia de solvencia"
                        ],
                        "costo" => "USD 90",
                        "mensualidad" => "USD 90"
                    ],
                    "becas_y_descuentos" => [
                        "Pronto pago",
                        "Beca docente",
                        "Becas especiales"
                    ],
                    "mora_y_pagos" => [
                        "procedimiento" => "Se conforma un expediente y se remite a los organismos competentes"
                    ],
                    "documentos_academicos" => [
                        "Constancia de inscripción",
                        "Constancia de estudios",
                        "Carta digital de aceptación",
                        "Formato de registro en el censo escolar"
                    ]
                ],
                "servicios_estudiantiles" => [
                    "alimentacion" => "Cantina interna",
                    "transporte" => "Disponible para actividades especiales",
                    "apoyo_academico" => "Círculos de estudio en horario de la tarde"
                ],
                "disciplina_y_convivencia" => [
                    "manejo_de_incidentes" => "Atención temprana a incidencias por docentes y especialistas",
                    "correctivos_pedagogicos" => "Aplicados según los acuerdos de convivencia escolar",
                    "comunicacion_con_representantes" => "A través de redes sociales, correo electrónico y otros instrumentos"
                ],
                "orientacion_y_bienestar" => [
                    "coordinacion" => "Coordinación de Bienestar Estudiantil",
                    "orientacion_vocacional" => "Apoyo en la preparación para educación superior"
                ],
                "censo_escolar" => [
                    "coordinacion" => "Coordinación de Bienestar Estudiantil",
                    "descripcion" => "
Proceso de Censo Escolar para Nuevo Ingreso (Periodo Escolar 2025-2026)

El presente proceso tiene como objetivo identificar y seleccionar a los estudiantes de nuevo ingreso que formarán parte de la matrícula escolar para el periodo 2025-2026. Se articula en una serie de etapas cuidadosamente diseñadas para garantizar la transparencia, equidad y eficiencia en la admisión de nuevos alumnos.

Fase 1: Registro de la Manifestación de Interés

Este es el punto de partida para todos los aspirantes a un cupo en nuestra institución. Los padres, madres o representantes legales de los estudiantes interesados deberán completar un Registro de Manifestación de Interés. Este registro se habilitará a través de [Especificar la plataforma o medio: página web de la escuela, formulario en línea, presencial en la secretaría, etc.] durante un periodo determinado, comprendido entre [Fecha de inicio] y [Fecha de fin].

En este registro, se solicitará información básica del aspirante (nombre completo, fecha de nacimiento, datos de contacto) y del representante legal. La formalización de este registro no garantiza la obtención del cupo, sino que constituye el primer paso para participar en el proceso de selección.

Fase 2: Actividades de Selección de Estudiantes

Una vez finalizado el periodo de registro, se dará inicio a la fase de selección. Esta etapa comprenderá una serie de actividades diseñadas para evaluar las aptitudes, conocimientos y el perfil de los aspirantes, considerando el nivel educativo al que desean ingresar. Las actividades podrán incluir, pero no se limitan a:

Evaluaciones Diagnósticas: Pruebas escritas u orales para identificar el nivel de desarrollo académico de los aspirantes en áreas relevantes para su futuro desempeño escolar.
Entrevistas (Individuales o Grupales): Espacios para conocer a los aspirantes, sus motivaciones e intereses, así como para evaluar aspectos socioemocionales y de comunicación.
Observación de Desempeño (para ciertos niveles): Actividades lúdicas o prácticas que permitan observar habilidades específicas según la edad de los aspirantes.
Revisión de Expedientes (si aplica): Análisis de informes de años escolares anteriores o documentos que aporten información relevante sobre el desarrollo del estudiante.
Los criterios específicos de evaluación y las ponderaciones de cada actividad serán definidos y comunicados previamente a los representantes legales a través de [Especificar el medio de comunicación: página web, correo electrónico, circulares informativas, etc.].

Fase 3: Comunicación de Resultados y Carta de Aceptación

Una vez culminada la fase de selección y analizados los resultados, la institución educativa procederá a comunicar las decisiones a los aspirantes. Aquellos estudiantes que hayan sido seleccionados para formar parte de la matrícula del periodo escolar 2025-2026 recibirán una Carta de Aceptación formal.

Esta carta será enviada de manera individual a la dirección de correo electrónico proporcionada en el Registro de Manifestación de Interés por el representante legal del estudiante. La Carta de Aceptación contendrá información relevante sobre los siguientes pasos a seguir para formalizar la inscripción, incluyendo plazos, documentación requerida y cualquier otra indicación pertinente.

Es importante destacar que la recepción de la Carta de Aceptación implica la reserva del cupo, la cual deberá ser confirmada por el representante legal dentro del plazo estipulado en la carta para completar el proceso de matriculación.

Este proceso de censo escolar busca asegurar una selección informada y justa de los nuevos estudiantes, contribuyendo a la construcción de una comunidad educativa sólida y comprometida con el aprendizaje."
                ]
            ], JSON_PRETTY_PRINT)
        ];


        // $content = PromptContext::getContextForCode($this->code);
        // $this->contents[] = ["role" => "system", "content" => $content];

        $menu = $this->getDefaultMenuResponse();
        $this->contents[] = ["role" => "system", "content" => "EL menú de opciones disponibles como asistente es el siguiente: " . $menu];

        $stringSql = WebhookMessage::where('from', $from)->where('type', 'text')->orderBy('id', 'desc')->limit(10)->get()->pluck('body')->implode(";\n");
        $historyChat = 'Los últimos mensaje recibidos del usuario: \n' . $stringSql;
        $this->contents[] = ["role" => "system", "content" => $historyChat];

        $this->contents[] = ["role" => "system", "content" => "Evita el uso de palabras técnicas propias de la implementación de modelos de aprendizajes, por ejemplo: json, xml, etc"];

        $this->contents[] = ["role" => "system", "content" => "Evita reposnder con mas de 100 palabras, no es conveniente respuestas muy largas"];

        // dd($this->contents);
    }

    public function generateQwenResponse($request, $from)
    {
        $this->getPromptContext($from);

        $this->contents[] = ['role' => 'user', 'content' => $request]; //dd($this->contents);

        $qwenService = new QwenService;
        $response = $qwenService->sendMessage($this->contents);

        if (isset($response['error'])) {
            $this->responseMessage = "Ocurrió algo inesperado, lo síento...";
        } else {
            $this->responseMessage = $response['choices'][0]['message']['content'] ?? 'Respuesta no encontrada.';
            // $this->responseMessage = $this->formatToHtml($response_text);
        }
        return $this->responseMessage;
    }

    public function formatToHtml($text)
    {
        // Encabezados (### Título)
        $text = preg_replace('/### (.+)/', '<h3>$1</h3>', $text);

        // Negritas (**texto**)
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Listas numeradas (1., 2., 3., etc.)
        $text = preg_replace_callback('/(\d+)\. (.+?)(?=\n\d+\. |\n-|$)/s', function ($matches) {
            $items = preg_split('/\n- /', $matches[2]);
            $listItems = '';
            foreach ($items as $item) {
                $listItems .= '<li>' . trim($item) . '</li>';
            }
            return '<ol start="' . $matches[1] . '">' . $listItems . '</ol>';
        }, $text);

        // Listas con guiones (- Item)
        $text = preg_replace_callback('/(?:^|\n)- (.+?)(?=\n(?:- |\d+\. )|$)/s', function ($matches) {
            $items = preg_split('/\n- /', $matches[0]);
            $listItems = '';
            foreach ($items as $item) {
                if (trim($item) !== '-') {
                    $listItems .= '<li>' . trim(ltrim($item, '- ')) . '</li>';
                }
            }
            return '<ul>' . $listItems . '</ul>';
        }, $text);

        // Párrafos (divide el texto por saltos de línea dobles)
        $text = preg_replace('/\n{2,}/', '</p><p>', $text);
        $text = '<p>' . $text . '</p>';

        return $text;
    }


    public function generateQwenResponseSend($message, $from = null)
    {
        $qwenService = new QwenService();
        $messageOriginal = $message;

        // Construir el contenido del mensaje para Qwen
        $this->contents = [
            [
                "role" => "system",
                "content" => "Eres un asistente muy útil. Mejora el siguiente mensaje en menos de 35 palabras, con un tono amable y cordial. Usa emojis de WhatsApp y formatea el texto para un mensaje en esa plataforma."
            ],
            [
                "role" => "user",
                "content" => 'Mejora el mensaje: ' . $message . '. La respuesta debe ser un JSON con esta estructura:
                {
                    "oldMessege": "coloca aquí el mensaje recibido que se va a ser mejorado",
                    "newMessege": "coloca aquí el mensaje mejorado"
                }'
            ]
        ];

        // Enviar el mensaje a la API de Qwen
        $response = $qwenService->sendMessage($this->contents);

        // Manejar la respuesta
        if (isset($response['error'])) {
            return [
                "oldMessege" => $message,
                "newMessege" => $messageOriginal
            ];
        }

        $responseText = $response['choices'][0]['message']['content'] ?? null;

        // Decodificar el JSON de la respuesta
        $decodedResponse = json_decode($responseText, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($decodedResponse['newMessege'])) {
            return $decodedResponse;
        }

        // Si no se puede decodificar, devolver un mensaje de error
        return [
            "oldMessege" => $message,
            "newMessege" => $messageOriginal
        ];
    }
}
