<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mapa de correcciones ortográficas, de acentuación, concordancia y espaciado
     * por columna: [valor_original, valor_corregido, ids_originales_para_down].
     *
     * Nota: la tabla `asignaturas` usa `utf8mb4_unicode_ci` (insensible a tildes y
     * mayúsculas), por lo que en MySQL las comparaciones en `up()` y `down()`
     * emplean `BINARY` para asegurar coincidencias exactas.
     *
     * @var array<string, list<array{0: string, 1: string, 2: list<int>}>>
     */
    private const CORRECTIONS = [
        'name' => [
            ['EDUCACION ESTETICA', 'EDUCACIÓN ESTÉTICA', [98]],
            ['EDUCACION FÍSICA', 'EDUCACIÓN FÍSICA', [46, 55, 64, 74, 85, 193, 195, 196, 197, 198]],
            ['FORMACION HUMANO CRISTIANA', 'FORMACIÓN HUMANO CRISTIANA', [116, 117, 118, 119, 120, 121, 252, 253, 254, 255, 256]],
            ['FORMACION PARA LA SOBERANÍA NACIONAL', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL', [79, 105]],
            ['GEOGRAFÍA  HISTORIA Y CIUDADANIA', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA', [49, 58, 68, 78]],
            ['GEOGRAFÍA HISTORIA Y CIUDADANIA', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA', [106]],
            ['GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL', [129, 216, 217, 218, 219]],
            ['INGLES Y OTRAS LENGUAS EXTRANJERAS', 'INGLÉS Y OTRAS LENGUAS EXTRANJERAS', [44, 53, 62, 72, 83]],
            ['INNOVACION TECNOLÓGICA Y PRODUCTIVA', 'INNOVACIÓN TECNOLÓGICA Y PRODUCTIVA', [130, 228, 263, 264]],
            ['MATEMATICAS', 'MATEMÁTICAS', [97, 99]],
            ['PARTICIPACION EN GRUPOS DE C.R.P.', 'PARTICIPACIÓN EN GRUPOS DE C.R.P.', [51, 60, 70, 81, 108, 220]],
            ['ROBOTICA', 'ROBÓTICA', [122]],
            ['ÁREA COMPLEMENTARIA  FORMACION HUMANO CRISTIANA', 'ÁREA COMPLEMENTARIA FORMACIÓN HUMANO CRISTIANA', [7]],
            ['ÁREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA', 'ÁREA COMPLEMENTARIA FORMACIÓN HUMANO CRISTIANA', [14, 28, 35, 42]],
            ['ÁREA COMPLEMENTARIA INGLES', 'ÁREA COMPLEMENTARIA INGLÉS', [111, 112, 114, 115]],
            ['ÁREA COMPLEMENTARIA MUSICA', 'ÁREA COMPLEMENTARIA MÚSICA', [109, 229, 230, 231, 232, 233]],
            ['ÁREAS COMPLEMENTARIA INGLES', 'ÁREA COMPLEMENTARIA INGLÉS', [110, 113]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 1G', 'ÁREA COMPLEMENTARIA ROBÓTICA 1G', [132]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 2G', 'ÁREA COMPLEMENTARIA ROBÓTICA 2G', [133]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 3G', 'ÁREA COMPLEMENTARIA ROBÓTICA 3G', [134]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 4G', 'ÁREA COMPLEMENTARIA ROBÓTICA 4G', [135]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 5G', 'ÁREA COMPLEMENTARIA ROBÓTICA 5G', [136]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 6G', 'ÁREA COMPLEMENTARIA ROBÓTICA 6G', [137]],
        ],
        'observations' => [
            ['BIOLOGIA, AMBIENTE Y TECNOLOGIA', 'BIOLOGÍA, AMBIENTE Y TECNOLOGÍA', [125]],
            ['BIOLOGIA, AMBIENTE Y TECNOLOGIA 2DO AÑO', 'BIOLOGÍA, AMBIENTE Y TECNOLOGÍA 2DO AÑO', [199]],
            ['BIOLOGIA, AMBIENTE Y TECNOLOGIA 3ER AÑO', 'BIOLOGÍA, AMBIENTE Y TECNOLOGÍA 3ER AÑO', [200]],
            ['BIOLOGIA, AMBIENTE Y TECNOLOGIA 4TO AÑO', 'BIOLOGÍA, AMBIENTE Y TECNOLOGÍA 4TO AÑO', [201]],
            ['BIOLOGIA, AMBIENTE Y TECNOLOGIA 5TO AÑO', 'BIOLOGÍA, AMBIENTE Y TECNOLOGÍA 5TO AÑO', [202]],
            ['EDUCACION FISICA 1ER AÑO', 'EDUCACIÓN FÍSICA 1ER AÑO', [193]],
            ['EDUCACION FISICA 2DO AÑO', 'EDUCACIÓN FÍSICA 2DO AÑO', [195]],
            ['EDUCACION FISICA 3ER AÑO', 'EDUCACIÓN FÍSICA 3ER AÑO', [196]],
            ['EDUCACION FISICA 4TO AÑO', 'EDUCACIÓN FÍSICA 4TO AÑO', [197]],
            ['EDUCACION FISICA 5TO AÑO', 'EDUCACIÓN FÍSICA 5TO AÑO', [198]],
            ['FISICA 1ER AÑO', 'FÍSICA 1ER AÑO', [126]],
            ['FISICA 2DO AÑO', 'FÍSICA 2DO AÑO', [203, 204]],
            ['FISICA 3ER AÑO', 'FÍSICA 3ER AÑO', [205]],
            ['FISICA 4TO AÑO', 'FÍSICA 4TO AÑO', [206]],
            ['FISICA 5TO AÑO', 'FÍSICA 5TO AÑO', [207]],
            ['FORMACION HUMANO CRISTIANA', 'FORMACIÓN HUMANO CRISTIANA', [252, 253, 254, 255, 256]],
            ['FORMACIÓN PARA LA SOBERANIA NACIONAL 1ER AÑO', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL 1ER AÑO', [146, 147]],
            ['FORMACIÓN PARA LA SOBERANIA NACIONAL 2DO AÑO', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL 2DO AÑO', [157, 158]],
            ['FORMACIÓN PARA LA SOBERANIA NACIONAL 3ER AÑO', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL 3ER AÑO', [168, 169]],
            ['FORMACIÓN PARA LA SOBERANIA NACIONAL 4TO AÑO', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL 4TO AÑO', [179, 180]],
            ['FORMACIÓN PARA LA SOBERANIA NACIONAL 5TO AÑO', 'FORMACIÓN PARA LA SOBERANÍA NACIONAL 5TO AÑO', [190, 191]],
            ['GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL', [129]],
            ['GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL 2DO AÑO', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL 2DO AÑO', [216]],
            ['GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL 3ER AÑO', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL 3ER AÑO', [217]],
            ['GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL 4TO AÑO', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL 4TO AÑO', [218]],
            ['GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL 5TO AÑO', 'GEOGRAFÍA, HISTORIA Y SOBERANÍA NACIONAL 5TO AÑO', [219]],
            ['GEOGRAFÍA, HISTORIA Y CIUDADANÍA  1ER AÑO', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA 1ER AÑO', [145]],
            ['GEOGRAFÍA, HISTORIA Y CIUDADANÍA  2DO AÑO', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA 2DO AÑO', [156]],
            ['GEOGRAFÍA, HISTORIA Y CIUDADANÍA  3ER AÑO', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA 3ER AÑO', [167]],
            ['GEOGRAFÍA, HISTORIA Y CIUDADANÍA  4TO AÑO', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA 4TO AÑO', [178]],
            ['GEOGRAFÍA, HISTORIA Y CIUDADANÍA  5TO AÑO', 'GEOGRAFÍA, HISTORIA Y CIUDADANÍA 5TO AÑO', [189]],
            ['INNOVACION TECNOLOGIA Y PRODUCTIVA', 'INNOVACIÓN TECNOLÓGICA Y PRODUCTIVA', [130]],
            ['INNOVACION TECNOLOGICA Y PRODUCTIVA', 'INNOVACIÓN TECNOLÓGICA Y PRODUCTIVA', [228]],
            ['ORIENTACION VOCACIONAL', 'ORIENTACIÓN VOCACIONAL', [131]],
            ['ORIENTACION VOCACIONAL 2DO AÑO', 'ORIENTACIÓN VOCACIONAL 2DO AÑO', [221]],
            ['ORIENTACION VOCACIONAL 3ER AÑO', 'ORIENTACIÓN VOCACIONAL 3ER AÑO', [222]],
            ['ORIENTACION VOCACIONAL 4TO AÑO', 'ORIENTACIÓN VOCACIONAL 4TO AÑO', [223]],
            ['ORIENTACION VOCACIONAL 5TO AÑO', 'ORIENTACIÓN VOCACIONAL 5TO AÑO', [224]],
            ['PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION 5TO AÑO', 'PARTICIPACIÓN EN GRUPOS DE CREACIÓN, RECREACIÓN Y PRODUCCIÓN 5TO AÑO', [220]],
            ['PARTICIPACIÓN EN GRUPOS DE CREACIÓN RECREACIÓN Y PRODUCCIÓN', 'PARTICIPACIÓN EN GRUPOS DE CREACIÓN, RECREACIÓN Y PRODUCCIÓN', [51, 60, 70, 81, 108]],
            ['QUIMICA 1ER AÑO', 'QUÍMICA 1ER AÑO', [124]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 1G', 'ÁREA COMPLEMENTARIA INTEGRAL 1G', [257]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 2G', 'ÁREA COMPLEMENTARIA INTEGRAL 2G', [258]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 3G', 'ÁREA COMPLEMENTARIA INTEGRAL 3G', [259]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 4G', 'ÁREA COMPLEMENTARIA INTEGRAL 4G', [260]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 5G', 'ÁREA COMPLEMENTARIA INTEGRAL 5G', [261]],
            ['ÁREAS COMPLEMENTARIA INTEGRAL 6G', 'ÁREA COMPLEMENTARIA INTEGRAL 6G', [262]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 1G', 'ÁREA COMPLEMENTARIA ROBÓTICA 1G', [132]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 2G', 'ÁREA COMPLEMENTARIA ROBÓTICA 2G', [133]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 3G', 'ÁREA COMPLEMENTARIA ROBÓTICA 3G', [134]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 4G', 'ÁREA COMPLEMENTARIA ROBÓTICA 4G', [135]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 5G', 'ÁREA COMPLEMENTARIA ROBÓTICA 5G', [136]],
            ['ÁREAS COMPLEMENTARIA ROBÓTICA 6G', 'ÁREA COMPLEMENTARIA ROBÓTICA 6G', [137]],
        ],
        'prelacions' => [
            ['2do GRADO', '2DO GRADO', [19]],
        ],
    ];

    public function up(): void
    {
        $isMySql = in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);

        DB::transaction(function () use ($isMySql): void {
            foreach (self::CORRECTIONS as $column => $entries) {
                foreach ($entries as [$from, $to]) {
                    $query = DB::table('asignaturas');

                    if ($isMySql) {
                        $query->whereRaw("BINARY `{$column}` = ?", [$from]);
                    } else {
                        $query->where($column, $from);
                    }

                    $query->update([$column => $to]);
                }
            }
        });
    }

    public function down(): void
    {
        $isMySql = in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);

        DB::transaction(function () use ($isMySql): void {
            foreach (self::CORRECTIONS as $column => $entries) {
                foreach ($entries as [$from, $to, $ids]) {
                    $query = DB::table('asignaturas')->whereIn('id', $ids);

                    if ($isMySql) {
                        $query->whereRaw("BINARY `{$column}` = ?", [$to]);
                    } else {
                        $query->where($column, $to);
                    }

                    $query->update([$column => $from]);
                }
            }
        });
    }
};
