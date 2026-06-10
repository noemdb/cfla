-- =============================================================================
-- SAEFL — MÓDULO LMS: PUBLICACIÓN Y ORGANIZACIÓN DE CONTENIDO
-- Versión: 2.0 — Adaptado a estructura real de SAEFL
-- Motor: MariaDB / MySQL 8 — Laravel 8 compatible
-- Compatible con: pevaluacions → activities (jerarquía existente)
-- =============================================================================
--
-- JERARQUÍA COMPLETA:
--
--   pensum (asignatura)
--       └── pevaluacions (plan de evaluación / unidad didáctica)
--               └── activities (clase / actividad pedagógica)  ← PIVOT CENTRAL
--                       │
--                       ├── activity_sections          (estructura del contenido)
--                       │       └── activity_contents  (bloques de contenido)
--                       │               └── activity_content_media  (media adjunta al bloque)
--                       │
--                       ├── activity_resources         (archivos descargables)
--                       ├── activity_links             (enlaces externos / embeds)
--                       │
--                       ├── activity_attendances       (registro de asistencia)
--                       ├── activity_progress          (seguimiento LMS por alumno)
--                       │
--                       ├── activity_assignments       (tareas)
--                       │       └── assignment_submissions
--                       │
--                       └── activity_assessments       (evaluaciones digitales)
--                               ├── assessment_questions
--                               │       └── question_options
--                               └── assessment_attempts
--                                       └── attempt_answers
--
-- TABLAS DE SOPORTE (transversales):
--   media_library     — catálogo centralizado de archivos subidos
--   activity_comments — foro/preguntas por actividad
--   activity_logs     — auditoría de accesos (directivos)
--
-- DECISIONES DE DISEÑO:
--   1. NO se crea tabla `lesson`. activities ES la lección.
--   2. activity_sections permite organizar el contenido en partes
--      (ej: "Introducción", "Desarrollo", "Cierre") sin romper activities.
--   3. media_library centraliza archivos: recursos y contenidos multimedia
--      apuntan al catálogo, no duplican rutas.
--   4. Separación entre activity_resources (descargables) y
--      activity_contents (contenido inline/estructurado).
--   5. Todos los soft deletes están en las tablas que el docente
--      gestiona (assignments, assessments). Las transaccionales no.
--   6. activity_logs cubre el requerimiento de supervisión de directivos.
-- =============================================================================


-- =============================================================================
-- BLOQUE 0: CATÁLOGO CENTRALIZADO DE ARCHIVOS
-- Evita duplicar file_path en cada tabla. Reutilizable en toda la app.
-- =============================================================================

CREATE TABLE media_library (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    uploaded_by     BIGINT UNSIGNED     NOT NULL,           -- FK → users.id
    disk            VARCHAR(50)         NOT NULL DEFAULT 'public',
    path            VARCHAR(1000)       NOT NULL,           -- Ruta relativa en storage
    original_name   VARCHAR(255)        NOT NULL,
    mime_type       VARCHAR(100)        NOT NULL,
    size_bytes      BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    duration_secs   INT UNSIGNED        NULL,               -- Para video/audio
    thumbnail_path  VARCHAR(1000)       NULL,
    provider        ENUM(
                        'LOCAL',
                        'YOUTUBE',
                        'VIMEO',
                        'DRIVE',
                        'DROPBOX'
                    )                   NOT NULL DEFAULT 'LOCAL',
    external_url    VARCHAR(1000)       NULL,               -- Si provider != LOCAL
    metadata        JSON                NULL,               -- Resolución, bitrate, etc.
    deleted_at      TIMESTAMP           NULL,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_media_library         PRIMARY KEY (id),
    CONSTRAINT fk_ml_uploaded_by        FOREIGN KEY (uploaded_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    -- Un archivo LOCAL no puede tener external_url y viceversa
    CONSTRAINT chk_ml_source            CHECK (
        (provider = 'LOCAL' AND external_url IS NULL)
        OR (provider != 'LOCAL' AND external_url IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo centralizado de archivos y medios del sistema';

CREATE INDEX idx_ml_uploader    ON media_library (uploaded_by, deleted_at);
CREATE INDEX idx_ml_mime        ON media_library (mime_type);
CREATE INDEX idx_ml_provider    ON media_library (provider);


-- =============================================================================
-- BLOQUE 1: ESTRUCTURA DEL CONTENIDO DE UNA ACTIVIDAD
-- Permite organizar el material en secciones ordenadas con bloques de contenido.
-- Esto es lo que el alumno "lee" al abrir la actividad.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- activity_sections: Partes o secciones dentro de una actividad/clase.
-- Ej: "1. Introducción", "2. Desarrollo", "3. Actividad práctica", "4. Cierre"
-- Si no se necesitan secciones, se usa una sola con sort_order=1.
-- Relación: activities (1) → activity_sections (N)
-- -----------------------------------------------------------------------------
CREATE TABLE activity_sections (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    title           VARCHAR(255)        NOT NULL,
    description     TEXT                NULL,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    is_visible      TINYINT(1)          NOT NULL DEFAULT 1,  -- Control de publicación
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_act_sections          PRIMARY KEY (id),
    CONSTRAINT fk_as_activity           FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Secciones ordenadas dentro de una actividad/clase';

CREATE INDEX idx_as_activity_order  ON activity_sections (activity_id, sort_order);


-- -----------------------------------------------------------------------------
-- activity_contents: Bloques de contenido dentro de una sección.
-- Cada bloque es atómico: un texto, un video embed, un HTML, etc.
-- El alumno los recorre en orden (modelo de "página de lección" LMS).
-- Relación: activity_sections (1) → activity_contents (N)
-- -----------------------------------------------------------------------------
CREATE TABLE activity_contents (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    section_id      BIGINT UNSIGNED     NOT NULL,
    type            ENUM(
                        'TEXT',         -- Texto enriquecido (HTML/Markdown)
                        'VIDEO',        -- Video embed o LOCAL via media_library
                        'AUDIO',        -- Audio embed o LOCAL
                        'IMAGE',        -- Imagen standalone
                        'PRESENTATION', -- PPT/PDF incrustado
                        'HTML',         -- HTML raw (para interactivos)
                        'EMBED',        -- iFrame embed (Padlet, GeoGebra, etc.)
                        'FILE_PREVIEW'  -- Vista previa de archivo (PDF viewer)
                    )                   NOT NULL DEFAULT 'TEXT',
    title           VARCHAR(255)        NULL,
    body            LONGTEXT            NULL,               -- Contenido TEXT/HTML
    media_id        BIGINT UNSIGNED     NULL,               -- FK → media_library
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    is_required     TINYINT(1)          NOT NULL DEFAULT 0, -- Bloque obligatorio para progreso
    is_visible      TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_act_contents          PRIMARY KEY (id),
    CONSTRAINT fk_ac_section            FOREIGN KEY (section_id)
                                            REFERENCES activity_sections(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_ac_media              FOREIGN KEY (media_id)
                                            REFERENCES media_library(id)
                                            ON DELETE SET NULL,
    CONSTRAINT chk_ac_body_or_media     CHECK (body IS NOT NULL OR media_id IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bloques atómicos de contenido dentro de una sección';

CREATE INDEX idx_ac_section_order   ON activity_contents (section_id, sort_order);
CREATE INDEX idx_ac_type            ON activity_contents (type);


-- =============================================================================
-- BLOQUE 2: RECURSOS DESCARGABLES Y ENLACES
-- Separados del contenido inline: son archivos que el alumno puede bajar
-- o enlaces externos que complementan la actividad.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- activity_resources: Archivos adjuntos descargables de una actividad.
-- Ej: Guía de práctica, hoja de trabajo, presentación para imprimir.
-- Relación: activities (1) → activity_resources (N)
-- -----------------------------------------------------------------------------
CREATE TABLE activity_resources (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    media_id        BIGINT UNSIGNED     NOT NULL,           -- FK → media_library
    uploaded_by     BIGINT UNSIGNED     NOT NULL,
    display_name    VARCHAR(255)        NOT NULL,           -- Nombre visible al alumno
    description     TEXT                NULL,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    is_visible      TINYINT(1)          NOT NULL DEFAULT 1,
    download_count  INT UNSIGNED        NOT NULL DEFAULT 0, -- Métricas básicas
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_act_resources         PRIMARY KEY (id),
    CONSTRAINT fk_ar_activity           FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_ar_media              FOREIGN KEY (media_id)
                                            REFERENCES media_library(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_ar_uploaded_by        FOREIGN KEY (uploaded_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Recursos descargables adjuntos a una actividad';

CREATE INDEX idx_ar_activity_visible ON activity_resources (activity_id, is_visible);


-- -----------------------------------------------------------------------------
-- activity_links: Enlaces externos y videos embebidos asociados a la actividad.
-- A diferencia de activity_contents, estos son referencias externas directas
-- visibles como lista, no como bloques de lectura.
-- Relación: activities (1) → activity_links (N)
-- -----------------------------------------------------------------------------
CREATE TABLE activity_links (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    added_by        BIGINT UNSIGNED     NOT NULL,
    title           VARCHAR(255)        NOT NULL,
    url             VARCHAR(1000)       NOT NULL,
    link_type       ENUM(
                        'REFERENCE',    -- Bibliografía / artículo web
                        'VIDEO',        -- YouTube, Vimeo
                        'TOOL',         -- Herramienta online (GeoGebra, Padlet)
                        'DOCUMENT',     -- Google Docs / Drive compartido
                        'OTHER'
                    )                   NOT NULL DEFAULT 'REFERENCE',
    description     TEXT                NULL,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    is_visible      TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_act_links             PRIMARY KEY (id),
    CONSTRAINT fk_al_activity           FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_al_added_by           FOREIGN KEY (added_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Enlaces externos y videos asociados a una actividad';

CREATE INDEX idx_al_activity_visible ON activity_links (activity_id, is_visible);


-- =============================================================================
-- BLOQUE 3: PUBLICACIÓN Y VISIBILIDAD
-- Controla cuándo y quién puede ver cada actividad.
-- Permite publicación programada (el docente prepara con antelación).
-- =============================================================================

-- -----------------------------------------------------------------------------
-- activity_publications: Estado de publicación de una actividad para los alumnos.
-- Desacopla el estado interno (activities.status = aprobación directiva)
-- del estado de visibilidad para estudiantes.
-- Relación: activities (1:1) → activity_publications
-- -----------------------------------------------------------------------------
CREATE TABLE activity_publications (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id         BIGINT UNSIGNED     NOT NULL,
    published_by        BIGINT UNSIGNED     NOT NULL,
    status              ENUM(
                            'DRAFT',        -- Solo visible para el docente
                            'SCHEDULED',    -- Se publicará en publish_at
                            'PUBLISHED',    -- Visible para estudiantes
                            'ARCHIVED'      -- Cerrada, no editable, aún visible
                        )                   NOT NULL DEFAULT 'DRAFT',
    publish_at          DATETIME            NULL,           -- Publicación programada
    unpublish_at        DATETIME            NULL,           -- Cierre automático
    published_at        DATETIME            NULL,           -- Fecha/hora real de publicación
    allow_comments      TINYINT(1)          NOT NULL DEFAULT 1,
    allow_downloads     TINYINT(1)          NOT NULL DEFAULT 1,
    notes               TEXT                NULL,           -- Nota interna del docente
    created_at          TIMESTAMP           NULL,
    updated_at          TIMESTAMP           NULL,

    CONSTRAINT pk_act_pub               PRIMARY KEY (id),
    CONSTRAINT uq_act_pub_activity      UNIQUE (activity_id),  -- 1:1 con activity
    CONSTRAINT fk_ap_activity           FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_ap_published_by       FOREIGN KEY (published_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT chk_ap_dates             CHECK (
        unpublish_at IS NULL
        OR publish_at IS NULL
        OR unpublish_at > publish_at
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Control de publicación y visibilidad de actividades para estudiantes';

CREATE INDEX idx_apub_status        ON activity_publications (status, publish_at);


-- =============================================================================
-- BLOQUE 4: ASISTENCIA
-- =============================================================================

CREATE TABLE activity_attendances (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id         BIGINT UNSIGNED     NOT NULL,
    student_id          BIGINT UNSIGNED     NOT NULL,
    recorded_by         BIGINT UNSIGNED     NOT NULL,
    status              ENUM(
                            'PRESENT',
                            'LATE',
                            'ABSENT',
                            'EXCUSED',
                            'REMOTE'        -- Conexión sincrónica remota
                        )                   NOT NULL DEFAULT 'ABSENT',
    observation         TEXT                NULL,
    checked_in_at       DATETIME            NULL,           -- Hora exacta de entrada
    created_at          TIMESTAMP           NULL,
    updated_at          TIMESTAMP           NULL,

    CONSTRAINT pk_act_att               PRIMARY KEY (id),
    CONSTRAINT uq_att_activity_student  UNIQUE (activity_id, student_id),
    CONSTRAINT fk_att_activity          FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_att_student           FOREIGN KEY (student_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_att_recorded_by       FOREIGN KEY (recorded_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de asistencia por alumno y actividad';

CREATE INDEX idx_att_student_status ON activity_attendances (student_id, status);
CREATE INDEX idx_att_activity       ON activity_attendances (activity_id, status);


-- =============================================================================
-- BLOQUE 5: PROGRESO LMS DEL ESTUDIANTE
-- Seguimiento de consumo de contenido. Se actualiza desde el frontend
-- cada vez que el alumno marca un bloque como visto o completa la actividad.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- activity_progress: Estado global de progreso en una actividad completa.
-- -----------------------------------------------------------------------------
CREATE TABLE activity_progress (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id         BIGINT UNSIGNED     NOT NULL,
    student_id          BIGINT UNSIGNED     NOT NULL,
    status              ENUM(
                            'NOT_STARTED',
                            'IN_PROGRESS',
                            'COMPLETED'
                        )                   NOT NULL DEFAULT 'NOT_STARTED',
    completion_pct      DECIMAL(5,2)        NOT NULL DEFAULT 0.00,
    time_spent_secs     INT UNSIGNED        NOT NULL DEFAULT 0,
    first_access_at     DATETIME            NULL,
    last_access_at      DATETIME            NULL,
    completed_at        DATETIME            NULL,
    created_at          TIMESTAMP           NULL,
    updated_at          TIMESTAMP           NULL,

    CONSTRAINT pk_act_progress          PRIMARY KEY (id),
    CONSTRAINT uq_prog_activity_student UNIQUE (activity_id, student_id),
    CONSTRAINT fk_prog_activity         FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_prog_student          FOREIGN KEY (student_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT chk_prog_pct             CHECK (completion_pct >= 0 AND completion_pct <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Progreso global del estudiante en una actividad';

CREATE INDEX idx_prog_student_status ON activity_progress (student_id, status);
CREATE INDEX idx_prog_activity       ON activity_progress (activity_id, status);


-- -----------------------------------------------------------------------------
-- content_progress: Progreso por bloque individual de contenido.
-- Permite calcular completion_pct en activity_progress con precisión.
-- Relación: activity_contents (1) → content_progress (N) [por alumno]
-- -----------------------------------------------------------------------------
CREATE TABLE content_progress (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    content_id      BIGINT UNSIGNED     NOT NULL,
    student_id      BIGINT UNSIGNED     NOT NULL,
    viewed          TINYINT(1)          NOT NULL DEFAULT 0,
    viewed_at       DATETIME            NULL,
    time_spent_secs INT UNSIGNED        NOT NULL DEFAULT 0,

    CONSTRAINT pk_content_progress      PRIMARY KEY (id),
    CONSTRAINT uq_cp_content_student    UNIQUE (content_id, student_id),
    CONSTRAINT fk_cp_content            FOREIGN KEY (content_id)
                                            REFERENCES activity_contents(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_cp_student            FOREIGN KEY (student_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Progreso por bloque de contenido individual';

CREATE INDEX idx_cp_student     ON content_progress (student_id);
CREATE INDEX idx_cp_content     ON content_progress (content_id, viewed);


-- =============================================================================
-- BLOQUE 6: TAREAS (ASSIGNMENTS)
-- =============================================================================

CREATE TABLE activity_assignments (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    created_by      BIGINT UNSIGNED     NOT NULL,
    title           VARCHAR(255)        NOT NULL,
    instructions    LONGTEXT            NULL,
    due_date        DATETIME            NULL,
    max_score       DECIMAL(8,2)        NOT NULL DEFAULT 100.00,
    allow_file      TINYINT(1)          NOT NULL DEFAULT 1,   -- Permite adjuntar archivo
    allow_text      TINYINT(1)          NOT NULL DEFAULT 1,   -- Permite respuesta de texto
    attempts_max    TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    status          ENUM(
                        'DRAFT',
                        'PUBLISHED',
                        'CLOSED'
                    )                   NOT NULL DEFAULT 'DRAFT',
    deleted_at      TIMESTAMP           NULL,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_assignments           PRIMARY KEY (id),
    CONSTRAINT fk_assgn_activity        FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_assgn_created_by      FOREIGN KEY (created_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT chk_assgn_max_score      CHECK (max_score > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tareas derivadas de una actividad';

CREATE INDEX idx_assgn_activity_status  ON activity_assignments (activity_id, status, deleted_at);


CREATE TABLE assignment_submissions (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    assignment_id   BIGINT UNSIGNED     NOT NULL,
    student_id      BIGINT UNSIGNED     NOT NULL,
    attempt_number  TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    text_response   LONGTEXT            NULL,
    media_id        BIGINT UNSIGNED     NULL,               -- Archivo entregado
    score           DECIMAL(8,2)        NULL,
    feedback        LONGTEXT            NULL,
    status          ENUM(
                        'DRAFT',        -- Guardado pero no enviado
                        'SUBMITTED',
                        'GRADED',
                        'RETURNED'      -- Devuelto para rehacer
                    )                   NOT NULL DEFAULT 'DRAFT',
    submitted_at    DATETIME            NULL,
    graded_at       DATETIME            NULL,
    graded_by       BIGINT UNSIGNED     NULL,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_submissions           PRIMARY KEY (id),
    CONSTRAINT uq_sub_attempt           UNIQUE (assignment_id, student_id, attempt_number),
    CONSTRAINT fk_sub_assignment        FOREIGN KEY (assignment_id)
                                            REFERENCES activity_assignments(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_sub_student           FOREIGN KEY (student_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_sub_media             FOREIGN KEY (media_id)
                                            REFERENCES media_library(id)
                                            ON DELETE SET NULL,
    CONSTRAINT fk_sub_graded_by         FOREIGN KEY (graded_by)
                                            REFERENCES users(id)
                                            ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Entregas de estudiantes para tareas';

CREATE INDEX idx_sub_student_status ON assignment_submissions (student_id, status);
CREATE INDEX idx_sub_assignment     ON assignment_submissions (assignment_id, status);


-- =============================================================================
-- BLOQUE 7: EVALUACIONES DIGITALES
-- =============================================================================

CREATE TABLE activity_assessments (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    created_by      BIGINT UNSIGNED     NOT NULL,
    title           VARCHAR(255)        NOT NULL,
    description     TEXT                NULL,
    assessment_type ENUM(
                        'QUIZ',
                        'EXAM',
                        'PRACTICE',
                        'SURVEY'        -- Encuesta sin calificación
                    )                   NOT NULL DEFAULT 'QUIZ',
    max_score       DECIMAL(8,2)        NOT NULL DEFAULT 100.00,
    passing_score   DECIMAL(8,2)        NULL,
    time_limit_min  SMALLINT UNSIGNED   NULL,               -- NULL = sin límite
    attempts_max    TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    randomize       TINYINT(1)          NOT NULL DEFAULT 0,
    show_results    TINYINT(1)          NOT NULL DEFAULT 1, -- Mostrar resultados al alumno
    available_from  DATETIME            NULL,
    available_until DATETIME            NULL,
    status          ENUM(
                        'DRAFT',
                        'PUBLISHED',
                        'CLOSED'
                    )                   NOT NULL DEFAULT 'DRAFT',
    deleted_at      TIMESTAMP           NULL,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_assessments           PRIMARY KEY (id),
    CONSTRAINT fk_asmnt_activity        FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_asmnt_created_by      FOREIGN KEY (created_by)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT chk_asmnt_scores         CHECK (
        passing_score IS NULL
        OR (passing_score >= 0 AND passing_score <= max_score)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Evaluaciones digitales asociadas a una actividad';

CREATE INDEX idx_asmnt_activity_status  ON activity_assessments (activity_id, status, deleted_at);


CREATE TABLE assessment_questions (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    assessment_id   BIGINT UNSIGNED     NOT NULL,
    type            ENUM(
                        'MULTIPLE_CHOICE',  -- Una opción correcta
                        'MULTIPLE_SELECT',  -- Varias opciones correctas
                        'TRUE_FALSE',
                        'SHORT_ANSWER',
                        'LONG_ANSWER'
                    )                   NOT NULL DEFAULT 'MULTIPLE_CHOICE',
    content         TEXT                NOT NULL,
    media_id        BIGINT UNSIGNED     NULL,               -- Imagen/audio en la pregunta
    points          DECIMAL(6,2)        NOT NULL DEFAULT 1.00,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    explanation     TEXT                NULL,               -- Retroalimentación post-intento
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_questions             PRIMARY KEY (id),
    CONSTRAINT fk_q_assessment          FOREIGN KEY (assessment_id)
                                            REFERENCES activity_assessments(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_q_media               FOREIGN KEY (media_id)
                                            REFERENCES media_library(id)
                                            ON DELETE SET NULL,
    CONSTRAINT chk_q_points             CHECK (points > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_q_assessment_order ON assessment_questions (assessment_id, sort_order);


CREATE TABLE question_options (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    question_id     BIGINT UNSIGNED     NOT NULL,
    content         TEXT                NOT NULL,
    is_correct      TINYINT(1)          NOT NULL DEFAULT 0,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    feedback        TEXT                NULL,

    CONSTRAINT pk_question_options      PRIMARY KEY (id),
    CONSTRAINT fk_qo_question           FOREIGN KEY (question_id)
                                            REFERENCES assessment_questions(id)
                                            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_qo_question    ON question_options (question_id, sort_order);


CREATE TABLE assessment_attempts (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    assessment_id   BIGINT UNSIGNED     NOT NULL,
    student_id      BIGINT UNSIGNED     NOT NULL,
    attempt_number  TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    score           DECIMAL(8,2)        NULL,
    status          ENUM(
                        'IN_PROGRESS',
                        'SUBMITTED',
                        'GRADED'
                    )                   NOT NULL DEFAULT 'IN_PROGRESS',
    started_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    submitted_at    DATETIME            NULL,
    graded_at       DATETIME            NULL,
    time_spent_secs INT UNSIGNED        NULL,

    CONSTRAINT pk_attempts              PRIMARY KEY (id),
    CONSTRAINT uq_attempt_number        UNIQUE (assessment_id, student_id, attempt_number),
    CONSTRAINT fk_att2_assessment       FOREIGN KEY (assessment_id)
                                            REFERENCES activity_assessments(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_att2_student          FOREIGN KEY (student_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_attempts_student   ON assessment_attempts (student_id, status);


CREATE TABLE attempt_answers (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    attempt_id      BIGINT UNSIGNED     NOT NULL,
    question_id     BIGINT UNSIGNED     NOT NULL,
    selected_ids    JSON                NULL,       -- IDs de question_options seleccionadas
    text_answer     TEXT                NULL,       -- Para SHORT/LONG_ANSWER
    points_awarded  DECIMAL(6,2)        NULL,
    is_correct      TINYINT(1)          NULL,       -- NULL = pendiente corrección manual

    CONSTRAINT pk_attempt_answers       PRIMARY KEY (id),
    CONSTRAINT uq_aa_attempt_q          UNIQUE (attempt_id, question_id),
    CONSTRAINT fk_aa_attempt            FOREIGN KEY (attempt_id)
                                            REFERENCES assessment_attempts(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_aa_question           FOREIGN KEY (question_id)
                                            REFERENCES assessment_questions(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_aa_attempt ON attempt_answers (attempt_id);


-- =============================================================================
-- BLOQUE 8: COMENTARIOS / FORO DE ACTIVIDAD
-- Permite a alumnos hacer preguntas y al docente responder.
-- is_instructor_reply diferencia las respuestas del docente visualmente.
-- =============================================================================

CREATE TABLE activity_comments (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    author_id       BIGINT UNSIGNED     NOT NULL,
    parent_id       BIGINT UNSIGNED     NULL,               -- NULL = hilo raíz
    content         TEXT                NOT NULL,
    is_instructor_reply TINYINT(1)      NOT NULL DEFAULT 0,
    is_pinned       TINYINT(1)          NOT NULL DEFAULT 0,
    deleted_at      TIMESTAMP           NULL,
    created_at      TIMESTAMP           NULL,
    updated_at      TIMESTAMP           NULL,

    CONSTRAINT pk_activity_comments     PRIMARY KEY (id),
    CONSTRAINT fk_ac_activity           FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE CASCADE,
    CONSTRAINT fk_ac_author             FOREIGN KEY (author_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_ac_parent             FOREIGN KEY (parent_id)
                                            REFERENCES activity_comments(id)
                                            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Foro de preguntas y comentarios por actividad';

CREATE INDEX idx_actcom_activity    ON activity_comments (activity_id, is_pinned, deleted_at);
CREATE INDEX idx_actcom_author      ON activity_comments (author_id);


-- =============================================================================
-- BLOQUE 9: AUDITORÍA DE ACCESOS (para directivos y coordinadores)
-- Registro inmutable de quién accedió a qué y cuándo.
-- Permite a directivos ver la actividad de docentes y estudiantes.
-- NO tiene ON DELETE CASCADE: es auditoría, no se borra.
-- =============================================================================

CREATE TABLE activity_logs (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    activity_id     BIGINT UNSIGNED     NOT NULL,
    user_id         BIGINT UNSIGNED     NOT NULL,
    event           ENUM(
                        'VIEW',             -- Alumno abrió la actividad
                        'CONTENT_VIEW',     -- Alumno vio un bloque de contenido
                        'RESOURCE_DOWNLOAD',-- Alumno descargó un recurso
                        'ASSIGNMENT_START', -- Alumno inició una tarea
                        'ASSIGNMENT_SUBMIT',-- Alumno entregó una tarea
                        'ASSESSMENT_START', -- Alumno inició una evaluación
                        'ASSESSMENT_SUBMIT',-- Alumno entregó una evaluación
                        'PUBLISH',          -- Docente publicó la actividad
                        'EDIT'              -- Docente editó la actividad
                    )                   NOT NULL,
    context_id      BIGINT UNSIGNED     NULL,               -- ID de la entidad relacionada
    context_type    VARCHAR(80)         NULL,               -- Ej: 'activity_contents'
    ip_address      VARCHAR(45)         NULL,
    user_agent      VARCHAR(500)        NULL,
    created_at      TIMESTAMP           NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_activity_logs         PRIMARY KEY (id),
    CONSTRAINT fk_log_activity          FOREIGN KEY (activity_id)
                                            REFERENCES activities(id)
                                            ON DELETE RESTRICT,
    CONSTRAINT fk_log_user              FOREIGN KEY (user_id)
                                            REFERENCES users(id)
                                            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Auditoría de accesos y eventos por actividad (inmutable)';

CREATE INDEX idx_log_activity_event ON activity_logs (activity_id, event, created_at);
CREATE INDEX idx_log_user_date      ON activity_logs (user_id, created_at);


-- =============================================================================
-- JERARQUÍA FINAL / RESUMEN DE RELACIONES
-- =============================================================================
/*

  EXISTENTE (sin modificar):
  ───────────────────────────
  pensum ──(1:N)──► pevaluacions ──(1:N)──► activities
                                                │
  NUEVAS TABLAS (se añaden sobre activities):   │
  ──────────────────────────────────────────────┘
  │
  ├──(1:1)──► activity_publications       [control de publicación]
  │
  ├──(1:N)──► activity_sections           [estructura del contenido]
  │               └──(1:N)──► activity_contents      [bloques de contenido]
  │                               └──(1:N)──► content_progress  [por alumno]
  │                               └──(N:1)──► media_library
  │
  ├──(1:N)──► activity_resources          [archivos descargables]
  │               └──(N:1)──► media_library
  │
  ├──(1:N)──► activity_links              [enlaces / videos externos]
  │
  ├──(1:N)──► activity_attendances        [asistencia por alumno]
  │
  ├──(1:N)──► activity_progress           [progreso global por alumno]
  │
  ├──(1:N)──► activity_assignments        [tareas]
  │               └──(1:N)──► assignment_submissions  [entregas]
  │                               └──(N:1)──► media_library
  │
  ├──(1:N)──► activity_assessments        [evaluaciones digitales]
  │               ├──(1:N)──► assessment_questions
  │               │               ├──(1:N)──► question_options
  │               │               └──(N:1)──► media_library
  │               └──(1:N)──► assessment_attempts
  │                               └──(1:N)──► attempt_answers
  │
  ├──(1:N)──► activity_comments           [foro de la actividad]
  │
  └──(1:N)──► activity_logs               [auditoría para directivos]

  TABLA TRANSVERSAL:
  media_library ◄──── activity_contents, activity_resources,
                       assignment_submissions, assessment_questions

*/
