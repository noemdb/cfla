# 02 — Modelos y Schema BD · Módulo Inicial (legacy s2526)

> **Fuente de verdad del schema:** `SHOW CREATE TABLE` de la BD viva `s2526` (MariaDB 10.11). Las migraciones backUp (`database/migrations/backUps/inicials/`, 41 archivos) están incompletas respecto a la BD y contienen bugs documentados en §A.9. Verificado también: la BD principal de cfla (`s2627`) ya contiene las 19 tablas clonadas (DDL idéntico, 0 filas).

---

# Parte A — Schema BD (19 tablas)

## A.1 Mapa Modelo → Tabla

Solo `Eilearningarea` y `Eilearningexpectation` declaran `protected $table` explícito; el resto usa convención Laravel (snake_case plural). 18 tablas con modelo + 1 pivote (`eifinalk_expectation`, sin modelo propio — se usa vía `belongsToMany` con `->withPivot('eilearningarea_id','pevaluacion_id')`).

| Modelo | Tabla | Filas (s2526 viva) | Rol |
|---|---|---|---|
| `Eiplanningwk` | `eiplanningwks` | 274 | Cabecera plan semanal |
| `Eiplanningwstrategy` | `eiplanningwstrategies` | 1,630 | Estrategias por día/momento del plan semanal |
| `Eiplanningwsummary` | `eiplanningwsummaries` | 0 | Resumen curricular semanal |
| `Eiplanningbwk` | `eiplanningbwks` | 6 | Cabecera plan quincenal |
| `Eiplanningbwstrategy` | `eiplanningbwstrategies` | 49 | Estrategias quincenales |
| `Eiplanningbwsummary` | `eiplanningbwsummaries` | 16 | Resumen curricular quincenal |
| `Eiprojectk` | `eiprojectks` | 19 | Cabecera proyecto de aula |
| `Eiprojectkstrategy` | `eiprojectkstrategies` | 322 | Estrategias del proyecto |
| `Eiprojectsummary` | `eiprojectsummaries` | 47 | Resúmenes del proyecto |
| `Eiprojectreview` | `eiprojectreviews` | 8 | Revisiones del proyecto |
| `Eispecialk` | `eispecialks` | 3 | Cabecera plan/informe especial |
| `Eispecialstrategy` | `eispecialstrategies` | 0 | Estrategias del plan especial |
| `Eispecialact` | `eispecialacts` | 8 | Actividades del plan especial |
| `Eievaluationk` | `eievaluationks` | 24 | Cabecera plan de evaluación |
| `Eievaluationp` | `eievaluationps` | 98 | Detalle (posiciones) del plan de evaluación |
| `Eifinalk` | `eifinalks` | 0 | Informe pedagógico final por estudiante |
| — *(pivote)* | `eifinalk_expectation` | 0 | Eifinalk ↔ Eilearningexpectation |
| `Eilearningarea` | `eilearningareas` | 0 | Áreas de aprendizaje (currículo inicial) |
| `Eilearningexpectation` | `eilearningexpectations` | 0 | Aprendizajes esperados por área |

> ℹ️ Tablas con contador AUTO_INCREMENT alto pero 0 filas (`eifinalks` AI=190, `eilearningareas` AI=28, `eilearningexpectations` AI=136, `eifinalk_expectation` AI=6, `eiplanningwsummaries` AI=51): **hubo datos que fueron eliminados** de s2526. El seeder `EILearningSeeder` (areas+expectations, 1,021 líneas, grado_id=22 en el seeder) nunca corrió en s2526 o fue vaciado después.

## A.2 DDL — Familia Planificación Semanal

### `eiplanningwks` (cabecera)
```sql
CREATE TABLE `eiplanningwks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` int(10) unsigned NOT NULL,
  `grado_id` int(10) unsigned NOT NULL,
  `seccion_id` int(10) unsigned NOT NULL,
  `eiprojectk_id` int(10) unsigned DEFAULT NULL COMMENT 'Proyecto vinculado',
  `finicial` date NOT NULL,
  `ffinal` date NOT NULL,
  `tiempo_ejecucion` int(10) unsigned NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiplanningwstrategies`
```sql
CREATE TABLE `eiplanningwstrategies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eiplanningwk_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(191) DEFAULT NULL,
  `momento_rutina_diaria` varchar(191) DEFAULT NULL,
  `lunes` text DEFAULT NULL,
  `martes` text DEFAULT NULL,
  `miercoles` text DEFAULT NULL,
  `jueves` text DEFAULT NULL,
  `viernes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eiplanningwstrategies_eiplanningwk_id_day_of_week_index` (`eiplanningwk_id`,`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiplanningwsummaries`
```sql
CREATE TABLE `eiplanningwsummaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eiplanningwk_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned DEFAULT NULL,
  `componente` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `aprendizaje_esperado` text DEFAULT NULL,
  `indicadores` text DEFAULT NULL,
  `linea_investigacion` text DEFAULT NULL,
  `enfasis_curriculares` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

## A.3 DDL — Familia Planificación Quincenal (bisemanal)

### `eiplanningbwks` (cabecera)
Idéntica estructura a `eiplanningwks` (mismas columnas y tipos, incl. `eiprojectk_id`):
```sql
CREATE TABLE `eiplanningbwks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` int(10) unsigned NOT NULL,
  `grado_id` int(10) unsigned NOT NULL,
  `seccion_id` int(10) unsigned NOT NULL,
  `eiprojectk_id` int(10) unsigned DEFAULT NULL COMMENT 'Proyecto vinculado',
  `finicial` date NOT NULL,
  `ffinal` date NOT NULL,
  `tiempo_ejecucion` int(10) unsigned NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiplanningbwstrategies`
```sql
CREATE TABLE `eiplanningbwstrategies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eiplanningbwk_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(191) NOT NULL,
  `momento_rutina_diaria` varchar(191) NOT NULL,
  `lunes` text DEFAULT NULL,
  `martes` text DEFAULT NULL,
  `miercoles` text DEFAULT NULL,
  `jueves` text DEFAULT NULL,
  `viernes` text DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eiplanningbwstrategies_eiplanningbwk_id_index` (`eiplanningbwk_id`),
  KEY `eiplanningbwstrategies_day_of_week_index` (`day_of_week`),
  KEY `eiplanningbwstrategies_momento_rutina_diaria_index` (`momento_rutina_diaria`),
  CONSTRAINT `eiplanningbwstrategies_eiplanningbwk_id_foreign` FOREIGN KEY (`eiplanningbwk_id`) REFERENCES `eiplanningbwks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
> A diferencia de las estrategias **semanales**, la quincenal agrega `description`, exige `day_of_week`/`momento_rutina_diaria` NOT NULL y tiene FK física CASCADE (migración 2025-11-17, registrada en `migrations`, batch 13).

### `eiplanningbwsummaries`
```sql
CREATE TABLE `eiplanningbwsummaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eiplanningbwk_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned DEFAULT NULL,
  `componente` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `aprendizaje_esperado` text DEFAULT NULL,
  `indicadores` text DEFAULT NULL,
  `linea_investigacion` text DEFAULT NULL,
  `enfasis_curriculares` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

## A.4 DDL — Familia Proyecto de Aula

### `eiprojectks` (cabecera)
```sql
CREATE TABLE `eiprojectks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` int(10) unsigned NOT NULL,
  `grado_id` int(10) unsigned NOT NULL,
  `seccion_id` int(10) unsigned NOT NULL,
  `finicial` date DEFAULT NULL,            -- ⚠️ NULLable aquí (NOT NULL en las demás cabeceras)
  `ffinal` date DEFAULT NULL,
  `tiempo_ejecucion` int(10) unsigned NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiprojectkstrategies`
```sql
CREATE TABLE `eiprojectkstrategies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eiprojectk_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(191) NOT NULL,
  `momento_rutina_diaria` varchar(191) NOT NULL,
  `lunes` text DEFAULT NULL,
  `martes` text DEFAULT NULL,
  `miercoles` text DEFAULT NULL,
  `jueves` text DEFAULT NULL,
  `viernes` text DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projectk_strat_day_moment` (`eiprojectk_id`,`day_of_week`,`momento_rutina_diaria`),
  KEY `projectk_strat_moment` (`eiprojectk_id`,`momento_rutina_diaria`),
  CONSTRAINT `eiprojectkstrategies_eiprojectk_id_foreign` FOREIGN KEY (`eiprojectk_id`) REFERENCES `eiprojectks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiprojectsummaries`
```sql
CREATE TABLE `eiprojectsummaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eiprojectk_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned NOT NULL,   -- ⚠️ NOT NULL aquí (nullable en las demás)
  `componente` varchar(191) DEFAULT NULL,          -- ⚠️ varchar(191) aquí (text en las demás)
  `objetivo` varchar(191) DEFAULT NULL,
  `aprendizaje_esperado` text DEFAULT NULL,
  `indicadores` text DEFAULT NULL,
  `linea_investigacion` varchar(191) DEFAULT NULL,
  `enfasis_curriculares` varchar(191) DEFAULT NULL,
  `estrategias` varchar(191) DEFAULT NULL COMMENT 'Estrategia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eiprojectreviews`
```sql
CREATE TABLE `eiprojectreviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eiprojectk_id` bigint(20) unsigned NOT NULL,
  `posibles_temas_interes` text DEFAULT NULL,
  `eleccion_tema_nombre` text DEFAULT NULL,
  `que_sabe` text DEFAULT NULL,
  `que_desean_aprender` text DEFAULT NULL,
  `que_necesitamos` text DEFAULT NULL,
  `quienes_nos_pueden_apoyar` text DEFAULT NULL,
  `estrategias` varchar(191) DEFAULT NULL COMMENT 'Estrategia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
> Es la fase de "diagnóstico de intereses" del proyecto (web de preguntas: qué saben, qué desean aprender, qué necesitan, quiénes pueden apoyar).

## A.5 DDL — Familia Plan/Informe Especial

### `eispecialks` (cabecera)
```sql
CREATE TABLE `eispecialks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` int(10) unsigned NOT NULL,
  `grado_id` int(10) unsigned NOT NULL,
  `seccion_id` int(10) unsigned NOT NULL,
  `finicial` date NOT NULL,
  `ffinal` date NOT NULL,
  `tiempo_ejecucion` int(10) unsigned NOT NULL,
  `justificacion` text DEFAULT NULL,      -- ⚠️ justificacion (no diagnostico)
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eispecialstrategies`
```sql
CREATE TABLE `eispecialstrategies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eispecialk_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(191) NOT NULL,
  `momento_rutina_diaria` varchar(191) NOT NULL,
  `lunes` text DEFAULT NULL,
  `martes` text DEFAULT NULL,
  `miercoles` text DEFAULT NULL,
  `jueves` text DEFAULT NULL,
  `viernes` text DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `specialk_strat_day_moment` (`eispecialk_id`,`day_of_week`,`momento_rutina_diaria`),
  KEY `specialk_strat_moment` (`eispecialk_id`,`momento_rutina_diaria`),
  CONSTRAINT `eispecialstrategies_eispecialk_id_foreign` FOREIGN KEY (`eispecialk_id`) REFERENCES `eispecialks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eispecialacts`
```sql
CREATE TABLE `eispecialacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eispecialk_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned DEFAULT NULL,
  `componente` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `aprendizaje_esperado` text DEFAULT NULL,
  `indicadores` text DEFAULT NULL,
  `linea_investigacion` text DEFAULT NULL,
  `enfasis_curriculares` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

## A.6 DDL — Familia Evaluación

### `eievaluationks` (cabecera)
```sql
CREATE TABLE `eievaluationks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` bigint(20) unsigned NOT NULL,
  `grado_id` bigint(20) unsigned NOT NULL,
  `lapso_id` bigint(20) unsigned NOT NULL,   -- ⚠️ relación con Lapso (no la tienen las demás cabeceras)
  `seccion_id` bigint(20) unsigned NOT NULL,
  `finicial` date NOT NULL,
  `ffinal` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `recomendacion` text DEFAULT NULL,
  `asistencia` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eievaluationps` (detalle/posiciones)
```sql
CREATE TABLE `eievaluationps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `eievaluationk_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `nombre_ninos` varchar(191) DEFAULT NULL,
  `aprendizaje_alcanzado` text DEFAULT NULL,
  `componente` varchar(191) DEFAULT NULL,
  `indicadores` text DEFAULT NULL,
  `instrumento` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

## A.7 DDL — Informe Final + Aprendizajes (currículo)

### `eifinalks` (informe pedagógico por estudiante)
```sql
CREATE TABLE `eifinalks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(10) unsigned DEFAULT NULL COMMENT 'Orden',
  `pevaluacion_id` bigint(20) unsigned NOT NULL,
  `estudiant_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL COMMENT 'Título del informe',
  `context_group` text DEFAULT NULL COMMENT 'Apreciación del estudiante, características, necesidades',
  `planing_eject` longtext DEFAULT NULL COMMENT 'Resumen de la planificación ejecutada',
  `featured_project` longtext DEFAULT NULL COMMENT 'Descripción del proyecto más significativo',
  `special_activities` longtext DEFAULT NULL COMMENT 'Eventos especiales',
  `achievements` longtext DEFAULT NULL COMMENT 'Logros del estudiante',
  `individual_observations` longtext DEFAULT NULL COMMENT 'Observaciones socioafectivas',
  `specialist_observation` text DEFAULT NULL COMMENT 'Observación de los Especialistas',
  `family_participation` longtext DEFAULT NULL COMMENT 'Participación familiar',
  `conclusions` longtext DEFAULT NULL COMMENT 'Reflexión final del docente',
  `recommendations` longtext DEFAULT NULL COMMENT 'Sugerencias a la familia y equipo docente',
  `expected_learnings` text DEFAULT NULL COMMENT 'Estrategia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eifinalks_pevaluacion_id_foreign` (`pevaluacion_id`),
  KEY `eifinalks_estudiant_id_foreign` (`estudiant_id`),
  CONSTRAINT `eifinalks_estudiant_id_foreign` FOREIGN KEY (`estudiant_id`) REFERENCES `estudiants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eifinalks_pevaluacion_id_foreign` FOREIGN KEY (`pevaluacion_id`) REFERENCES `pevaluacions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eifinalk_expectation` (pivote, sin modelo)
```sql
CREATE TABLE `eifinalk_expectation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eifinalk_id` bigint(20) unsigned NOT NULL,
  `eilearningarea_id` bigint(20) unsigned NOT NULL,
  `eilearningexpectation_id` bigint(20) unsigned NOT NULL,
  `pevaluacion_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eifinalk_expectation_eifinalk_id_foreign` (`eifinalk_id`),
  KEY `eifinalk_expectation_eilearningarea_id_foreign` (`eilearningarea_id`),
  KEY `eifinalk_expectation_eilearningexpectation_id_foreign` (`eilearningexpectation_id`),
  KEY `eifinalk_expectation_pevaluacion_id_foreign` (`pevaluacion_id`),
  CONSTRAINT `eifinalk_expectation_eifinalk_id_foreign` FOREIGN KEY (`eifinalk_id`) REFERENCES `eifinalks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eifinalk_expectation_eilearningarea_id_foreign` FOREIGN KEY (`eilearningarea_id`) REFERENCES `eilearningareas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eifinalk_expectation_eilearningexpectation_id_foreign` FOREIGN KEY (`eilearningexpectation_id`) REFERENCES `eilearningexpectations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eifinalk_expectation_pevaluacion_id_foreign` FOREIGN KEY (`pevaluacion_id`) REFERENCES `pevaluacions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eilearningareas`
```sql
CREATE TABLE `eilearningareas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grado_id` int(11) NOT NULL COMMENT 'Grupo de edad: Grupo 1, 2, 3',
  `name` varchar(191) NOT NULL COMMENT 'Nombre del área de aprendizaje',
  `description` text NOT NULL COMMENT 'Descripción del área aprendizaje',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### `eilearningexpectations`
```sql
CREATE TABLE `eilearningexpectations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eilearningarea_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL COMMENT 'Descripción del aprendizaje esperado',
  `observations` longtext DEFAULT NULL COMMENT 'Observaciones',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eilearningexpectations_eilearningarea_id_foreign` (`eilearningarea_id`),
  CONSTRAINT `eilearningexpectations_eilearningarea_id_foreign` FOREIGN KEY (`eilearningarea_id`) REFERENCES `eilearningareas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

## A.8 Integridad referencial

- **FKs físicas (solo tablas de la tanda 2025):** `eifinalks`→`estudiants`/`pevaluacions`, pivote→4 tablas, `eilearningexpectations`→`eilearningareas`, `eiplanningbwstrategies`→`eiplanningbwks`, `eiprojectkstrategies`→`eiprojectks`, `eispecialstrategies`→`eispecialks`. Todas `ON DELETE CASCADE`.
- **Sin FK física (solo lógica en Eloquent):** todas las tablas de la tanda 2024 (`eiplanningwks`, `eiplanningbwks`, `eiprojectks`, `eispecialks`, `eievaluationks` y sus hijas summaries/reviews/acts/evaluationps). Columnas `profesor_id`, `grado_id`, `seccion_id`, `eiplanningwk_id`, `pevaluacion_id`, etc. son FKs lógicas sin CONSTRAINT. La migración original de eiplanningwstrategies trae el `foreign()` comentado.
- **Tablas externas referenciadas:** `profesors`, `grados`, `seccions`, `lapsos`, `pevaluacions`, `estudiants`, `peducativos`.

## A.9 Drift y bugs schema-código (documentados, verificar en migración)

| # | Hallazgo | Detalle |
|---|---|---|
| 1 | **`max_number_*` en `peducativos` no aplicadas** | 6 migraciones backUp del 2025-06-24 agregan `max_number_eiplanningwks/bwks/eiprojectks/eispecialks/eievaluationks/eifinalks` a `peducativos`, pero esas columnas **no existen** ni en s2526 ni en s2627. El modelo `Peducativo` y el Livewire de Evaluación las referencian → drift: lecturas null, escrituras fallarían. |
| 2 | **down() roto en migración eispecialstrategies** | `2025_11_18_143904_create_eispecialkstrategies_table.php` crea `eispecialstrategies` (correcto para el modelo) pero su `down()` dropea `eispecialkstrategies` (inexistente). Nombre de archivo/clase también dice "eispecialkstrategies". |
| 3 | **Migración mal nombrada** | `2025_02_05_152933_add_order_to_eiplanningbwstrategies_table.php` en realidad altera `eiplanningbwsummaries`. |
| 4 | **Cast residual en Eifinalk** | `'estudiantes' => 'array'` en `$casts` sin columna `estudiantes` existente (código muerto). |
| 5 | **Stubs nunca aplicados** | `database/migrations/temp/` (eispecialps, eifinalps, eipedagogicalks, eipedagogicalps) son stubs vacíos; esas tablas no existen. Hay una tabla `pedagogicals` ajena sin columnas ei*. |
| 6 | **Solo 3 migraciones registradas** | En `migrations` de s2526 solo constan las 3 `*strategies` de nov-2025 (batch 13). Las otras 16 tablas se crearon directo en BD viva; sus migraciones solo existen como backUp fuera del path de Artisan. |

## A.10 Estrategia de migración de schema/datos a cfla

1. **El schema ya está en s2627 (BD principal de cfla)** — 19 tablas clonadas con DDL idéntico, 0 filas, sin migraciones formales registradas. **No crear migraciones nuevas** sin necesidad: la fuente de verdad es `SHOW CREATE TABLE` de s2526.
2. Si se quieren migraciones formales reproducibles en cfla, generarlas desde el DDL de §A.2–A.7 (no desde las migraciones backUp del legacy, que están incompletas — p. ej. les faltan columnas `max_number_*` registradas ni aplicadas — y contienen los bugs §A.9).
3. **Migración de datos** desde la conexión `s2526` (ya definida en `config/database.php` de cfla) por dumps/INSERT…SELECT, respetando: tablas 2024 sin FK física (orden libre), tablas 2025 con CASCADE (cargar padres antes que hijas). Volumen real: 274 planes semanales, 1,630 estrategias semanales, 322 de proyecto, 49 quincenales, 19 proyectos, 47 resúmenes de proyecto, 8 revisiones, 3 planes especiales, 8 actividades, 24 evaluaciones, 98 posiciones.
4. **Sembrar** `eilearningareas` + `eilearningexpectations` con el `EILearningSeeder` del legacy (1,021 líneas, adaptando `grado_id` al id del grado de Inicial en la BD destino) — en s2526 están vacías pese a haber tenido datos.
5. Bajo la regla absoluta del proyecto cfla: prohibido `migrate:fresh`/drop; cualquier operación de datos solo con INSERT/UPDATE selectivos o dump SQL provisto.

---

# Parte B — Modelos Eloquent

> Namespace legacy: `App\Models\app\Inicial` — 18 modelos en `saefl/s2526/app/Models/app/Inicial/`.

## B.0 Convenciones transversales

| Familia | Cabecera (K) | Detalles |
|---|---|---|
| Planificación Semanal | `Eiplanningwk` | `Eiplanningwsummary` (resúmenes por área), `Eiplanningwstrategy` (matriz día×momento) |
| Planificación Quincenal | `Eiplanningbwk` | `Eiplanningbwsummary`, `Eiplanningbwstrategy` |
| Proyecto de Aula | `Eiprojectk` | `Eiprojectsummary`, `Eiprojectreview`, `Eiprojectkstrategy` |
| Plan Especial | `Eispecialk` | `Eispecialact`, `Eispecialstrategy` |
| Plan de Evaluación | `Eievaluationk` | `Eievaluationp` (posiciones) |
| Informe Final | `Eifinalk` | pivote `eifinalk_expectation` |
| Aprendizajes Esperados | `Eilearningarea` | `Eilearningexpectation` |

1. **NINGÚN modelo Inicial usa SoftDeletes ni LogsActivity** — solo `HasFactory`. Ninguna tabla `ei*` tiene `deleted_at`; todo borrado es físico.
2. **16 de 18 modelos NO declaran `protected $table`** (convención Laravel). Solo `Eilearningarea` y `Eilearningexpectation` lo declaran explícito.
3. **Solo `Eievaluationk` declara `$dates` (legacy) y solo `Eifinalk`/`Eilearningarea`/`Eilearningexpectation` declaran `$casts`.**
4. **El puente al resto del sistema es `Pevaluacion`** (`App\Models\app\Profesor\Pevaluacion` — ⚠️ NO está en `Pescolar`). Toda tabla detalle usa `pevaluacion_id` como "Área de aprendizaje".
5. **`Lapso::current()` y las listas por pestudio (id 6)** alimentan los filtros de los controladores de perspectiva (comentarios literales `//Educ Inicial` en `Planning\Tab\InicialController:54` y `Evaluacion\Tab\InicialController:65`).
6. **Migraciones:** todas en `database/migrations/backUps/inicials/` (46 archivos, 2024-09 → 2025-11); stubs huérfanos en `database/migrations/temp/`.
7. **Patrón de ordenamiento repetido en todos los `getOrdered*()`:**
   ```php
   ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END')
   ->orderBy('order')->orderBy('created_at')
   ```
   (`order` es palabra reservada de MySQL — requiere backticks; NULL va al final).
8. **`order` es entrada manual** (min 1), nunca autogenerado; conviven NULL y duplicados.

## B.1 Familia Planificación Semanal

### B.1.1 `Eiplanningwk` (cabecera — plan semanal)

Archivo: `app/Models/app/Inicial/Eiplanningwk.php` · Tabla `eiplanningwks` (create `2024_09_30_084918`; `eiprojectk_id` añadido `2025_02_10_113818` NULL `after('seccion_id')`).

```php
protected $fillable = [
    'profesor_id','grado_id','seccion_id','eiprojectk_id',
    'finicial','ffinal','tiempo_ejecucion','diagnostico','observacion',
];
```

**COLUMN_COMMENTS:** `profesor_id`→'Profesor', `grado_id`→'Grado/Año', `seccion_id`→'Sección', `finicial`→'Inicio', `ffinal`→'Culminación', `tiempo_ejecucion`→'Cant.Semanas', `diagnostico`→'Diagnóstico inicial', `observacion`→'Observación', **`lapso_id`→'Momento' (⚠️ ORFÁN: no existe columna)**, `eiprojectk_id`→'Proyecto vinculado'.

**Relaciones:**
- `eiplanningwsummaries()` → hasMany `Eiplanningwsummary` ('eiplanningwk_id')
- `eiplanningwstrategies()` → hasMany `Eiplanningwstrategy` ('eiplanningwk_id')
- `eiprojectk()` → belongsTo `Eiprojectk` ('eiprojectk_id')
- `profesor()` / `grado()` / `seccion()` → belongsTo Pescolar (`Profesor`, `Grado`, `Seccion`)

**Accessors:**
- `getPeducativoAttribute()` — query cruda: `Peducativo::select('peducativos.*')->join('pestudios','peducativos.id','=','pestudios.peducativo_id')->join('grados','pestudios.id','=','grados.pestudio_id')->where('grados.id',$this->grado_id)->groupBy('peducativos.manager_id')->orderBy('peducativos.id')->first()` — Período Educativo del grado.
- `getManagerAttribute()` — mismo join vía `peducativos.manager_id` → `User` coordinador.
- `getListMomentAttribute()` → `Eiplanningwstrategy::LIST_MOMENT`
- `getWeekDaysAttribute()` → `Eiplanningwstrategy::WEEK_DAYS`

**Métodos:** `getPevaluacions($profesor_id=null,$lapso_id=null)` (query cruda sobre `Pevaluacion` con joins pensums→asignaturas→grados, seccions, lapsos; filtra `seccion_id`, `whereNull` de `deleted_at` de pensums/pevaluacions; selectRaw `CONCAT(asignaturas.name, " [",asignaturas.code,"] ",grados.code," ",seccions.name," ",lapsos.code_sm) as fullname_lg`); `getPevaluacionsList(...)` → `pluck('fullname_lg','id')`; `getOrderedStrategies()`; `getOrderedSummaries()`; `getStrategyByMomentAndDay($momento_rutina_diaria, $day_of_week)`.

**Import muerto:** `App\Models\app\Institucion\Autoridad` (no usado).

### B.1.2 `Eiplanningwsummary` (resumen por área)

Tabla `eiplanningwsummaries` (create `2024_09_30_084919`; `order` añadido `2025_02_05_152916`).

```php
protected $fillable = ['eiplanningwk_id','pevaluacion_id','componente','objetivo',
    'aprendizaje_esperado','indicadores','linea_investigacion','enfasis_curriculares','order'];
```

**COLUMN_COMMENTS:** `eiplanningwk_id`→'Relación con la planificación semanal', `pevaluacion_id`→'Área de aprendizaje', `componente`→'Componente', `objetivo`→'Objetivo', `aprendizaje_esperado`→'Aprendizaje esperado', `indicadores`→'Indicadores', `linea_investigacion`→'Línea de investigación', `enfasis_curriculares`→'Énfasis curriculares', **`lapso_id`→'Momento' (⚠️ ORFÁN)**, `order`→'Orden'.

**Relaciones:** `eiplanningwk()` belongsTo; `pevaluacion()` belongsTo `Pevaluacion`.

### B.1.3 `Eiplanningwstrategy` (matriz día×momento)

Tabla `eiplanningwstrategies` (create `2024_09_30_084920`; `order` `2025_02_05_152902`; `day_of_week` + índice `(eiplanningwk_id, day_of_week)` `2025_07_10_141200`). **FK solo lógica** (la constraint está comentada en la migración). **No existe columna `description`.**

```php
protected $fillable = ['eiplanningwk_id','day_of_week','momento_rutina_diaria',
    'lunes','martes','miercoles','jueves','viernes','order'];
```

**Constantes (idénticas en las 4 entidades strategy):**
```php
const LIST_MOMENT = [   // 10 momentos de la Rutina Diaria preescolar
    'Recibimiento' => 'Recibimiento',  'Momento Cívico' => 'Momento Cívico',
    'Aseo-Desayuno-Aseo' => 'Aseo-Desayuno-Aseo',
    'Periodo: Planificación' => 'Periodo: Planificación',
    'Periodo: Trabajo Libre' => 'Periodo: Trabajo Libre',
    'Periodo: Orden y limpieza' => 'Periodo: Orden y limpieza',
    'Periodo: Intercambio y Recuento' => 'Periodo: Intercambio y Recuento',
    'Periodo: Trabajos en Pequeños Grupos' => 'Periodo: Trabajos en Pequeños Grupos',
    'Periodo: Actividades Colectivas' => 'Periodo: Actividades Colectivas',
    'Periodo: Despedida' => 'Periodo: Despedida',
];
const WEEK_DAYS = ['lunes' => 'Lunes','martes' => 'Martes','miercoles' => 'Miércoles',
    'jueves' => 'Jueves','viernes' => 'Viernes'];
```

**COLUMN_COMMENTS:** … `day_of_week`→'Día de la semana', `momento_rutina_diaria`→'Momento de la Rutina Diaria', `lunes`→'Estrategia del lunes' … `viernes`→'Estrategia del viernes', `order`→'Orden', **`description`→'Descripción' (⚠️ ORFÁN: columna inexistente y fuera de fillable)**.

**Scope:** `scopeForDay($query, $day)` → `where('day_of_week', $day)`.

**Accessor/Mutator (verbatim):**
```php
public function getEstrategiaAttribute() {
    // Si tenemos day_of_week, usar el campo lunes como estrategia principal
    if ($this->day_of_week) { return $this->lunes; }
    // Mantener compatibilidad con el sistema anterior
    return $this->lunes;
}
public function setEstrategiaAttribute($value) { $this->attributes['lunes'] = $value; }
```
⚠️ El atributo virtual `estrategia` **siempre lee/escribe la columna `lunes`** (la rama `if` es código muerto).

## B.2 Familia Planificación Quincenal

### B.2.1 `Eiplanningbwk`

Tabla `eiplanningbwks` (create `2024_09_30_084928`; `eiprojectk_id` añadido `2025_02_10_113844`). Esquema/fillable/COLUMN_COMMENTS **idénticos a Eiplanningwk** (incluye el orfán `lapso_id`). Relaciones: `eiplanningbwsummaries()`, `eiplanningbwstrategies()`, `eiprojectk()`, `profesor()`, `grado()`, `seccion()`. Accessors `getPeducativoAttribute`/`getManagerAttribute`/`getListMomentAttribute` (→ `Eiplanningbwstrategy::LIST_MOMENT`)/`getWeekDaysAttribute`. Métodos equivalentes (`getPevaluacions`, `getPevaluacionsList`, `getOrderedSummaries`, `getOrderedStrategies`, `getStrategyByMomentAndDay`).

### B.2.2 `Eiplanningbwsummary`

Tabla `eiplanningbwsummaries` (create `2024_09_30_084929`). Idéntica a `Eiplanningwsummary` con FK `eiplanningbwk_id`. ⚠️ Su columna `order` fue añadida por la **migración mal nombrada** `2025_02_05_152933_add_order_to_eiplanningbwstrategies_table.php` cuyo `up()` altera `eiplanningbwsummaries`.

### B.2.3 `Eiplanningbwstrategy`

Tabla `eiplanningbwstrategies` — create **`2025_11_17_143207`** (posterior a las demás). `eiplanningbwk_id` **foreignId constrained CASCADE**; `day_of_week` string NOT NULL; `momento_rutina_diaria`; `lunes`…`viernes`; `order`; **`description` text NULL (sí existe aquí)**; índices simples individuales en `eiplanningbwk_id`, `day_of_week`, `momento_rutina_diaria`.

Fillable: `[eiplanningbwk_id, day_of_week, momento_rutina_diaria, lunes, martes, miercoles, jueves, viernes, order, description]`. Constantes/scope/accessor idénticos al patrón (estrategia→lunes).

## B.3 Familia Proyecto de Aula

### B.3.1 `Eiprojectk` (cabecera)

Tabla `eiprojectks` (create `2024_09_30_084932`). Diferencias con las planificaciones: `finicial`/`ffinal` **nullable**; sin auto-referencia a proyectos.

Fillable: `['profesor_id','grado_id','seccion_id','finicial','ffinal','tiempo_ejecucion','diagnostico','observacion']`. COLUMN_COMMENTS sin orfanes.

**Relaciones:** `eiprojectreviews()`, `eiprojectsummaries()`, `eiprojectkstrategies()` (hasMany); `profesor()`/`grado()`/`seccion()` (belongsTo).

**Métodos:** `getPevaluacions`/`getPevaluacionsList`, `getOrderedSummaries()`, `getOrderedViews()` (eiprojectreviews), `getOrderedStrategies()`, `getStrategyByMomentAndDay()`, y el **estático**:
```php
public static function getForProfesorIdList($profesor_id = null)
{
    $query = Eiprojectk::query();
    if ($profesor_id) { $query->where('profesor_id', $profesor_id); }
    $results = $query->pluck('diagnostico', 'id');
    return $results->map(fn($diagnostico, $id) => $id.': '.Str::limit($diagnostico, 50, ' ...'));
}
```
→ Collection `"id: diagnóstico(truncado 50)"` para el select "Proyecto vinculado" de las planificaciones.

### B.3.2 `Eiprojectsummary`

Tabla `eiprojectsummaries` (create `2024_09_30_084934`; `order` `2025_02_05_152951`; `estrategias` string `2025_07_09_141102`). `pevaluacion_id` **NOT NULL** aquí.

Fillable: `['eiprojectk_id','pevaluacion_id','componente','objetivo','aprendizaje_esperado','indicadores','linea_investigacion','enfasis_curriculares','order','estrategias']`. COLUMN_COMMENTS: `eiprojectk_id`→'Proyecto de Aula', …, **`lapso_id`→'Momento' (ORFÁN)**, `estrategias`→'Estrategias'.

**Relaciones:** `eiprojectk()`; `pevaluacion()`; ⚠️ **`eiplanningwk()` → belongsTo `Eiplanningwk` con FK `eiplanningwk_id` que NO existe en la tabla — relación ROTA** (la vinculación real es Eiplanningwk→eiprojectk). Eliminar en migración.

### B.3.3 `Eiprojectreview` (diagnóstico/elección del tema)

Tabla `eiprojectreviews` (create `2024_09_30_084933`; `order` `2025_02_05_152943`; `estrategias` `2025_07_09_141108`).

Fillable: `['eiprojectk_id','posibles_temas_interes','eleccion_tema_nombre','que_sabe','que_desean_aprender','que_necesitamos','quienes_nos_pueden_apoyar','order','estrategias']`.

COLUMN_COMMENTS (posiciones temáticas del diagnóstico participativo): `posibles_temas_interes`→'Posibles temas de interés', `eleccion_tema_nombre`→'Elección del tema y nombre del proyecto', `que_sabe`→'Qué saben los estudiantes', `que_desean_aprender`→'Qué desean aprender los estudiantes', `que_necesitamos`→'Qué necesitamos para el proyecto', `quienes_nos_pueden_apoyar`→'Quiénes nos pueden apoyar', `estrategias`→'Estrategias'.

Relación: `eiprojectk()` belongsTo.

### B.3.4 `Eiprojectkstrategy`

Tabla `eiprojectkstrategies` — create **`2025_11_18_140550`**: `eiprojectk_id` foreignId **cascade**; `description` text NULL; índices compuestos con nombre corto `projectk_strat_day_moment (eiprojectk_id, day_of_week, momento_rutina_diaria)` y `projectk_strat_moment (eiprojectk_id, momento_rutina_diaria)`. Patrón constantes/scope/accessor idéntico.

## B.4 Familia Plan Especial

### B.4.1 `Eispecialk` (cabecera)

Tabla `eispecialks` (create `2024_09_30_084935`). **Difiere: `justificacion` en lugar de `diagnostico`.**

Fillable: `['profesor_id','grado_id','seccion_id','finicial','ffinal','tiempo_ejecucion','justificacion','observacion']`. COLUMN_COMMENTS: `justificacion`→'Justificación', resto igual.

Relaciones: `activities()` → hasMany `Eispecialact` ('eispecialk_id'); `eispecialkstrategies()` → hasMany `Eispecialstrategy` (⚠️ nombre de método con "k", modelo sin ella); `profesor()`/`grado()`/`seccion()`.

Métodos: `getOrderedActivities()`, `getPevaluacions`/`getPevaluacionsList`, `getOrderedStrategies()`, `getStrategyByMomentAndDay()`, accessors `getPeducativo`/`getManager`/`getListMoment` (→ `Eispecialstrategy::LIST_MOMENT`)/`getWeekDays`.

### B.4.2 `Eispecialact`

Tabla `eispecialacts` (create `2024_09_30_084937`; `order` `2025_02_05_153000`). Estructura igual a los summaries: fillable `['eispecialk_id','pevaluacion_id','componente','objetivo','aprendizaje_esperado','indicadores','linea_investigacion','enfasis_curriculares','order']`; COLUMN_COMMENTS con `eispecialk_id`→'Plan Especial' y **orfán `lapso_id`**. Relaciones: `eispecialk()`, `pevaluacion()`.

### B.4.3 `Eispecialstrategy`

Tabla `eispecialstrategies` — create `2025_11_18_143904` (⚠️ el archivo se llama `create_eispecialkstrategies` pero crea `eispecialstrategies`; el `down()` dropea `eispecialkstrategies` — nombre inconsistente, irrelevante porque la convención de clase `Eispecialstrategy` produce `eispecialstrategies` que coincide con el create). `eispecialk_id` foreignId cascade; `description` NULL; índices `specialk_strat_day_moment` y `specialk_strat_moment`. Patrón idéntico.

## B.5 Familia Plan de Evaluación

### B.5.1 `Eievaluationk` (cabecera)

Tabla `eievaluationks` (create `2024_09_30_090001`). **Única cabecera con `lapso_id` directo.**

```php
protected $fillable = ['profesor_id','grado_id','lapso_id','seccion_id',
    'finicial','ffinal','observaciones','recomendacion','asistencia'];
protected $dates = ['finicial','ffinal'];   // estilo legacy, sin $casts
```

⚠️ La BD tiene **ambas columnas `observaciones` y `observacion`**; solo `observaciones` es fillable (doble columna legacy).

**COLUMN_COMMENTS:** `profesor_id`→'Profesor', `grado_id`→'Grado', `lapso_id`→'Momento', `seccion_id`→'Sección', `finicial`→'Fecha inicial', `ffinal`→'Fecha final', `observaciones`→'Observaciones del docente', `recomendacion`→'Recomendación del Coord. de Evaluación', `asistencia`→'Control de Asistencia', **`tiempo_ejecucion`→'Período de ejecucion' (ORFÁN)**.

**Relaciones:** `eievaluationps()` hasMany; `profesor()`/`grado()`/`seccion()`/`lapso()` belongsTo.

**Accessors:** `getPeducativoAttribute()`, `getManagerAttribute()` (mismos joins que las demás cabeceras).

**Métodos:**
- `getPevaluacions($profesor_id=null,$lapso_id=null)` / `getPevaluacionsList(...)` — mismo patrón `fullname_lg`.
- `getPositionsForArea($id)`:
  ```php
  Eievaluationp::select('eievaluationps.*')
      ->join('eievaluationks', ...)->join('pevaluacions', ...)
      ->where('eievaluationks.id', $this->id)->where('pevaluacions.id', $id)
      ->orderByRaw('CASE WHEN `eievaluationps`.`order` IS NOT NULL THEN 0 ELSE 1 END')
      ->orderBy('eievaluationps.order')->orderBy('eievaluationps.created_at')->get();
  ```
- `getPositionsForAreaFilter($id)` — igual + `where(fn($q) => $q->whereNotNull('fecha')->orWhereNotNull('nombre_ninos')->orWhereNotNull('aprendizaje_alcanzado')->orWhereNotNull('indicadores')->orWhereNotNull('instrumento')->orWhereNotNull('observacion'))`.
- `getOrderedEvaluationps()` — patrón order NULLS-LAST.

### B.5.2 `Eievaluationp` (posición — renglón por área)

Tabla `eievaluationps` (create `2024_09_30_090002`; `order` `2025_02_05_153012`).

Fillable: `['eievaluationk_id','pevaluacion_id','fecha','nombre_ninos','aprendizaje_alcanzado','componente','indicadores','instrumento','observacion','order']`.

**COLUMN_COMMENTS:** `eievaluationk_id`→'Plan de Evaluación', `pevaluacion_id`→'Área de aprendizaje/Año', `fecha`→'Fecha de evaluación', `nombre_ninos`→'Nombre de los niños', `aprendizaje_alcanzado`→'Aprendizaje a ser alcanzado', `componente`→'Componente del área de aprendizaje', `indicadores`→'Indicadores de evaluación', `instrumento`→'Instrumento de evaluación', `observacion`→'Observaciones adicionales del docente', **`lapso_id`→'Momento' (ORFÁN)**, `order`→'Orden'.

Relaciones: `eievaluationk()` belongsTo; `pevaluacion()` belongsTo.

## B.6 Familia Informe Final

### B.6.1 `Eifinalk` (boletín pedagógico por estudiante/área/lapso)

Tabla `eifinalks` — create operativo `backUps/inicials/2025_05_12_201358` (existe además stub huérfano `temp/2024_09_30_085047` con solo id+timestamps). Adds: `order` `2025_06_13_150756` (after id), `specialist_observation` `2025_07_09_160512`, `expected_learnings` `2025_07_09_151040`.

```php
protected $fillable = ['order','pevaluacion_id','title','estudiant_id','context_group',
    'planing_eject','featured_project','special_activities','achievements',
    'individual_observations','specialist_observation','recommendations',
    'expected_learnings','family_participation','conclusions'];
protected $casts = ['order' => 'integer','created_at' => 'datetime','updated_at' => 'datetime',
    'estudiantes' => 'array'];   // ⚠️ cast HUÉRFANO: no existe columna estudiantes
```

**COLUMN_COMMENTS (transcripción completa):** `order`→'Orden', `pevaluacion_id`→'Plan de evaluación', `estudiant_id`→'Estudiante', `title`→'Título del informe', `context_group`→'Apreciación del estudiante, características, necesidades', `planing_eject`→'Resumen de la planificación ejecutada' (⚠️ typo real "planing"), `featured_project`→'Descripción del proyecto más significativo', `special_activities`→'Eventos especiales', `achievements`→'Logros del estudiante', `individual_observations`→'Observaciones socioafectivas', `specialist_observation`→'Observación de los Especialistas', `family_participation`→'Participación familiar', `conclusions`→'Reflexión final del docente', `recommendations`→'Sugerencias a la familia y equipo docente', `expected_learnings`→'Aprendizajes Esperados'.

**Relaciones Eloquent:**
```php
public function pevaluacion()  { return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id', 'id'); }
public function estudiant()   { return $this->belongsTo('App\Models\app\Estudiant'); }  // FK convención estudiant_id
public function expectations() {
    return $this->belongsToMany(Eilearningexpectation::class, 'eifinalk_expectation')
        ->withPivot('eilearningarea_id', 'pevaluacion_id')->withTimestamps();
}
```

**Relaciones delegadas (NO Eloquent — nullsafe, colisionan con accessors homónimos):**
```php
public function profesor() { return $this->pevaluacion?->profesor; }
public function seccion()  { return $this->pevaluacion?->seccion; }
public function lapso()   { return $this->pevaluacion?->lapso; }
public function pensum()  { return $this->pevaluacion?->pensum; }
```

**Scopes:** `scopeByLapsoYSeccion($query, $lapso_id, $seccion_id)` y `scopeByProfesor($query, $profesor_id)` — ambos `whereHas('pevaluacion', ...)`.

**Accessors:** `getResumenTituloAttribute()` → `Str::limit($this->title, 40)`; `getProfesorAttribute`/`getSeccionAttribute`/`getLapsoAttribute`/`getPensumAttribute` → delegados de pevaluacion.

### B.6.2 Pivote `eifinalk_expectation` (sin modelo)

Create `2025_05_14_142029`: `eifinalk_id`, `eilearningarea_id`, `eilearningexpectation_id`, `pevaluacion_id` — 4 foreignId **constrained cascade** + timestamps. **Desnormalización deliberada**: el pivote lleva área + pevaluación para filtrar por período (`wherePivot('pevaluacion_id', ...)`) y agrupar el boletín por área sin joins extra.

## B.7 Familia Aprendizajes Esperados (currículo Inicial)

### B.7.1 `Eilearningarea` (área de aprendizaje)

Tabla explícita `eilearningareas` (create `2025_05_12_201300` — archivo `ei_learningareas`, tabla `eilearningareas`). `grado_id` integer con `->comment('Grupo de edad: Grupo 1, 2, 3')` **sin FK**.

```php
protected $table = 'eilearningareas';
protected $fillable = ['grado_id','name','description'];
protected $casts = ['grado_id' => 'integer'];
```

**COLUMN_COMMENTS:** `grado_id`→'Grupo de edad: Grupo 1, 2, 3', `name`→'Nombre del área de aprendizaje', `description`→'Descripción del área aprendizaje'.

**Relaciones:** `grado()` belongsTo `Grado` (FK implícita); `expectations()` hasMany `Eilearningexpectation` ('eilearningarea_id').

**Scopes:** `scopeByGrado($q,$gradoId)`; `scopeSearch($q,$search)` (like name/description); `scopeActive($q)` → `whereHas('expectations')` (⚠️ semántica particular: "activa" = tiene expectativas).

**Accessors:** `getNombreCompletoAttribute()` → `"{name} - Grupo {grado->name}"`; `getExpectationsCountAttribute()`.

**Métodos:** `hasExpectations()`, `getExpectationsByGrado($gradoId)` (redundante — filtra por su propio grado), `getActiveExpectations()` (whereNotNull description), `getRelatedEifinalks()`.

### B.7.2 `Eilearningexpectation` (aprendizaje esperado)

Tabla explícita `eilearningexpectations` (create `2025_05_12_201310`). `eilearningarea_id` foreignId cascade; `description` text; `observations` longText NULL.

```php
protected $table = 'eilearningexpectations';
protected $fillable = ['eilearningarea_id','description','observations'];
protected $casts = ['eilearningarea_id' => 'integer'];
```

**COLUMN_COMMENTS:** `eilearningarea_id`→'Área de aprendizaje', `description`→'Descripción del aprendizaje esperado', `observations`→'Observaciones'.

**Relaciones:** `area()` belongsTo; `eifinalks()` belongsToMany vía `eifinalk_expectation` con `withPivot('eilearningarea_id','pevaluacion_id')` + timestamps.

**Scopes:** `scopeByArea`, `scopeSearch` (description/observations), `scopeWithObservations`, `scopeByGrado` (whereHas area).

**Accessors:** `getNombreCompletoAttribute()`, `getEifinalksCountAttribute()`, `getHasObservationsAttribute()`, `getGradoAttribute()` → `$this->area->grado`.

**Métodos:** `hasEifinalks()`, `getEifinalksByPevaluacion($pevaluacionId)` (wherePivot), `getRelatedAreas()` (⚠️ referencia `EiLearningarea` — casing distinto, funciona por insensibilidad de PHP; normalizar), `getActiveEifinalks()`.

## B.8 Servicio `EducationStatsService`

Archivo: `app/Services/EducationStatsService.php` (96 líneas). `getEducationStats($profesor_id=null, $grado_id=null): array` — 9 métricas, todas conteos simples:

| Clave | Fuente | Lógica |
|---|---|---|
| `eiplanningwks` … `eievaluationks` | tablas respectivas | `count()` con filtros directos `grado_id`/`profesor_id` |
| `eifinalks` | `eifinalks` | ⚠️ distinto: `whereHas('pevaluacion')`→`whereHas('pensum', fn($q)=>$q->where('grado_id',$grado_id))` + `where('profesor_id',...)` (ruta relacional anidada Eifinalk→Pevaluacion→Pensum→grado) |
| `totalRecords` | — | `array_sum` de los 6 |
| `activeProjects` | `eiprojectks` | `whereNotNull('finicial')->whereNull('ffinal')` + filtros |
| `completedEvaluations` | `eievaluationks` | `whereNotNull('ffinal')` + filtros |

`getStatsAsJson(...)` → `json_encode(...)`. **Consumidor:** `Evaluacion\Tab\InicialController` (cache 5 min `inicial_data_{p}_{g}_{s}`, pasa `education_stats` + `stats_json` a la vista). El `Planning\Tab\InicialController` **no** lo usa (queries inline).

## B.9 Modelos relacionados fuera de Inicial

### `Pevaluacion` — el puente ("Área de aprendizaje")

⚠️ **Ubicación real: `app/Models/app/Profesor/Pevaluacion.php`** (namespace `App\Models\app\Profesor`), NO en Pescolar. **Usa `SoftDeletes`** — todas las queries del módulo la filtran `whereNull('pevaluacions.deleted_at')`.

- Fillable: `['profesor_id','lapso_id','seccion_id','pensum_id','grupo_estable_id','status_baremo','status_official','status_note_report','nota_type','escala_id','objetivo','description','observations','category','deleted_at']`
- `eifinalks()` → `hasMany(Eifinalk::class)` — única referencia inversa del módulo en Pevaluacion.
- Relaciones usadas: `pensum()`, `seccion()`, `profesor()`, `lapso()`, `grupo_estable()`, `escala()`, etc.
- Accessors consumidos por los formatos: `getGradoAttribute` (join pensums), `getAsignaturaAttribute`, `getPestudioAttribute`, `getFullNameAttribute` (concatena description + grado code + seccion + lapso code_sm).
- `status_official` separa **áreas oficiales** de **componentes de formación** en el boletín final.
- Estático usado por componentes: `Pevaluacion::list_pevaluacion($profesor_id)` (selectRaw `CONCAT(lapsos.name,' | ',grados.name,' ',seccions.name,' | ',asignaturas.name)`).

### `Estudiant` (modelo raíz `app/Models/app/Estudiant.php`)

SoftDeletes + 16 traits. Referencias al módulo (transcripción exacta):
```php
// trait Relations (Functions/Estudiant/Relations.php:129)
public function eifinalks() { return $this->hasMany(Eifinalk::class, 'estudiant_id'); }

// Estudiant.php:461-479
public function getHasEifinalkAttribute() { return $this->eifinalks()->exists(); }
public function getEifinalkIdAttribute() {
    $eifinalk = $this->eifinalks()->first();
    return $eifinalk ? $eifinalk->id : null;
}
public function hasEifinalkForLapso($lapsoId) {
    return $this->eifinalks()->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $lapsoId))->exists();
}
```

### `Peducativo` — límites del módulo

Campos que gobiernan cuántos documentos puede crear un docente (migraciones `2025_06_24_1242xx/1243xx`, `unsignedInteger` NULL):
`max_number_eiplanningwks`, `max_number_eiplanningbwks`, `max_number_eiprojectks`, `max_number_eispecialks`, `max_number_eievaluationks`, `max_number_eifinalks` — COLUMN_COMMENTS "Cantidad máxima de planes semanales/quincenales/proyectos/…/Informes Pedagógico". Los accessors `getPeducativoAttribute`/`getManagerAttribute` de las 5 cabeceras lo resuelven desde `grado_id` (con `manager_id`, `assistant_id`, `deputy_id`).

### `Lapso` — `current()` y listas

```php
public static function current($fecha = null) {
    $fecha = ($fecha) ? $fecha : Carbon::now()->format('Y-m-d');
    $lapso_first = Lapso::all()->first();
    $lapso = Lapso::whereDate('finicial','<=',$fecha)->whereDate('ffinal','>=',$fecha)->orderBy('id')->first();
    return ($lapso) ? $lapso : $lapso_first;   // ⚠️ fallback no determinístico (orden de inserción)
}
```
(y clon `getCurrentOrFirst()`). Trait `Functions\Lapso\Lists`:
```php
public static function list_lapso() { return Lapso::select('name','id')->orderby('name','asc')->pluck('name','id'); }
public static function list_lapso_final() { /* agrega opción "FINAL" con id = último+1 */ }
```

### `Grado` — `list_pestudio_grado(6)`

```php
public static function list_pestudio_grado($pestudio_id = null) {
    $pestudios = Pestudio::active('true');
    $pestudios = ($pestudio_id) ? $pestudios->where('id',$pestudio_id) : $pestudios;
    $datas_grados = collect();
    foreach ($pestudios->get() as $pestudio) {
        $datas_grados->put($pestudio->code.'-'.$pestudio->name,
            $pestudio->getGradosActive()->pluck('name','id'));
    }
    return $datas_grados;   // Collection anidada "CODE-Name" => [grado_id => name] para optgroups
}
```
Depende de `Pestudio::getGradosActive()` (`grados->where('status_active','true')`) y de `scopeActive` (⚠️ `status_active` es string `'true'/'false'` — patrón ENUM legacy).

### `Profesor` — `list_profesors_pestudio(6)`

Join `pevaluacions → pensums → grados → pestudios` con `whereNull(deleted_at)` de las 4 tablas, filtro `pestudios.id = 6`, `groupBy profesors.id`, selectRaw `CONCAT(lastname,' ',name)` — **solo lista profesores con carga académica activa**.

### Referencias inversas (grep verificado)

`Grado`, `Seccion`, `Profesor`, `Pensum` (Pescolar) **NO referencian modelos Inicial**. Las únicas referencias inversas fuera del módulo: `Pevaluacion::eifinalks()`, `Estudiant::eifinalks()` + 3 métodos, y los `max_number_*` de `Peducativo`.

## B.10 Hallazgos críticos de modelos (para la migración)

1. **Sin SoftDeletes** en las 18 tablas — borrado físico; las queries filtran `deleted_at` solo de tablas ajenas.
2. **FKs físicas solo en tablas 2025** (`eifinalks`, `eilearningexpectations`, `eifinalk_expectation`, y las strategies de 2025-11); las 12 tablas de 2024-09 son enteros sueltos.
3. **COLUMN_COMMENTS huérfanos:** `lapso_id` en 7 modelos; `tiempo_ejecucion` en Eievaluationk; `description` en Eiplanningwstrategy.
4. **`Eifinalk::$casts['estudiantes']='array'` sin columna.**
5. **Relación rota** `Eiprojectsummary::eiplanningwk()` (FK inexistente).
6. **`Eievaluationk` tiene doble columna** `observaciones`/`observacion` (solo la primera es fillable).
7. **Migración mal nombrada** `add_order_to_eiplanningbwstrategies` (altera bwsummaries); **down() inconsistente** en `create_eispecialkstrategies`.
8. **Código duplicado masivo en 5 cabeceras** (`getPeducativo`, `getManager`, `getPevaluacions`, `getPevaluacionsList`, `getStrategyByMomentAndDay`, `getOrderedStrategies`) + constantes `LIST_MOMENT`/`WEEK_DAYS` repetidas en 4 modelos + accessor `estrategia`→`lunes` en 4 modelos — candidato a servicio/trait único en la nueva arquitectura.
9. **`order` reservada MySQL** (backticks obligatorios) y nullable; replicar el patrón NULLS-LAST exacto.
10. **Dos creates para `eifinalks`** (stub temp/ vs real backUps/).
11. **Tablas huérfanas sin modelo** en `temp/`: `eispecialps`, `eifinalps`, `eipedagogicalks`, `eipedagogicalps`.
12. **`Lapso::current()` fallback no determinístico** (primer registro por inserción).
13. **Relaciones delegadas nullsafe de Eifinalk** colisionan con accessors homónimos — unificar como campos derivados del recurso `pevaluacion`.
14. **Vinculación planificación↔proyecto N:1** (`eiplanningwk(s).eiprojectk_id` nullable) sin relación inversa en `Eiprojectk`.
15. **Nomenclatura:** `k`=cabecera, `p`=partida; `summary`=resumen por área (pevaluación); `strategy`=matriz día×momento con columna por día; `act`=actividad de plan especial; `review`=diagnóstico/elección de tema. `tiempo_ejecucion`="Cant.Semanas" en planificaciones pero "Período de ejecución" en evaluación.
16. **`Pevaluacion` está en `app/Models/app/Profesor/`**, no en Pescolar — ajustar imports en la migración (en cfla: `App\Models\app\Academy\Pevaluacion`).
