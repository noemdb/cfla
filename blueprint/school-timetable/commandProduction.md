comando para produccion:

php artisan migrate

php8.2 artisan db:seed --class=TimetableShiftsSeeder --force

php8.2 artisan timetable:create-section-rooms --pestudio=1 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=2 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=4 --capacity=40
php8.2 artisan timetable:create-section-rooms --pestudio=6 --capacity=40

php8.2 artisan timetable:import-legacy --lapso=1 --csv-dir="blueprint/school-timetable/legacy/csv" --replace

php8.2 artisan timetable:backfill-horas --lapso=1 --force

php8.2 artisan timetable:normalize-legacy-hours --force --lapso=1