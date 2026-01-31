#!/bin/bash

echo "=== Verificando archivos comprometidos ==="
echo ""

echo "1. Buscando 'slot gacor' en archivos públicos:"
grep -r "slot gacor" public/ 2>/dev/null | head -20

echo ""
echo "2. Verificando integridad de archivos WireUI:"
ls -lah public/vendor/wireui/assets/ 2>/dev/null

echo ""
echo "3. Buscando archivos PHP sospechosos modificados recientemente:"
find public/ -name "*.php" -mtime -30 -ls 2>/dev/null

echo ""
echo "4. Verificando .htaccess:"
cat public/.htaccess 2>/dev/null | grep -v "^#" | grep -v "^$"

echo ""
echo "5. Buscando archivos con permisos 777 (inseguros):"
find public/ -type f -perm 0777 -ls 2>/dev/null

echo ""
echo "6. Verificando archivos index.php:"
md5sum public/index.php 2>/dev/null
