# MEJORAS AL BLUEPRINT TIMETABLE SOLVER — RESUMEN EJECUTIVO

> **Versión**: 2 (mejorado)  
> **Contexto**: CFLA (Laravel 10, módulo Inicial, Pest, Reverb, Pennant)  
> **Fecha**: 2026-09-13

---

## 📊 Matriz de 13 Mejoras

| # | Mejora | Problema Original | Solución Propuesta | Impacto | Esfuerzo | Prioridad |
|---|--------|---|---|---|---|---|
| 1 | **Precondiciones Inicial** | No menciona integración con módulo Inicial (FKs s2526→s2627, quirk lunes) | Sección 2.1: validar grid, precondiciones de BD, FK sync | Alto | Medio | 🔴 Crítico |
| 2 | **Testing sin migrate:fresh** | v1 asume ambiente dev; cfla prohibe migrate:fresh | Patrón Pest + DatabaseTransactions, seeders persistentes | Alto | Alto | 🔴 Crítico |
| 3 | **Broadcasting + Reverb** | Solo menciona logs; cfla usa Reverb para real-time | Eventos broadcast (SolverStarted, SolverCompleted), UI updates sin refresh | Medio | Medio | 🟡 Alto |
| 4 | **Feature Flags (Pennant)** | Sin control de rollout; riesgo de regresión inmediata | Pennant features + gradual rollout (20%→50%→100%), QualityGate automático | Alto | Medio | 🔴 Crítico |
| 5 | **Presupuesto dinámico** | Budget global único; planning vs coordinacion tienen SLAs diferentes | Config por contexto/rol: `budget_ms[planning.admin]=60s`, `budget_ms[preview]=15s` | Bajo | Bajo | 🟢 Medio |
| 6 | **DTOs tipadas + enums** | DTOs mencionados sin especificación PHP 8.2; error-prone | Enums (UnassignedReason), readonly classes, strict types | Medio | Medio | 🟡 Alto |
| 7 | **Permisos & Audit** | Sin considerar middleware (IsAdmin, IsDiagnostic) ni auditoría | Middleware checks en Job, tabla `timetable_solver_audits`, logging quién/cuándo/resultado | Medio | Medio | 🟡 Alto |
| 8 | **UX Error Handling** | UI "muestra motivos" pero no especifica implementation | Componente Blade con feedback accionable por `UnassignedReason` (diálogos, recomendaciones) | Medio | Bajo | 🟡 Alto |
| 9 | **Grid "lunes" quirk** | No valida asimetría del grid (lunes ≠ otros días) | Pre-check en `SchedulingContext`, log asymmetry, validación en `assignablePeriods` | Bajo | Bajo | 🟢 Medio |
| 10 | **Docs en docs/timetable/** | Sección 13 dice "documentado" sin estructura | 7 archivos md + diagrams (README, solver-arch, fallback, feasibility, repair, testing, observability) | Bajo | Bajo | 🟢 Medio |
| 11 | **Metrología & KPIs** | Sin métricas de éxito para v2; no hay baseline | Dashboard `/admin/metrics/timetable` (cobertura, tiempo, timeout %, repair success %) | Medio | Medio | 🟡 Alto |
| 12 | **Rollback strategy** | Si v2 degrada → sin plan de fallback | QualityGate automático, rollback si coverage < baseline - 3%, Slack alerts | Alto | Bajo | 🔴 Crítico |
| 13 | **Roadmap CFLA-specific** | Tickets v1 (FB-01..FB-15) genéricos, sin cfla context | 18 tickets (TT-CFP-01..18) con dependencias, deadlines, sprint assignment | Bajo | Bajo | 🟢 Medio |

---

## 🎯 Impact by Severity

### 🔴 Críticos (4): Antes de F1
- **Mejora 1**: Precondiciones Inicial → evita infactibilidad silenciosa
- **Mejora 2**: Testing safe → no romper BD, regresión testeable
- **Mejora 4**: Feature flags → rollout seguro, rollback automático
- **Mejora 12**: Rollback strategy → protección contra degradación

### 🟡 Altos (6): F1-F5
- Mejoras 3, 6, 7, 8, 11: Observabilidad, typing, permisos, UX, métricas
- Habilitan monitoreo en prod, debugging, user experience mejorada

### 🟢 Medios (3): F6+
- Mejoras 5, 9, 10, 13: Nice-to-have (presupuesto dinámico, quirks, docs, roadmap)
- Pulido final, documentación, knowledge transfer

---

## 📈 Roadmap Impactado

| Fase Original | Mejoras incorporadas | Duración | Cambio |
|---|---|---|---|
| **F0** | 1 (precondiciones), 2 (testing baseline) | 1 sprint | +1 semana (validaciones) |
| **F1** | 1, 2, 4, 6 (orquestador typed + Pennant setup) | 2 sprints | +0.5 sprints (enum/readonly) |
| **F2** | 3 (broadcasting en attemptCompleted) | 1 sprint | Sin cambio |
| **F3** | Ninguna | 1 sprint | Sin cambio |
| **F4** | 7, 8, 9 (audit, UX, quirks) | 1.5 sprints | +0.5 sprints |
| **F5** | 3, 8, 11 (eventos, UX integrada, metrics base) | 2 sprints | Sin cambio |
| **F6** | 4, 11, 12 (Pennant rollout, dashboard completo, QualityGate) | 2 sprints | Sin cambio |
| **F7** | 10, 13 (docs, roadmap close-out) | 1 sprint | Sin cambio |
| **Total** | **Todas 13** | **11 sprints** | **+1.5 sprints = 12 sprints (~3 meses)** |

---

## 🔗 Archivos Nuevos Requeridos

### Services (6):
```
app/Services/Timetable/Solver/
├── TimetableSolverOrchestrator.php          [Mejora 1,4,12]
├── SolverAttemptConfig.php                  [Mejora 6]
├── AttemptResult.php                        [Mejora 6]
├── SolverOutcome.php                        [Mejora 6]
├── UnassignedReason.php (enum)              [Mejora 6,8]
└── QualityGate.php                          [Mejora 12]
```

### Events (3):
```
app/Events/Timetable/
├── SolverStarted.php                        [Mejora 3]
├── SolverCompleted.php                      [Mejora 3]
└── SolverAttemptCompleted.php               [Mejora 3]
```

### Models & Listeners (4):
```
app/Models/Timetable/
├── SolverAudit.php                          [Mejora 7]
└── SolverRun.php                            [Mejora 11]

app/Listeners/
├── BroadcastSolverProgress.php              [Mejora 3]
└── LogTimetableSolverInvocation.php         [Mejora 7]
```

### Controllers (1):
```
app/Http/Controllers/Admin/
└── TimetableMetricsController.php           [Mejora 11]
```

### Database (2):
```
database/seeders/
├── TimetableTestDataSeeder.php              [Mejora 2]

database/migrations/
├── create_timetable_solver_audits_table     [Mejora 7]
└── create_timetable_solver_runs_table       [Mejora 11]
```

### Tests (2):
```
tests/
├── Feature/Timetable/SolverOrchestratorTest.php     [Mejora 2]
└── Unit/Timetable/TimetableSolverOrchestratorTest.php [Mejora 2]
```

### Views (1):
```
resources/views/
├── admin/timetable-metrics.blade.php       [Mejora 11]
└── (actualizar livewire/timetable-wizard-step5.blade.php) [Mejora 8]
```

### Docs (7):
```
docs/timetable/
├── README.md
├── solver-architecture.md
├── fallback-strategy.md
├── feasibility-report.md
├── repair-phase.md
├── testing-guide.md
├── observability.md
└── troubleshooting.md
```

---

## ⚙️ Archivos Modificados

| Archivo | Mejoras | Cambios |
|---------|---------|---------|
| `app/Jobs/Timetable/GenerateTimetableJob.php` | 1,4,5,7 | Invocar orquestador, feature flag, audit, presupuesto dinámico |
| `app/Services/Timetable/Solver/TimetableSolver.php` | 6 | Aceptar SolverAttemptConfig en lugar de parámetros sueltos |
| `config/timetable.php` | 1,5,6 | Budget dinámico, enums, precondiciones |
| `resources/views/livewire/timetable-wizard-step5.blade.php` | 8 | UX mejorada, feedback accionable |
| `app/Livewire/TimetableWizard.php` | 8 | Mostrar motivos, acciones recomendadas |
| `routes/web.php` | 7,11 | Rutas metrics, feature flags admin |

---

## 📋 Checklist de Aprobación

Para proceder a implementation:

- [ ] **Mejora 1**: Confirmar integración con módulo Inicial (quirk lunes, FK sync)
- [ ] **Mejora 2**: Confirmar patrón Pest + DatabaseTransactions es compatible con setup cfla
- [ ] **Mejora 4**: Confirmar Pennant está enabled en cfla (Laravel 10+)
- [ ] **Mejora 7**: Definir policy de retención de auditoría (30 días? Archive?)
- [ ] **Mejora 11**: Confirmar acceso `/admin/metrics` está restringido a is_admin
- [ ] **Mejora 12**: Confirmar Slack webhook para alertas está configurado
- [ ] **Roadmap**: Asignar tickets a sprints, revisar deadlines

---

## 🚀 Próximos Pasos

1. **Esta semana**: User review de mejoras, ajustes
2. **Próxima semana**: Kickoff F0 (TT-CFP-01) → baselining
3. **Sprint N+1**: F1 (orquestador, typing, Pennant setup)
4. **3 meses**: v2 en producción con 100% rollout

---

**Documento**: MEJORAS-SUMMARY.md  
**Generado por**: Claude Code  
**Fecha**: 2026-09-13  
**Próxima revisión**: Post-aprobación del usuario
