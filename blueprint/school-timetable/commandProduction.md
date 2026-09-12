comando para produccion:

php artisan migrate

php8.2 artisan db:seed --class=TimetableShiftsSeeder --force

php8.2 artisan timetable:create-section-rooms --pestudio=1 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=2 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=4 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=6 --capacity=40

php8.2 artisan timetable:import-legacy --lapso=1 --csv-dir="blueprint/school-timetable/legacy/csv" --replace --strategy=optimized

php8.2 artisan timetable:backfill-horas --lapso=1 --force

# Fuente normativa recomendada para horas teóricas/prácticas.
php8.2 artisan timetable:normalize-legacy-hours --lapso=1 --dry-run
php8.2 artisan timetable:normalize-legacy-hours --force --lapso=1

# `timetable:backfill-horas` permanece disponible por compatibilidad y muestra
# una advertencia de deprecación; no usarlo para nuevas normalizaciones.



php8.2 artisan optimize:clear                                                                                                                                                 
php8.2 artisan config:cache                                                                                                                                                   
php8.2 artisan route:cache                                                                                                                                                    
php8.2 artisan view:cache 