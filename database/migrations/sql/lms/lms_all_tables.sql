-- ============================================================================
-- LMS (Learning Management System) — Esquema Completo
-- Generado a partir de las migraciones Laravel 10.
--
-- Tablas (15):
--   1. lms_media_library
--   2. lms_activity_publications
--   3. lms_activity_sections
--   4. lms_activity_contents
--   5. lms_activity_resources
--   6. lms_activity_links
--   7. lms_activity_logs
--   8. lms_activity_attendances
--   9. lms_activity_progress
--  10. lms_content_progress
--  11. lms_activity_assessments
--  12. lms_assessment_questions
--  13. lms_question_options
--  14. lms_assessment_attempts
--  15. lms_attempt_answers
--
-- Orden de creación respeta dependencias de FK.
-- Orden de borrado es inverso.
-- ============================================================================

-- ============================================================================
-- DROP (orden inverso)
-- ============================================================================

DROP TABLE IF EXISTS `lms_attempt_answers`;
DROP TABLE IF EXISTS `lms_assessment_attempts`;
DROP TABLE IF EXISTS `lms_question_options`;
DROP TABLE IF EXISTS `lms_assessment_questions`;
DROP TABLE IF EXISTS `lms_activity_assessments`;
DROP TABLE IF EXISTS `lms_content_progress`;
DROP TABLE IF EXISTS `lms_activity_progress`;
DROP TABLE IF EXISTS `lms_activity_attendances`;
DROP TABLE IF EXISTS `lms_activity_logs`;
DROP TABLE IF EXISTS `lms_activity_links`;
DROP TABLE IF EXISTS `lms_activity_resources`;
DROP TABLE IF EXISTS `lms_activity_contents`;
DROP TABLE IF EXISTS `lms_activity_sections`;
DROP TABLE IF EXISTS `lms_activity_publications`;
DROP TABLE IF EXISTS `lms_media_library`;

-- ============================================================================
-- 1. lms_media_library
-- Repositorio central de archivos multimedia (imágenes, PDFs, vídeos, etc.).
-- ============================================================================

CREATE TABLE `lms_media_library` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uploaded_by`     INT UNSIGNED NOT NULL,
    `disk`            VARCHAR(50) NOT NULL DEFAULT 'public',
    `path`            VARCHAR(1000) NOT NULL,
    `original_name`   VARCHAR(255) NOT NULL,
    `mime_type`       VARCHAR(100) NOT NULL,
    `size_bytes`      BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `duration_secs`   INT UNSIGNED DEFAULT NULL,
    `thumbnail_path`  VARCHAR(1000) DEFAULT NULL,
    `provider`        ENUM('LOCAL','YOUTUBE','VIMEO','DRIVE','DROPBOX') NOT NULL DEFAULT 'LOCAL',
    `external_url`    VARCHAR(1000) DEFAULT NULL,
    `metadata`        JSON DEFAULT NULL,
    `deleted_at`      TIMESTAMP NULL DEFAULT NULL,
    `created_at`      TIMESTAMP NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_media_library_uploaded_by_foreign` (`uploaded_by`),
    CONSTRAINT `lms_media_library_uploaded_by_foreign`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. lms_activity_publications
-- Estado de publicación de cada actividad (borrador, programado, publicado…).
-- ============================================================================

CREATE TABLE `lms_activity_publications` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`     BIGINT UNSIGNED NOT NULL,
    `published_by`    INT UNSIGNED NOT NULL,
    `status`          ENUM('DRAFT','SCHEDULED','PUBLISHED','ARCHIVED') NOT NULL DEFAULT 'DRAFT',
    `publish_at`      DATETIME DEFAULT NULL,
    `unpublish_at`    DATETIME DEFAULT NULL,
    `published_at`    DATETIME DEFAULT NULL,
    `allow_comments`  TINYINT(1) NOT NULL DEFAULT 1,
    `allow_downloads` TINYINT(1) NOT NULL DEFAULT 1,
    `notes`           TEXT DEFAULT NULL,
    `created_at`      TIMESTAMP NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_activity_publications_activity_id_unique` (`activity_id`),
    KEY `lms_activity_publications_published_by_foreign` (`published_by`),
    CONSTRAINT `lms_activity_publications_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_publications_published_by_foreign`
        FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. lms_activity_sections
-- Secciones que estructuran el contenido de una lección.
-- ============================================================================

CREATE TABLE `lms_activity_sections` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id` BIGINT UNSIGNED NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `is_visible`  TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_activity_sections_activity_id_sort_order_index` (`activity_id`, `sort_order`),
    CONSTRAINT `lms_activity_sections_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. lms_activity_contents
-- Bloques de contenido (texto, vídeo, imagen…) dentro de cada sección.
-- ============================================================================

CREATE TABLE `lms_activity_contents` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_id` BIGINT UNSIGNED NOT NULL,
    `type`       ENUM('TEXT','VIDEO','AUDIO','IMAGE','PRESENTATION','HTML','EMBED','FILE_PREVIEW') NOT NULL DEFAULT 'TEXT',
    `title`      VARCHAR(255) DEFAULT NULL,
    `body`       LONGTEXT DEFAULT NULL,
    `media_id`   BIGINT UNSIGNED DEFAULT NULL,
    `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_activity_contents_section_id_sort_order_index` (`section_id`, `sort_order`),
    KEY `lms_activity_contents_media_id_foreign` (`media_id`),
    CONSTRAINT `lms_activity_contents_section_id_foreign`
        FOREIGN KEY (`section_id`) REFERENCES `lms_activity_sections` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_contents_media_id_foreign`
        FOREIGN KEY (`media_id`) REFERENCES `lms_media_library` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. lms_activity_resources
-- Archivos descargables asociados a una actividad (PDFs, hojas de cálculo…).
-- ============================================================================

CREATE TABLE `lms_activity_resources` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`    BIGINT UNSIGNED NOT NULL,
    `media_id`       BIGINT UNSIGNED NOT NULL,
    `uploaded_by`    INT UNSIGNED NOT NULL,
    `display_name`   VARCHAR(255) NOT NULL,
    `description`    TEXT DEFAULT NULL,
    `sort_order`     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `is_visible`     TINYINT(1) NOT NULL DEFAULT 1,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`     TIMESTAMP NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_activity_resources_activity_id_is_visible_index` (`activity_id`, `is_visible`),
    KEY `lms_activity_resources_media_id_foreign` (`media_id`),
    KEY `lms_activity_resources_uploaded_by_foreign` (`uploaded_by`),
    CONSTRAINT `lms_activity_resources_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_resources_media_id_foreign`
        FOREIGN KEY (`media_id`) REFERENCES `lms_media_library` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `lms_activity_resources_uploaded_by_foreign`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. lms_activity_links
-- Enlaces externos (referencias, vídeos, documentos online…).
-- ============================================================================

CREATE TABLE `lms_activity_links` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id` BIGINT UNSIGNED NOT NULL,
    `added_by`    INT UNSIGNED NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `url`         VARCHAR(1000) NOT NULL,
    `link_type`   ENUM('REFERENCE','VIDEO','TOOL','DOCUMENT','OTHER') NOT NULL DEFAULT 'REFERENCE',
    `description` TEXT DEFAULT NULL,
    `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `is_visible`  TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_activity_links_activity_id_foreign` (`activity_id`),
    KEY `lms_activity_links_added_by_foreign` (`added_by`),
    CONSTRAINT `lms_activity_links_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_links_added_by_foreign`
        FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. lms_activity_logs
-- Auditoría de eventos sobre actividades (vistas, descargas, publicación…).
-- ============================================================================

CREATE TABLE `lms_activity_logs` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`  BIGINT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `event`        ENUM(
        'VIEW','CONTENT_VIEW','RESOURCE_DOWNLOAD',
        'PUBLISH','UNPUBLISH','EDIT','SECTION_ADD',
        'RESOURCE_ADD','RESOURCE_DELETE'
    ) NOT NULL,
    `context_id`    BIGINT UNSIGNED DEFAULT NULL,
    `context_type`  VARCHAR(80) DEFAULT NULL,
    `ip_address`    VARCHAR(45) DEFAULT NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `lms_activity_logs_activity_id_event_created_at_index` (`activity_id`, `event`, `created_at`),
    KEY `lms_activity_logs_user_id_created_at_index` (`user_id`, `created_at`),
    CONSTRAINT `lms_activity_logs_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `lms_activity_logs_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. lms_activity_attendances
-- Asistencia de estudiantes a actividades presenciales/remotas.
-- ============================================================================

CREATE TABLE `lms_activity_attendances` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`   BIGINT UNSIGNED NOT NULL,
    `student_id`    INT UNSIGNED NOT NULL,
    `recorded_by`   INT UNSIGNED NOT NULL,
    `status`        ENUM('PRESENT','LATE','ABSENT','EXCUSED','REMOTE') NOT NULL DEFAULT 'ABSENT',
    `observation`   TEXT DEFAULT NULL,
    `checked_in_at` DATETIME DEFAULT NULL,
    `created_at`    TIMESTAMP NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_activity_attendances_activity_id_student_id_unique` (`activity_id`, `student_id`),
    KEY `lms_activity_attendances_activity_id_status_index` (`activity_id`, `status`),
    KEY `lms_activity_attendances_student_id_foreign` (`student_id`),
    KEY `lms_activity_attendances_recorded_by_foreign` (`recorded_by`),
    CONSTRAINT `lms_activity_attendances_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `lms_activity_attendances_student_id_foreign`
        FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `lms_activity_attendances_recorded_by_foreign`
        FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. lms_activity_progress
-- Progreso global de cada estudiante en una actividad.
-- ============================================================================

CREATE TABLE `lms_activity_progress` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`      BIGINT UNSIGNED NOT NULL,
    `student_id`       INT UNSIGNED NOT NULL,
    `status`           ENUM('NOT_STARTED','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'NOT_STARTED',
    `completion_pct`   DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `time_spent_secs`  INT UNSIGNED NOT NULL DEFAULT 0,
    `first_access_at`  DATETIME DEFAULT NULL,
    `last_access_at`   DATETIME DEFAULT NULL,
    `completed_at`     DATETIME DEFAULT NULL,
    `created_at`       TIMESTAMP NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_activity_progress_activity_id_student_id_unique` (`activity_id`, `student_id`),
    KEY `lms_activity_progress_student_id_foreign` (`student_id`),
    CONSTRAINT `lms_activity_progress_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_progress_student_id_foreign`
        FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. lms_content_progress
-- Progreso a nivel de bloque de contenido individual.
-- ============================================================================

CREATE TABLE `lms_content_progress` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `content_id`      BIGINT UNSIGNED NOT NULL,
    `student_id`      INT UNSIGNED NOT NULL,
    `viewed`          TINYINT(1) NOT NULL DEFAULT 0,
    `viewed_at`       DATETIME DEFAULT NULL,
    `time_spent_secs` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_content_progress_content_id_student_id_unique` (`content_id`, `student_id`),
    KEY `lms_content_progress_student_id_foreign` (`student_id`),
    CONSTRAINT `lms_content_progress_content_id_foreign`
        FOREIGN KEY (`content_id`) REFERENCES `lms_activity_contents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_content_progress_student_id_foreign`
        FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. lms_activity_assessments
-- Evaluaciones (cuestionarios, exámenes, prácticas…) asociadas a una actividad.
-- ============================================================================

CREATE TABLE `lms_activity_assessments` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`     BIGINT UNSIGNED NOT NULL,
    `created_by`      INT UNSIGNED NOT NULL,
    `title`           VARCHAR(255) NOT NULL,
    `description`     TEXT DEFAULT NULL,
    `assessment_type` ENUM('QUIZ','EXAM','PRACTICE','SURVEY') NOT NULL DEFAULT 'QUIZ',
    `max_score`       DECIMAL(8,2) NOT NULL DEFAULT 100.00,
    `passing_score`   DECIMAL(8,2) DEFAULT NULL,
    `time_limit_min`  SMALLINT UNSIGNED DEFAULT NULL,
    `attempts_max`    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `randomize`       TINYINT(1) NOT NULL DEFAULT 0,
    `show_results`    TINYINT(1) NOT NULL DEFAULT 1,
    `available_from`  DATETIME DEFAULT NULL,
    `available_until` DATETIME DEFAULT NULL,
    `status`          ENUM('DRAFT','PUBLISHED','CLOSED') NOT NULL DEFAULT 'DRAFT',
    `deleted_at`      TIMESTAMP NULL DEFAULT NULL,
    `created_at`      TIMESTAMP NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_activity_assessments_activity_id_status_deleted_at_index` (`activity_id`, `status`, `deleted_at`),
    KEY `lms_activity_assessments_created_by_foreign` (`created_by`),
    CONSTRAINT `lms_activity_assessments_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_activity_assessments_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. lms_assessment_questions
-- Preguntas que componen una evaluación.
-- ============================================================================

CREATE TABLE `lms_assessment_questions` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `assessment_id` BIGINT UNSIGNED NOT NULL,
    `type`          ENUM('MULTIPLE_CHOICE','MULTIPLE_SELECT','TRUE_FALSE','SHORT_ANSWER','LONG_ANSWER') NOT NULL DEFAULT 'MULTIPLE_CHOICE',
    `content`       TEXT NOT NULL,
    `media_id`      BIGINT UNSIGNED DEFAULT NULL,
    `points`        DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `explanation`   TEXT DEFAULT NULL,
    `created_at`    TIMESTAMP NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_assessment_questions_assessment_id_sort_order_index` (`assessment_id`, `sort_order`),
    KEY `lms_assessment_questions_media_id_foreign` (`media_id`),
    CONSTRAINT `lms_assessment_questions_assessment_id_foreign`
        FOREIGN KEY (`assessment_id`) REFERENCES `lms_activity_assessments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_assessment_questions_media_id_foreign`
        FOREIGN KEY (`media_id`) REFERENCES `lms_media_library` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. lms_question_options
-- Opciones de respuesta para preguntas de selección.
-- ============================================================================

CREATE TABLE `lms_question_options` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `content`    TEXT NOT NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `feedback`   TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lms_question_options_question_id_sort_order_index` (`question_id`, `sort_order`),
    CONSTRAINT `lms_question_options_question_id_foreign`
        FOREIGN KEY (`question_id`) REFERENCES `lms_assessment_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. lms_assessment_attempts
-- Intentos de un estudiante en una evaluación.
-- ============================================================================

CREATE TABLE `lms_assessment_attempts` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `assessment_id`   BIGINT UNSIGNED NOT NULL,
    `student_id`      INT UNSIGNED NOT NULL,
    `attempt_number`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `score`           DECIMAL(8,2) DEFAULT NULL,
    `status`          ENUM('IN_PROGRESS','SUBMITTED','GRADED') NOT NULL DEFAULT 'IN_PROGRESS',
    `started_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `submitted_at`    DATETIME DEFAULT NULL,
    `graded_at`       DATETIME DEFAULT NULL,
    `time_spent_secs` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_att_attempts_uniq` (`assessment_id`, `student_id`, `attempt_number`),
    KEY `lms_assessment_attempts_student_id_status_index` (`student_id`, `status`),
    KEY `lms_assessment_attempts_assessment_id_status_index` (`assessment_id`, `status`),
    KEY `lms_assessment_attempts_student_id_foreign` (`student_id`),
    CONSTRAINT `lms_assessment_attempts_assessment_id_foreign`
        FOREIGN KEY (`assessment_id`) REFERENCES `lms_activity_assessments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_assessment_attempts_student_id_foreign`
        FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. lms_attempt_answers
-- Respuestas individuales de un estudiante en un intento.
-- ============================================================================

CREATE TABLE `lms_attempt_answers` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attempt_id`      BIGINT UNSIGNED NOT NULL,
    `question_id`     BIGINT UNSIGNED NOT NULL,
    `selected_ids`    JSON DEFAULT NULL,
    `text_answer`     TEXT DEFAULT NULL,
    `points_awarded`  DECIMAL(6,2) DEFAULT NULL,
    `is_correct`      TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lms_ans_attempt_q_uniq` (`attempt_id`, `question_id`),
    KEY `lms_attempt_answers_attempt_id_index` (`attempt_id`),
    KEY `lms_attempt_answers_question_id_foreign` (`question_id`),
    CONSTRAINT `lms_attempt_answers_attempt_id_foreign`
        FOREIGN KEY (`attempt_id`) REFERENCES `lms_assessment_attempts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `lms_attempt_answers_question_id_foreign`
        FOREIGN KEY (`question_id`) REFERENCES `lms_assessment_questions` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
