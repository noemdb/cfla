<?php

// Académico
// Disciplinario
// Social
// Emocional
// Familiar
// Otro


$arr_positive = [
    "Logros académicos" => [
        "Gran progreso en rendimiento académico" => [
            'Académico'=>[
                "El estudiante mejoró significativamente su promedio de calificaciones en un período de tiempo determinado",
                "El estudiante logró un aumento notable en su rendimiento académico en una o varias asignaturas",
            ],
        ],
        "Mejor índice académico [Plan de Formación, Grado, Sección]" => [
            'Académico'=>[
                "El estudiante obtuvo el promedio más alto de su plan de formación en un período de tiempo determinado",
                "El estudiante obtuvo el promedio más alto de su grado en un período de tiempo determinado",
                "El estudiante obtuvo el promedio más alto de su sección en un período de tiempo determinado",
            ],
        ],
        "Mérito académico en áreas de ciencias e investigación" => [
            'Académico'=>[
                "El estudiante obtuvo un reconocimiento por su participación destacada en un evento científico o de investigación",
                "El estudiante realizó una investigación original y rigurosa sobre un tema relevante para su área de estudio",
            ],
        ]
    ],

    "Participación y liderazgo" => [
        "Actividades extracurriculares" =>[
            "Social" =>  [
                "El estudiante participó en actividades extracurriculares como clubes o grupos de estudio",
                "El estudiante demostró un compromiso destacado en actividades extracurriculares"
            ],
        ],
        "Liderazgo" => [
            "Social" =>  [
                "El estudiante mostró habilidades de liderazgo al organizar y dirigir proyectos o eventos",
                "El estudiante fue reconocido por su liderazgo en el ámbito escolar",
            ],
        ],
        "Ayuda a un compañero de clase" => [
            "Social" =>  [
                "El estudiante brindó apoyo y ayuda a un compañero de clase en dificultades académicas o personales",
                "El estudiante demostró empatía y solidaridad al ayudar a un compañero de clase",
            ],
        ]
    ],

    "Investigación y creatividad" => [
        "Proyecto de investigación" => [
            "Académico" =>  [
                "El estudiante realizó una investigación original y rigurosa sobre un tema relevante para su área de estudio",
                "El estudiante presentó un proyecto de investigación innovador que aborda un problema importante en su campo de estudio",
            ],
        ],
        "Creatividad" => [
            "Académico" =>  [
                "El estudiante demostró habilidades creativas al desarrollar un proyecto artístico original y significativo",
                "El estudiante utilizó su creatividad para resolver un problema complejo en su área de estudio",
            ],
        ]
    ],

    "Servicio comunitario y responsabilidad social" => [ //Académico, Disciplinario, Social, Emocional, Familiar, Otro
        "Proyecto comunitario" => [
            "Social" =>  [
                "El estudiante lideró o participó en un proyecto que benefició a la comunidad local",
                "El estudiante trabajó en equipo para desarrollar un proyecto que aborda un problema social importante",
            ],
        ],
        "Mentoría" => [
            "Social" =>  [
                "El estudiante brindó apoyo y orientación a otros estudiantes para ayudarlos a alcanzar sus metas académicas o personales",
                "El estudiante participó en un programa de mentoría que ayudó a otros estudiantes a desarrollar habilidades y conocimientos importantes",
            ],
        ],
        "Sostenibilidad" => [
            "Social" =>  [
                "El estudiante lideró o participó en un proyecto que promueve la sostenibilidad ambiental o social",
                "El estudiante trabajó en equipo para desarrollar un proyecto que aborda un problema ambiental o social importante",
            ],
        ],
        "Diversidad" => [
            "Social" =>  [
                "El estudiante participó en actividades que promueven la inclusión y la diversidad en la comunidad escolar o local",
                "El estudiante lideró o participó en un proyecto que aborda un problema relacionado con la discriminación o la exclusión social",
            ],
        ],
        "Ambiente inclusivo" => [
            "Social" =>  [
                "El estudiante trabajó para crear un ambiente escolar o comunitario más inclusivo y acogedor para todos",
                "El estudiante lideró o participó en un proyecto que promueve la inclusión y la igualdad de oportunidades para todos",
            ],
        ],
        "Ayuda a los demás" => [
            "Social" =>  [
                "El estudiante brindó apoyo y ayuda a personas necesitadas en la comunidad local",
                "El estudiante participó en un programa de voluntariado que ayudó a personas necesitadas a mejorar su calidad de vida",
            ],
        ]
    ],

    "Habilidades sociales y emocionales" => [
        "Resolución de conflictos" => [
            "Social" =>  [
                "El estudiante demostró habilidades para resolver conflictos de manera efectiva y pacífica",
                "El estudiante participó en actividades que promueven la resolución de conflictos y la mediación",
            ],
        ],
        "Actitud positiva" => [
            "Social" =>  [
                "El estudiante mantuvo una actitud positiva y constructiva en situaciones difíciles o desafiantes",
                "El estudiante participó en actividades que promueven la actitud positiva y el bienestar emocional",
            ],
        ],
        "Comunicación efectiva" => [
            "Social" =>  [
                "El estudiante demostró habilidades para comunicarse de manera clara y efectiva con sus compañeros y profesores",
                "El estudiante participó en actividades que promueven la comunicación efectiva y la escucha activa",
            ],
        ],
        "Perseverancia" => [
            "Social" =>  [
                "El estudiante demostró perseverancia y determinación para alcanzar sus metas académicas y personales",
                "El estudiante participó en actividades que promueven la perseverancia y la resiliencia emocional",
            ],
        ]
    ],

    "Deportes y actividad física" => [
        "Espíritu deportivo" => [
            "Social" =>  [
                "El estudiante demostró un compromiso y una actitud positiva hacia el deporte y la actividad física",
                "El estudiante participó en actividades deportivas y físicas con entusiasmo y dedicación",
            ],
        ],
        "Notable condición física" => [
                "Social" =>  [
                "El estudiante demostró una buena condición física y habilidades atléticas destacadas",
                "El estudiante participó en actividades físicas y deportivas con regularidad y mantuvo un estilo de vida activo",
            ],
        ]
    ],

    "Ciencias y tecnología" => [
        "Premio en un concurso" => [
            "Académico" =>  [
                "El estudiante recibió un reconocimiento por su destacada participación y logros en un concurso relacionado con las ciencias y la tecnología",
                "El estudiante demostró habilidades sobresalientes en un área específica de las ciencias y la tecnología, lo que le permitió ganar un premio en un concurso",
            ],
        ],
        "Participación destacada" => [
            "Académico" =>  [
                "El estudiante se destacó por su participación activa y compromiso en actividades relacionadas con las ciencias y la tecnología",
                "El estudiante demostró un alto nivel de interés y conocimiento en áreas específicas de las ciencias y la tecnología, lo que le permitió destacarse en su participación",
            ],
        ]
    ],

    "Desarrollo personal" => [
        "Argumentos sólidos en un debate" => [
            "Personal" =>  [
                "El estudiante demostró habilidades para argumentar de manera efectiva y persuasiva en un debate o discusión",
                "El estudiante participó en actividades que promueven el desarrollo de habilidades de debate y argumentación",
            ],
        ],
        "Habilidad para mediar en conflictos" => [
            "Personal" =>  [
                "El estudiante demostró habilidades para mediar en conflictos y resolver disputas de manera efectiva y pacífica",
                "El estudiante participó en actividades que promueven el desarrollo de habilidades de mediación y resolución de conflictos",
            ],
        ]
    ],
];


$arr_negative = [
    "Dificultades académicas" => [
        "Dificultades académicas y atención en clase" =>[
            "Académico" =>  [
                "En ocasiones, el estudiante ha experimentado dificultades para completar las actividades asignadas en clase dentro del plazo establecido.",
                "En algunas situaciones, el estudiante puede distraerse durante las explicaciones del docente en clase.",
                "El estudiante ha tenido ausencias injustificadas en clases.",
                "El estudiante ha llegado tarde a clase sin justificación.",
                "El estudiante ha abandonado la clase sin permiso.",
                "El estudiante ha tenido dificultades para seguir las instrucciones dadas por los docentes.",
                "El estudiante ha mostrado falta de interés en participar activamente en las actividades escolares.",
                "El estudiante ha tenido dificultades para organizar su tiempo y cumplir con los plazos establecidos.",
                "El estudiante ha mostrado falta de responsabilidad al no completar las tareas asignadas.",
                "El estudiante ha tenido dificultades para seguir las indicaciones de los docentes durante las evaluaciones.",
            ],
        ],
    ],

    "Comportamiento, conducta y relaciones interpersonales" => [
        "Comportamiento y respeto hacia los demás" => [
            "Disciplinario" =>  [
                "Se ha observado que el estudiante podría mejorar su atención y consideración hacia los demás.",
                "Se ha observado que el estudiante podría mejorar su comprensión sobre el respeto y el cuidado de las instalaciones y materiales de la institución.",
                "A veces, el estudiante puede olvidar la importancia de garantizar que todos los estudiantes tengan la oportunidad de participar en las clases y actividades escolares.",
                "A veces, el estudiante puede olvidar que se fundamental utilizar un lenguaje respetuoso y cuidadoso al interactuar con los demás.",
                "En ocasiones, el estudiante puede olvidar la importancia de cuidar y proteger a sus compañeros.",
                "A veces, el estudiante puede olvidar la importancia de tratar a todos los estudiantes con respeto y consideración.",
            ],
        ],
        "Conducta inapropiada" => [
            "Disciplinario" =>  [
                "El estudiante ha mostrado falta de cortesía o respeto hacia los maestros o el personal.",
                "El estudiante ha mostrado comportamiento violento o agresivo hacia otros estudiantes o el personal.",
                "El estudiante ha causado daños a la propiedad de la instituciòn.",
                "El estudiante ha infringido las normas al traer armas o drogas a la escuela.",
                "El estudiante ha participado en actividades no permitidas por la ley en la escuela.",
                "El estudiante ha cometido actos de robo hacia otros estudiantes o el personal.",
                "El estudiante ha amenazado o intimidado a otros estudiantes o al personal.",
                "El estudiante ha difundido información falsa o rumores sobre otros estudiantes o el personal.",
                "El estudiante ha publicado contenido inapropiado o perjudicial en línea.",
                "El estudiante ha participado en conductas inapropiadas de naturaleza sexual en la escuela.",
            ],
        ],
        "Habilidades sociales y emocionales" => [
            "Social" =>  [
                "Se ha observado que el estudiante podría mejorar su capacidad para trabajar en equipo y colaborar con sus compañeros.",
                "Se ha observado que el estudiante podría mejorar su capacidad para expresar sus ideas y opiniones de manera respetuosa.",
                "El estudiante ha mostrado falta de compromiso con su propio aprendizaje y desarrollo académico.",
                "El estudiante ha tenido dificultades para seguir las normas y reglas establecidas en la institución.",                
                "El estudiante ha tenido dificultades para adaptarse a los cambios y nuevas situaciones en el entorno escolar.",
                "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y aprender de los errores.",
                "El estudiante ha mostrado falta de interés en participar en actividades extracurriculares y eventos escolares.",
                "El estudiante ha tenido dificultades para mantener una comunicación efectiva con sus compañeros y docentes.",                
            ],
            "Emocional" =>  [
                "El estudiante ha mostrado falta de empatía hacia las necesidades y sentimientos de sus compañeros.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar el estrés y la presión académica.",
                "Se ha observado que el estudiante podría mejorar su capacidad para resolver conflictos de manera pacífica y constructiva.",
            ],
        ],
        "Tolerancia y aceptación de la diversidad" => [
            "Social" =>  [
                "El estudiante ha mostrado falta de respeto hacia las opiniones y perspectivas diferentes a las suyas.",
                "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y aprender de la retroalimentación recibida.",
            ],
        ],
    ],    

    "Habilidades sociales y emocionales" => [
        "Habilidades socioemocionales"=>
        [
            "Social" =>  [
                "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar la diversidad cultural presente en la institución.",
                "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y respetar las diferencias de género en la institución.",
                "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar el esfuerzo y logros de sus compañeros.",                
                "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar la diversidad de habilidades y talentos presentes en la institución.",
            ],
            "Emocional" =>  [
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar las críticas constructivas de manera adecuada.",
            ],
        ],
        "Habilidades de afrontamiento"=>
        [
            "Social" =>  [
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar el uso responsable de la tecnología en el entorno escolar.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar el estrés y la ansiedad relacionados con el rendimiento académico.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar los conflictos de manera pacífica y constructiva.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar la presión social y los desafíos de la adolescencia.",
                
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar los desafíos de la transición entre etapas educativas.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar las distracciones y mantener el enfoque en clase.",
            ],
            "Emocional" =>  [
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar los cambios emocionales y las dificultades personales.",
                "Se ha observado que el estudiante podría mejorar su capacidad para manejar la presión académica y las altas expectativas.",
            ],
        ]
    ],
];

