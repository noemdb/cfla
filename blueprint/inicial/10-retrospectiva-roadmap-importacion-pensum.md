# 10 — Retrospectiva y roadmap: importación desde s2526 y campo `pensum_id`

> Redactado 2026-10-08 tras implementar en `Eiplanningwk` (planificación semanal) dos
> capacidades que se quieren replicar a los otros documentos del módulo:
> **importación de planes desde s2526** y **campo opcional `pensum_id` (área de
> aprendizaje)**. Este documento deja la retrospectiva (qué se hizo y por qué) y el
> roadmap de generalización, para que la réplica sea por diseño y no copia-pega.

## 1. Retrospectiva — qué se implementó y por qué

### 1.1 Importación de planes desde s2526 (solo `eiplanningwks`)

El asistente `Importar de s2526` (botón en `livewire/inicial/eiplanningwk/index.blade.php`)
abre un modal que lista los planes legacy del período anterior y los importa al
período actual con **ID nuevo**.

**Servicio** `app/Services/Inicial/ImportadorEiplanningwk.php`:

| Método | Responsabilidad |
|---|---|
| `cargasVigentes($profesorId)` | Pevaluaciones del docente en el **lapso en curso** (pestudio 6): `pevaluacion_id`, `grado_id`, `seccion_id`, `asignatura_id`. Es el "ancla" del contexto. |
| `candidatos($profesorId)` | Planes legacy cuyo `(grado_id, seccion_id)` coincide con alguna carga vigente. Trae `profesor_origen`, áreas (`asignatura_ids`/`asignaturas`), `estrategias`, `resumenes` — resueltos en consultas agrupadas (no N+1). |
| `importar($legacyIds, $profesorId)` | Crea cada plan con **ID nuevo**, anclado a la carga vigente; copia estrategias íntegras; **remapea resúmenes** a la pevaluación vigente (misma asignatura+sección) o los omite con conteo; **anti-duplicado** (cabecera completa). |
| `origenDisponible()` | ¿Responde la conexión `s2526`? (solo lectura). |

**Reglas de negocio de la importación**:
- El plan legacy entra **solo si** su `(grado, sección)` coincide con la carga vigente
  del docente. **No se filtra por `profesor_id` de origen**: sirve como plantilla.
- Cada plan nace con **id nuevo** (nunca se copia el id legacy): sin colisiones.
- El proyecto vinculado (`eiprojectk_id`) **no se copia** (ids del período viejo) y se
  informa.
- La **duplicidad** se evita por cabecera completa (`profesor_id, grado_id, seccion_id,
  finicial, ffinal, diagnostico`).
- s2526 es **solo lectura**; todo va dentro de `DatabaseTransactions`.

**UI del asistente** (`partials/import-wizard.blade.php`):
- Cabecera con contexto (N planes · lapso en curso).
- Toolbar: búsqueda, filtro por grado, filtro por **docente origen** (select), orden
  (Fecha/Grado), alternador Tarjetas↔Tabla, contador.
- Tarjetas con menú kebab (Ver detalle / Importar este plan), botón **Detalle** visible
  y **paginación numerada**.
- Reporte post-importación (creados + omitidos con motivo).

**Nota de diseño**: el filtro por **área (asignatura)** se implementó y luego se **eliminó**
porque no funcionaba como se esperaba (los planes semanales reales de s2526 no traen
resúmenes, así que casi todos carecen de área; el select quedaba casi vacío). Queda el
filtro por docente origen y por grado, que sí son útiles. Las áreas se muestran como
**badges informativos** en la tarjeta, no como filtro.

### 1.2 Campo opcional `pensum_id` (área de aprendizaje) en `eiplanningwks`

Añadido como columna **nullable** en la cabecera, con FK a `pensums` (`nullOnDelete`).

- **Migración** `2026_10_08_000002_add_pensum_id_to_eiplanningwks_table.php` (aditiva,
  re-ejecutable con `hasColumn`).
- **Modelo**: `fillable`, `@property`, `COLUMN_COMMENTS`, relación `pensum()`.
- **Componente**: `listPensum` + `loadPensums($gradoId)` — **solo los pensums del
  docente** (los que aparecen en SUS `pevaluaciones` del lapso en curso), recargados al
  elegir grado. `save()` valida que el pensum pertenezca al docente.
- **Vistas**: select "Área de aprendizaje (opcional)" en el form, detalle, formato y
  columna "Área" en las perspectivas.

**Decisión de fondo (importante)**: se consultó la fuente (`s2526`) y se determinó que
**la `pevaluacion` vive en el DETALLE** (`summaries`/`positions`), no en la cabecera:
`eiplanningwks` legacy no tiene `pevaluacion_id`, y cada plan tiene **hasta 3
pevaluaciones distintas** (una por área). Por eso el `pevaluacion_id` que se intentó
añadir a la cabecera se **reviertió**. El `pensum_id` sí se mantiene como campo de
**conveniencia** en la cabecera (relaciona el plan con un área sin depender de los
resúmenes), pero **no sustituye** al `pevaluacion_id` de los detalles.

---

## 2. Patrones a replicar

### 2.1 Patrón de importación (por documento)

```
[doc]Cabecera (grado/seccion/lapso…) ──(1)──> pevaluacion vigente (ancla)
        │
        ├──> estrategias (si aplica)        [hija sin pevaluacion]
        ├──> summaries/acts/positions       [hija CON pevaluacion → remapear]
        └──> reviews (si aplica)            [hija sin pevaluacion]
```

Pasos comunes de `importar()`:
1. Resolver la **carga vigente** del docente (lapso en curso, pestudio 6).
2. Filtrar los **candidatos** legacy por `(grado, seccion)` [o `(grado, seccion, lapso)`
   para evaluación] contra la carga vigente.
3. Crear la cabecera con **ID nuevo** anclada a la carga vigente.
4. Copiar **hijas sin pevaluacion** tal cual (estrategias, reviews).
5. **Remapear hijas con pevaluacion** (summaries/acts/positions) a la pevaluación
   vigente de misma asignatura+sección, u omitir con conteo.
6. **Anti-duplicado** por cabecera completa.
7. Todo en transacción; reporte `{creados, omitidos}`.

### 2.2 Patrón `pensum_id` (conveniencia en cabecera)

```
select pensum (opcional) ──> loadPensums($grado)  [solo pensums del docente]
                                  │
                                  └─> where pevaluaciones.profesor_id = docente
                                      where lapso = activo
                                      where pestudio = 6
```

Escopado por docente: un pensum que el docente no imparte **nunca** aparece en el
desplegable, y `save()` lo vuelve a comprobar.

### 2.3 Patrón filtro por área en el listado

```
select filtro "Área" ──> listPensumFiltro  [separada de listPensum del form]
                              │
                              ├─> mount: loadPensums() (todas las del docente)
                              ├─> updatedFilterGrado: loadPensums($grado) + limpia filterPensum
                              └─> query: where('pensum_id', $filterPensum) si hay selección
```

La lista del filtro es **distinta** de la del formulario (`listPensum`, en cascada por
el grado del form): compartirla haría que abrir el modal de edición cambiara las
opciones del filtro. Al cambiar de grado en los filtros, el área elegida se limpia
(un área de otro grado no puede quedar activa).

---

## 3. Roadmap de generalización

### Fase R1 — Extraer la base común de importación (servicio reutilizable)

**Objetivo**: convertir `ImportadorEiplanningwk` en una base `ImportadorDocumento` que
los 6 documentos extiendan, en vez de duplicar el servicio.

- Crear `app/Services/Inicial/ImportadorDocumento.php` (abstracta) con:
  - `cargasVigentes($profesorId)` (ya existe, igual para todos).
  - `origenDisponible()` (ya existe).
  - `candidatos($profesorId)` → **abstracto** (cada documento define su query de
    candidatos).
  - `importar($legacyIds, $profesorId)` → plantilla con los 7 pasos del §2.1, delegando
    en 4 métodos abstractos:
    - `tablaCabecera()` (tabla legacy y modelo destino).
    - `contextoDeCandidato($legacyRow)` → `{grado_id, seccion_id, lapso_id?}`.
    - `hijasSinPevaluacion()` → `[{tabla, fk, campos}]`.
    - `hijasConPevaluacion()` → `[{tabla, fk, campos}]` + `remapPevaluacion($legacyId)`.
- `ImportadorEiplanningwk` pasa a ser una subclase que solo declara esos 4 métodos.

**DoD**: `ImportadorEiplanningwk` sigue pasando sus 11 tests sin cambios de
comportamiento; existe la base abstracta con su propio test (mock de un documento
hipotético o el propio wk).

### Fase R2 — Replicar la importación a los documentos de "cabecera + hijas"

| Documento | Candidatos por | Hijas sin pevaluación | Hijas con pevaluación (remapear) | Particularidad |
|---|---|---|---|---|
| **eiplanningbwks** | (grado, sección) | `eiplanningbwstrategies` | `eiplanningbwsummaries` | igual que wk, sin `tiempo_ejecucion`? (verificar) |
| **eiprojectks** | (grado, sección) | `eiprojectkstrategies`, `eiprojectreviews` | `eiprojectsummaries` | el `reviews` se copia tal cual |
| **eispecialks** | (grado, sección) | `eispecialstrategies` | `eispecialacts` | cabecera usa `justificacion`, no `diagnostico` |
| **eievaluationks** | (grado, sección, **lapso**) | — | `eievaluationps` | cabecera ya tiene `lapso_id`; candidatos se filtran por lapso |
| **eifinalks** | **distinto** (por estudiante) | — | `eifinalk_expectation` (pivote de expectativas) | requiere `Estudiant` + áreas sembradas; **no** comparte el patrón de cabecera |

**DoD**: cada documento tiene su `Importador{Documento}` (extiende la base), su botón
"Importar de s2526", su wizard (o se reutiliza el parcial si es idéntico) y su test con
≥3 casos (crea, opcional, anti-duplicado, fuera de carga).

**Decisión a tomar**: el parcial `import-wizard.blade.php` es casi genérico salvo el
título y las columnas. Extraer `partials/import-wizard.blade.php` → `partials/import/
documento.blade.php` parametrizado por `$entidad` y `$titulo`, para no duplicar la UI.

### Fase R3 — Replicar `pensum_id` a las cabeceras donde tenga sentido

Solo a documentos con el patrón "cabecera con grado/sección + resúmenes":

| Documento | ¿pensum_id en cabecera? | Motivo |
|---|---|---|
| eiplanningbwks | ✅ | idéntico a wk |
| eiprojectks | ✅ | mismo patrón |
| eispecialks | ⚠️ | verificar (cabecera usa `justificacion`; las actividades llevan `pevaluacion_id`) |
| eievaluationks | ⚠️ | ya tiene `lapso_id`; las posiciones llevan `pevaluacion_id` |
| eifinalks | ❌ | patrón distinto (por estudiante; `pevaluacion_id` directo) |

**Reutilización**: extraer `loadPensums()` y el select a un trait `HasPensumCabecera`
(o a la base de componentes), ya que la lógica es idéntica.

**DoD**: migración aditiva por documento (nullable, `nullOnDelete`), select escopado,
validación de pertenencia y pruebas (guardar con área, opcional, desplegable sin áreas
ajenas, rechazo de área ajena).

### Fase R4 — Consolidación y pruebas

- **Refactor seguro**: mover `loadPensums`, `fechaInput`, `cargasVigentes` a la base
  común para eliminar duplicación entre los 5 componentes.
- **Test de fuga de alcance**: para cada documento, un test que confirme que un
  docente no ve/importa datos de otro.
- **Smoke**: ninguna ruta del módulo devuelve 5xx (ya existe `HomeInicialTest`).
- **Higiene**: sin `dd()`, sin `{!! !!}` sin escapar, sin `migrate:fresh`, sin CJK en
  strings.

### Fase R5 — `eifinalk` (patrón distinto, fuera del §2.1)

El informe final es **por estudiante** (`estudiant_id` + `pevaluacion_id`), no por
grado/sección. Su importación no comparte el ancla de cabecera: requeriría listar los
estudiantes del lapso y sus informes legacy. Se documenta como **caso aparte**; si se
quiere, se aborda tras R1–R4 con un análisis propio.

---

## 4. Checklist de validación (al terminar cada fase)

- [ ] Los tests del documento importado pasan (≥3 casos por documento).
- [ ] El wizard de importación se abre desde el documento correcto y lista los
      candidatos esperados.
- [ ] Importar crea el plan con ID nuevo y ancla a la carga vigente; las hijas con
      pevaluación se remapean u omiten.
- [ ] El anti-duplicado impide crear el mismo plan dos veces.
- [ ] El select de `pensum_id` solo muestra áreas del docente; guardar un área ajena
      da error sin persistir.
- [ ] Suite completa del módulo verde (`php8.2 artisan config:clear && php8.2 artisan
      test --group=inicial`).
- [ ] Sin duplicación de código: los métodos comunes viven en la base/trait, no copiados.
