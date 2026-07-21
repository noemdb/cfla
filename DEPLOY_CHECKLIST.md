# Checklist de Despliegue — Seguridad XSS + KaTeX Math

> Despliegue de parches de seguridad (DOMPurify, sanitizador server-side, KaTeX CVE-2025-1390) y generación de contenido matemático con LaTeX.

---

## ⚙️ Pre-despliegue (preparación en local)

### 1. Variables de entorno

Agregar al `.env` de **producción**:

```bash
# Modelos para generación de expresiones matemáticas (LaTeX)
OPENROUTER_MODEL_MATH_PRIMARY=qwen/qwen3-coder-flash
OPENROUTER_MODEL_MATH_FALLBACK1=deepseek/deepseek-v4-flash
```

Si no se configuran, usan estos defaults (funcionales pero revisar cuota):
| Variable | Default | Alternativas recomendadas |
|----------|---------|--------------------------|
| `OPENROUTER_MODEL_MATH_PRIMARY` | `qwen/qwen3-coder-flash` | `qwen/qwen3-coder-flash`, `google/gemini-2.5-flash-preview` |
| `OPENROUTER_MODEL_MATH_FALLBACK1` | `deepseek/deepseek-v4-flash` | `nvidia/nemotron-3-nano-30b-a3b` |

Verificar también que existan:
```bash
OPENROUTER_API_KEY=sk-or-...
OPENROUTER_MODEL_FALLBACK4=anthropic/claude-sonnet-4       # fallback general (existe?)
```

### 2. Verificar dependencias npm

```bash
# Asegurar que kaTeX y DOMPurify están en package-lock.json
npm ls katex dompurify
# → katex@0.16.11, dompurify@3.4.12
```

### 3. Compilar assets

```bash
npm run build
# → Verificar en public/build/ que los archivos nuevos aparecen
#   - assets/mermaid-*.js (chunk dinámico de Mermaid/KaTeX)
#   - assets/app-*.js (DOMPurify va incluido aquí)
#   - manifest.json actualizado
```

### 4. Verificar archivos nuevos

Confirmar que estos archivos existen y están trackeados (o se agregan en el commit):

```
app/Services/Lms/LmsHtmlSanitizerService.php    ← NUEVO
resources/views/components/lms/math-text.blade.php  ← NUEVO
config/openrouter.php                           ← MODIFICADO (2 nuevas keys)
resources/js/lms-student-preview.js             ← MODIFICADO (parches)
app/Livewire/Profesor/Lms/LessonWizard.php      ← MODIFICADO
resources/views/livewire/profesor/lms/lesson-wizard.blade.php  ← MODIFICADO
resources/views/components/lms/student-preview.blade.php      ← MODIFICADO
```

### 5. Commit

```bash
git add -A
git commit -m "feat: math LaTeX generation + XSS security patches

- DOMPurify client-side sanitization (XSS prevention)
- LmsHtmlSanitizerService server-side defense-in-depth
- KaTeX integration with dynamic Vite chunk
- CVE-2025-1390 patch for \htmlData macros
- <x-lms.math-text> component with 4-layer security
- GenerateSlideMath() with model chain (primary + fallback)
- OpenRouter config for math models

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>
"
```

---

## 🚀 Despliegue en producción

### 6. Push + Deploy

```bash
git push origin main
# Si hay CI/CD: se despliega automáticamente
# Si es manual:
ssh usuario@produccion
cd /ruta/del/proyecto
git pull origin main
```

### 7. Dependencias (producción)

```bash
# Sin acceso a internet? Verificar que node_modules existe
# Con acceso:
npm ci --production --no-optional
# O si se despliegan los assets pre-construidos:
# Saltar npm install, los assets ya están en public/build/
```

### 8. Compilar assets (producción)

**Opción A — Assets pre-construidos (recomendada)**: Si los assets se construyeron en local y se subieron al repo (public/build/):
```bash
# Solo verificar que manifest.json tenga las nuevas entries:
grep -c "katex\|dompurify\|math-content\|math-text" public/build/manifest.json
```

**Opción B — Build en producción**:
```bash
npm run build
```

### 9. Limpiar cachés de Laravel

```bash
php artisan optimize:clear    # Limpia todo (view, config, route, cache)
php artisan optimize          # Re-optimiza (config, route, events)
php artisan view:cache        # Compila vistas Blade (mejora rendimiento)
```

> ⚠️ **Importante**: Después de `php artisan optimize`, los cambios en archivos Blade y config NO se reflejan hasta re-ejecutar el comando. Si se necesita hotfix, ejecutar solo `php artisan optimize:clear` y `php artisan view:cache`.

### 10. Verificar que el storage tenga permisos correctos

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # ajustar usuario según servidor
```

---

## ✅ Post-despliegue (verificación)

### 11. Consola del navegador

Abrir el LessonWizard (`/profesors/lms/activity/lesson/new`):

- [ ] **No hay errores 404** en la consola (chunks de Vite cargando)
- [ ] **No hay errores JS** relacionados con KaTeX, DOMPurify o Alpine
- [ ] **Red → JS**: Verificar que `assets/mermaid-*.js` se carga dinámicamente
- [ ] **Red → JS**: Verificar que `assets/app-*.js` contiene el bundle principal

### 12. Prueba funcional — Generar contenido matemático

En el paso 2 del wizard:

- [ ] Escribir contenido con expresiones matemáticas (ej: "La fórmula cuadrática es x = (-b ± √(b² - 4ac)) / 2a")
- [ ] Hacer clic en **"Etiquetar Matemáticas"**
- [ ] Esperar que el overlay de carga desaparezca
- [ ] Verificar que la previsualización muestra el contenido con LaTeX renderizado
- [ ] Cambiar a la pestaña "Editor" y verificar que el HTML contiene `<div id="math-block"><p>...\(expresión\)...</p></div>`

### 13. Prueba funcional — Vista estudiante

En el paso 4 del wizard:

- [ ] Abrir "Vista Estudiante"
- [ ] Verificar que el contenido matemático se renderiza con KaTeX (símbolos, fracciones, etc.)
- [ ] Verificar que no hay errores en consola

### 14. Prueba de seguridad — XSS

- [ ] En el editor de contenido, pegar: `<script>alert(1)</script><img src=x onerror=alert(1)>`
- [ ] Verificar que NO se ejecuta la alerta (DOMPurify + sanitizador server-side)
- [ ] En la previsualización, verificar que aparece como texto plano (o no aparece)

### 15. Prueba de degradación gradual

- [ ] Bloquear temporalmente `assets/mermaid-*.js` en DevTools (Network → Offline)
- [ ] Verificar que el contenido se muestra sin símbolos matemáticos (degradación controlada)
- [ ] Verificar que no hay errores fatales en consola

### 16. Logs del servidor

```bash
tail -f storage/logs/laravel.log
# Verificar que no hay:
#   - "Class 'App\Services\Lms\LmsHtmlSanitizerService' not found"
#   - Errores de blade: "Undefined component: lms.math-text"
#   - Errores de Vite manifest
```

---

## 🔁 Rollback

Si algo sale mal, revertir el despliegue:

### Rollback rápido (revertir commit)

```bash
git revert HEAD --no-edit
git push origin main
```

### Rollback completo (reset a commit anterior)

```bash
git log --oneline -5
git reset --hard <commit-hash-anterior>
git push origin main --force   # ⚠️ Con cuidado, solo si equipo pequeño
```

### Post-rollback

```bash
php artisan optimize:clear
php artisan view:cache
npm run build    # Reconstruir assets sin los cambios
```

---

## 📋 Monitoreo posterior (primeras 24h)

| Qué monitorear | Dónde | Frecuencia |
|----------------|-------|------------|
| Errores 500 | logs de Laravel, Pulse | Cada hora |
| Errores JS | Consola del navegador (reportes de usuarios) | Bajo demanda |
| Uso de modelos OpenRouter | Dashboard de OpenRouter (créditos) | Diario |
| Fragmentación de chunks Vite | Network tab / Lighthouse | Post-despliegue |
| Alertas de seguridad | Logs de errores 500, reportes de usuarios | Inmediato |

---

## 🧹 Post-estabilización

Cuando haya red para actualizar paquetes:

```bash
# Actualizar KaTeX para remover parche manual de CVE-2025-1390
npm install katex@^0.16.21 --save

# Reconstruir assets
npm run build

# Eliminar las líneas del parche manual en:
#   resources/js/lms-student-preview.js
#   resources/views/components/lms/math-text.blade.php
# Buscar: "Parchar vulnerabilidad conocida de KaTeX" / "CVE-2025-1390"

# Auditoría de dependencias
composer audit
npm audit

# Corregir dependencias transitivas (low severity)
npm audit fix
```
