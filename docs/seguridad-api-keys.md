# Guía: fuga de API keys (OpenRouter) — verificación y protección

> Última revisión: 2026-10-01. Proyecto `cfla`, producción en
> `uefrayluisamigosf.com`. Repo GitHub **`noemdb/cfla` (PÚBLICO)**.

---

## 0. Contención inmediata (hacer YA, en este orden)

Una key filtrada no se "desfiltra": lo único que la invalida es revocarla.

1. **Revoca la key en OpenRouter**: https://openrouter.ai/keys → Delete en la
   key comprometida. Todo lo que use esa key deja de funcionar al instante
   (incluidos atacantes).
2. **Crea una key nueva** y anota su actividad/uso antes de borrarla
   (modelos, costo, fechas) como evidencia del abuso.
3. **Actualiza SOLO el `.env` de producción** (`OPENROUTER_API_KEY=...`) y
   luego, en el servidor:
   ```bash
   php8.2 artisan config:clear
   php8.2 artisan queue:restart   # los workers cargan el .env al arrancar
   sudo systemctl restart supervisor  # o como corran queue/reverb
   ```
4. **Si hay señales de que se expuso el `.env` entero** (ver §2: página de
   debug visible o `/.env` descargable), rota **TODO**: `DB_PASSWORD`,
   `MAIL_*`, `REVERB_APP_KEY`, `APP_KEY` (¡cuidado! cambiar `APP_KEY`
   invalida sesiones y datos cifrados: hazlo en ventana de mantenimiento),
   tokens de Gmail/Resend y demás secretos del `.env`.

---

## 1. Lo ya verificado en este repo (2026-10-01)

| # | Punto | Resultado |
|---|-------|-----------|
| 1 | `.env` en git | ✅ Nunca commiteado (`.gitignore:9`) |
| 2 | Key real en historial git (`git log --all -S 'sk-or-v1-…'`) | ✅ Nunca commiteada |
| 3 | `DEPLOY_CHECKLIST.md` (borrado, en historial) | ✅ Solo placeholder `sk-or-...`, sin valor real |
| 4 | Key en el bundle JS (`VITE_*`, `resources/js`) | ✅ Limpio |
| 5 | Endpoints debug (`phpinfo`, telescope, debugbar) | ✅ No existen |
| 6 | Controladores que devuelvan `env()`/`config()` con secretos | ✅ Solo se exponen **nombres de modelos**, nunca keys |
| 7 | Keys en `storage/logs/*.log` | ✅ Limpio |
| 8 | Keys en query strings logueables | ⚠️ `GeminiService` manda `?key=…` (método oficial de Google, pero esas URLs **nunca deben loguearse**) |
| 9 | Headers `Authorization` logueados | ✅ Ningún servicio loguea headers |
| 10 | Handler de excepciones | ✅ Solo mensaje/archivo/línea a bitácora, sin env |
| 11 | Ruta de backup DB | ✅ Solo admin, descarga directa, fuera de `public/` |
| 12 | Archivos sueltos en `public/` (`.env`, `.sql`, `.log`) | ✅ Limpio |
| 13 | **`.env.backup` en la raíz (5.1 KB, con OTRA key real distinta a la del `.env`)** | 🔴 **Riesgo**: no está en git (lo cubre `.env.*`), pero es una copia en texto plano; si el deploy copia el proyecto entero al servidor, ambas keys viven en prod |
| 14 | **`.claude/settings.local.json` (gitignoreado, solo local) con keys reales en comandos `curl`** (`sk-or-v1-9f78…`, `sk-EOzsG9w4…` reutilizada en ~10 proveedores) | 🔴 **Riesgo**: vive en disco y casi seguro en `~/.bash_history` |
| 15 | Repo GitHub | 🔴 **PÚBLICO**: todo lo commiteado es legible por cualquiera |

Conclusión: el código **no** filtra la key por sí solo. Los vectores probables
están en el **servidor de producción** (§2) o en **copias sueltas de la key**
(§1.13, §1.14, historial del shell).

---

## 2. Verificación en el servidor de producción

Ejecutar **en el servidor** (o contra el dominio). Lo esperado va entre `[ ]`.

### 2.1. ¿Se descarga el `.env` por web? (sospechoso #1 si el docroot apunta mal)

```bash
for p in "/.env" "/.env.backup" "/.env.example" "/storage/logs/laravel.log" \
         "/server-status" "/server-info" "/phpinfo.php" "/info.php"; do
  printf '%-28s -> %s\n' "$p" \
    "$(curl -s -o /dev/null -w '%{http_code}' "https://uefrayluisamigosf.com$p")"
done
# [404 o 403 en TODO. Un 200 en /.env = fuga total confirmada]
```

### 2.2. ¿Está `APP_DEBUG` encendido en prod? (sospechoso #2: la página de error vuelca TODO el `.env`)

```bash
grep -E "^(APP_ENV|APP_DEBUG)=" .env
# [APP_ENV=production / APP_DEBUG=false]

# Y provocado desde fuera (debe dar una página genérica, SIN variables):
curl -s https://uefrayluisamigosf.com/ruta-que-no-existe-xyz | grep -ci "OPENROUTER\|DB_PASSWORD\|APP_KEY"
# [0]
```

### 2.3. Permisos y copias del `.env` en el servidor

```bash
ls -la .env*            # [.env en 600, dueño el usuario del deploy. 664 en hosting
                       #  compartido = otros usuarios del servidor pueden leerlo]
find /ruta/al/proyecto /ruta/a/backups -maxdepth 3 -name ".env*" 2>/dev/null
# [Solo debe existir UN .env. Cada .env.backup/.env.old/.env.prod es una copia
#  de TODOS los secretos: bórralos o muévelos fuera del proyecto]
```

### 2.4. Historial del shell y archivos del usuario deploy

```bash
grep -rhoE "sk-or-v1-[A-Za-z0-9_-]{10,}|sk-ant-[A-Za-z0-9_-]{10,}" ~/.bash_history ~/.zsh_history 2>/dev/null | sort -u | wc -l
# [0. Si >0, esas líneas (y la key) quedaron en texto plano: rótala y limpia]
```

### 2.5. Alcance del abuso (panel de OpenRouter)

- https://openrouter.ai/activity → modelos, costo y fechas que **tú no
  generaste** = ventana del abuso.
- https://openrouter.ai/keys → ¿hay keys que **no creaste tú**? Bórralas.
- Activa **límites de gasto** (spend limits) por key: aunque vuelva a filtrarse
  una key, el daño queda acotado.

---

## 3. Cómo identificar el vector con lo anterior

| Lo que ves | Vector probable |
|------------|-----------------|
| `/.env` devuelve 200 | Docroot mal apuntado (a la raíz en vez de `public/`) |
| Página de error muestra variables | `APP_DEBUG=true` en prod |
| Solo la key de OpenRouter tiene uso raro, el resto intacto | Copia suelta (settings, historial shell, snippet compartido) o la key se pegó en algún chat/log |
| Varias keys/secretos con uso raro | `.env` completo expuesto (debug o descarga directa) → rotar TODO (§0.4) |
| Key antigua (la del `.env.backup`) con uso | Esa copia llegó a algún servidor/servicio: rastrea dónde se desplegó |

---

## 4. Protección (endurecimiento)

**Servidor web** (elige según tu stack):

```apache
# Apache (.htaccess en la RAÍZ del proyecto o vhost): negar dotfiles
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```
```nginx
# nginx: dentro del server {}
location ~ /\. { deny all; access_log off; log_not_found off; }
location ~* \.(env|log|sql|bak|backup|old)$ { deny all; }
```

**Laravel / proyecto:**

```bash
# .env solo legible por su dueño
chmod 600 .env
# Nada de copias junto al proyecto: fuera del docroot o bórralas
rm -v .env.backup .env.old .env.prod 2>/dev/null
# Nunca más pegar keys en comandos: usa variables del shell
export OPENROUTER_API_KEY='sk-or-...'   # solo en memoria, no queda en el comando... 
# (ojo: igual queda en history; mejor: léela con read -s o desde un gestor de secretos)
```

**GitHub (repo público):**

1. Activa **Push protection** y **Secret scanning**:
   `Settings → Code security → Secret scanning + Push protection`.
2. Aunque el historial está limpio hoy, añade un hook pre-commit con
   [gitleaks](https://github.com/gitleaks/gitleaks) para que una key no pueda
   volver a commitearse ni por accidente.

**OpenRouter:**

- Una key por entorno (prod / local), con nombre que lo indique.
- Spend limits activados en cada key.
- Revisa `activity` semanalmente (o móntalo en tu rutina con
  `notifications:stats` como recordatorio operativo).

**Higiene local:**

- Borra las keys reales de `.claude/settings.local.json` (usa placeholders o
  variables de entorno en esos comandos de prueba).
- Limpia el historial del shell **después** de rotar:
  `history -c && history -w` (bash) y revisa `~/.bash_history`.
- La key de `.env.backup` es **distinta** a la del `.env`: verifica en el panel
  si sigue activa y revócala también.

---

## 5. Checklist post-incidente

- [ ] Key comprometida revocada en OpenRouter
- [ ] Key nueva solo en el `.env` de producción (+ `config:clear` + `queue:restart`)
- [ ] `APP_ENV=production`, `APP_DEBUG=false` verificados en el servidor
- [ ] `curl .../.env` → 404/403
- [ ] `.env` en 600, sin copias `.env.*` junto al proyecto
- [ ] Segunda key (la del `.env.backup`) verificada/revocada; archivo eliminado
- [ ] Keys de `.claude/settings.local.json` e historial del shell rotadas/limpiadas
- [ ] Spend limits activos en OpenRouter
- [ ] Push protection + secret scanning activos en GitHub
- [ ] Si hubo exposición del `.env` completo: rotados DB, mail, Reverb, APP_KEY y demás
