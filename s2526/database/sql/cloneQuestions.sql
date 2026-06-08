-- ======================================================
-- CLONACIÓN DE PREGUNTAS HACIA UN DEBATE EXISTENTE
-- MariaDB 10.4.27
-- ======================================================

SET @competition_id    = 1;
SET @source_grado      = 12;
SET @target_debate_id  = 43;
SET @categoria         = '[31059] Formación Humana Cristiana';
SET @fixed_pensum_id   = 223;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------
-- 1. Tabla temporal de mapeo
-- ------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS temp_question_map;
CREATE TEMPORARY TABLE temp_question_map (
    old_id SMALLINT UNSIGNED PRIMARY KEY,
    new_id SMALLINT UNSIGNED NOT NULL
) ENGINE = MEMORY;

-- ------------------------------------------------------
-- 2. Capturar el MAX(id) ANTES del INSERT
--    Para identificar exactamente las filas nuevas
-- ------------------------------------------------------
SET @max_id_before = (
    SELECT COALESCE(MAX(id), 0) FROM debate_questions
);

-- ------------------------------------------------------
-- 3. Clonar las preguntas
-- ------------------------------------------------------
INSERT INTO debate_questions (
    debate_id,
    user_id,
    pensum_id,
    category,
    time,
    time_elapsed,
    text,
    weighting,
    observation,
    option_max,
    status_active,
    status_answer,
    status_under_review,
    attachment,
    context,
    created_at,
    updated_at
)
SELECT
    @target_debate_id,
    dq.user_id,
    @fixed_pensum_id,
    dq.category,
    dq.time,
    dq.time_elapsed,
    dq.text,
    dq.weighting,
    dq.observation,
    dq.option_max,
    dq.status_active,
    dq.status_answer,
    dq.status_under_review,
    dq.attachment,
    dq.context,
    NOW(),
    NOW()
FROM debate_questions dq
INNER JOIN debates d ON d.id = dq.debate_id
WHERE d.competition_id = @competition_id
  AND d.grado_id       = @source_grado
  AND dq.category      = @categoria;

-- ------------------------------------------------------
-- 4. Mapeo exacto usando rango de IDs reales
--    Sin NOW(), sin text matching — 100% determinista
-- ------------------------------------------------------
INSERT INTO temp_question_map (old_id, new_id)
SELECT
    dq_old.id,
    dq_new.id
FROM debate_questions dq_new
INNER JOIN (
    SELECT dq.id, dq.text, dq.category
    FROM debate_questions dq
    INNER JOIN debates d ON d.id = dq.debate_id
    WHERE d.competition_id = @competition_id
      AND d.grado_id       = @source_grado
      AND dq.category      = @categoria
) dq_old ON (
    dq_new.text     = dq_old.text
    AND dq_new.category = dq_old.category
)
WHERE dq_new.id          > @max_id_before       -- Solo filas recién insertadas
  AND dq_new.debate_id   = @target_debate_id     -- Solo el debate destino
  AND dq_new.pensum_id   = @fixed_pensum_id;     -- Seguridad adicional

-- ------------------------------------------------------
-- 5. Clonar las opciones usando el mapeo real
-- ------------------------------------------------------
INSERT INTO debate_options (
    question_id,
    user_id,
    text,
    observation,
    attachment,
    context,
    status_option_correct,
    status_wrong_answer,
    created_at,
    updated_at
)
SELECT
    tqm.new_id,
    dop.user_id,
    dop.text,
    dop.observation,
    dop.attachment,
    dop.context,
    dop.status_option_correct,
    dop.status_wrong_answer,
    NOW(),
    NOW()
FROM debate_options dop
INNER JOIN temp_question_map tqm ON tqm.old_id = dop.question_id;

-- ------------------------------------------------------
-- 6. Limpieza
-- ------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS temp_question_map;
SET FOREIGN_KEY_CHECKS = 1;