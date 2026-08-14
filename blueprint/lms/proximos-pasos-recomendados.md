# �� 📋 Próximos Pasos Recomendados (Según el Plan)

El documento de mejora establece un orden claro basado en esfuerzo vs valor:

## Fase Inmediata (Alto Valor, Esfuerzo Medio-Bajo)

1. **Opción 11 - Emisión Centralizada** (M/A): Corregir H1 moviendo la emisión a `LmsPublicationService::publish()` - **PREREQUISITO** para muchas otras mejoras
2. **Opción 0 - Higiene** (XS/M): Eliminar código muerto y duplicación (listeners para eventos no emitidos, `$listeners` redundante)
3. **Opción 4 - Badge Clicable** (XS/M): Hacer que el badge enlace al monitor filtrado por SCHEDULED
4. **Opción 1 - Toast + Sonido** (S/M): Notificaciones UX mejoradas en tiempo real

## Fase de Corrección Crítica (Alto Valor, Esfuerzo Medio)

5. **Opción 9 - Crash-Guard + Cola/Backoff** (M/A): Solucionar H2 mediante try/catch o migración a `ShouldBroadcast` con job de respaldo
6. **Opción 5 - Marcado como Leído** (M/A): Implementar persistencia de estado leído (tabla `user_lesson_reads`)
7. **Opción 2 - Contador por Rol/Scope** (M/A): Corregir H3 aplicando filtros de rol y scope de coordinación en la query

## Mejoras de Experiencia y Arquitectura (Valor Medio)

8. **Opción 3 - Poll Configurable + Cobertura Navbars** (S/M): Expandir badge a todos los navbars (móvil y otros roles) + hacer poll configurable
9. **Opción 6 - Monitor en Vivo** (M/A): Actualizar ítems del monitor sin recargar
10. **Opción 8 - Presencia Online** (S/M): Mostrar quién está viendo el monitor vía PresenceChannel
11. **Opción 10 - Auditoría + Métricas** (M/M): Logging de eventos y estadísticas de entrega
12. **Opción 7 - Redis Multi-tenant** (L/M): Escalabilidad horizontal (solo si se requiere multi-equipo)

## �� 🎯 Conclusión

La implementación base del sistema de notificaciones en tiempo real está funcionando y ha sido verificada mediante tests, pero presenta limitaciones significativas identificadas mediante auditoría de código que afectan su robustez, consistencia y experiencia de usuario.

El estado actual es: � ✅ Funcional pero con deuda técnica crítica y gaps de funcionalidad que requieren atención antes de considerar el sistema "completo" según los estándares de calidad del proyecto.

La prioridad técnica recomendada es comenzar por la **Opción 11 (Emisión Centralizada)** porque:
- Corrige el **hueco funcional real** (H1) donde solo el flujo del profesor notifica
- Es **prerequisito** para las mejoras de contador por rol (Opción 2) y monitor en vivo (Opción 6)
- Tiene alto valor con esfuerzo medio según la matriz de priorización

Una vez completada la centralización, las siguientes opciones de mayor impacto serían abordar el riesgo de crash (Opción 9) y mejorar la relevancia del contador mediante filtrado por rol/scope (Opción 2).