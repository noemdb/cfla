<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EILearningSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        // Grupo 1 (3-4 años)
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Formación personal, social y comunicación',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Comparte objetos y alimentos con sus pares y adultos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se integra progresivamente a juegos colectivos con respeto y afecto.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce normas básicas de cortesía: saludar, agradecer, disculparse.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Expresa emociones mediante gestos, palabras o juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones y actividades comunitarias escolares.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Relación con el ambiente',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora el entorno inmediato identificando elementos naturales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en el cuidado de plantas y animales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce cambios en el clima y estaciones del año.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Distingue entre objetos naturales y artificiales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta interés por fenómenos de la naturaleza.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Lenguaje oral y escrito',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se comunica usando frases sencillas para expresar necesidades.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce imágenes y símbolos en cuentos o textos ilustrados.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Anticipa contenido de cuentos por ilustraciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en juegos de rimas y canciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Inventa cuentos o anécdotas simples a partir de experiencias.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Procesos lógico-matemáticos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Clasifica objetos por color, forma o tamaño.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Cuenta objetos hasta cinco o más.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica relaciones espaciales básicas (arriba, abajo, cerca...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Agrupa elementos según cantidad o similitud.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce figuras geométricas básicas: círculo, cuadrado, triángulo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Expresión plástica, corporal y musical',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza materiales como plastilina, témperas o papel para crear.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Baila y canta al ritmo de canciones infantiles.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Representa personas u objetos mediante dibujos simples.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Coordina movimientos con música o instrumentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora sonidos del cuerpo y objetos para crear ritmos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Imitación y juegos de roles',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Imita acciones de personas conocidas (familiares, docentes).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en dramatizaciones de cuentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Crea personajes e historias en juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza disfraces y materiales para representar roles sociales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Asume roles de grupo en situaciones de juego organizado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Educación vial y ciudadana',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de tránsito básicas (alto, paso peatonal...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Aplica normas de seguridad al transitar por la calle.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en simulacros de emergencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Conoce los colores del semáforo y su significado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Demuestra respeto por normas de convivencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Salud integral y hábitos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Practica hábitos de higiene personal diariamente.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica alimentos saludables y no saludables.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en rutinas de alimentación, descanso y aseo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de malestar y pide ayuda.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Colabora con la limpieza y orden del aula.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 22,
            'name' => 'Identidad, historia y cultura',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Dice su nombre, apellido y el de familiares cercanos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica símbolos patrios como bandera, himno y escudo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones patrias o tradicionales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce personajes históricos locales en cuentos o murales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta orgullo por su comunidad y cultura.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Grupo 2 (4-5 años)
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Formación personal, social y comunicación',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Comparte objetos y alimentos con sus pares y adultos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se integra progresivamente a juegos colectivos con respeto y afecto.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce normas básicas de cortesía: saludar, agradecer, disculparse.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Expresa emociones mediante gestos, palabras o juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones y actividades comunitarias escolares.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Relación con el ambiente',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora el entorno inmediato identificando elementos naturales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en el cuidado de plantas y animales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce cambios en el clima y estaciones del año.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Distingue entre objetos naturales y artificiales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta interés por fenómenos de la naturaleza.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Lenguaje oral y escrito',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se comunica usando frases sencillas para expresar necesidades.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce imágenes y símbolos en cuentos o textos ilustrados.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Anticipa contenido de cuentos por ilustraciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en juegos de rimas y canciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Inventa cuentos o anécdotas simples a partir de experiencias.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Procesos lógico-matemáticos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Clasifica objetos por color, forma o tamaño.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Cuenta objetos hasta cinco o más.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica relaciones espaciales básicas (arriba, abajo, cerca...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Agrupa elementos según cantidad o similitud.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce figuras geométricas básicas: círculo, cuadrado, triángulo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Expresión plástica, corporal y musical',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza materiales como plastilina, témperas o papel para crear.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Baila y canta al ritmo de canciones infantiles.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Representa personas u objetos mediante dibujos simples.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Coordina movimientos con música o instrumentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora sonidos del cuerpo y objetos para crear ritmos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Imitación y juegos de roles',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Imita acciones de personas conocidas (familiares, docentes).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en dramatizaciones de cuentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Crea personajes e historias en juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza disfraces y materiales para representar roles sociales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Asume roles de grupo en situaciones de juego organizado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Educación vial y ciudadana',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de tránsito básicas (alto, paso peatonal...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Aplica normas de seguridad al transitar por la calle.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en simulacros de emergencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Conoce los colores del semáforo y su significado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Demuestra respeto por normas de convivencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Salud integral y hábitos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Practica hábitos de higiene personal diariamente.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica alimentos saludables y no saludables.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en rutinas de alimentación, descanso y aseo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de malestar y pide ayuda.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Colabora con la limpieza y orden del aula.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 23,
            'name' => 'Identidad, historia y cultura',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Dice su nombre, apellido y el de familiares cercanos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica símbolos patrios como bandera, himno y escudo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones patrias o tradicionales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce personajes históricos locales en cuentos o murales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta orgullo por su comunidad y cultura.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Grupo 3 (5-6 años)
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Formación personal, social y comunicación',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Comparte objetos y alimentos con sus pares y adultos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se integra progresivamente a juegos colectivos con respeto y afecto.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce normas básicas de cortesía: saludar, agradecer, disculparse.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Expresa emociones mediante gestos, palabras o juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones y actividades comunitarias escolares.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Relación con el ambiente',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora el entorno inmediato identificando elementos naturales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en el cuidado de plantas y animales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce cambios en el clima y estaciones del año.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Distingue entre objetos naturales y artificiales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta interés por fenómenos de la naturaleza.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Lenguaje oral y escrito',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Se comunica usando frases sencillas para expresar necesidades.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce imágenes y símbolos en cuentos o textos ilustrados.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Anticipa contenido de cuentos por ilustraciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en juegos de rimas y canciones.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Inventa cuentos o anécdotas simples a partir de experiencias.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Procesos lógico-matemáticos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Clasifica objetos por color, forma o tamaño.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Cuenta objetos hasta cinco o más.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica relaciones espaciales básicas (arriba, abajo, cerca...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Agrupa elementos según cantidad o similitud.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce figuras geométricas básicas: círculo, cuadrado, triángulo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Expresión plástica, corporal y musical',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza materiales como plastilina, témperas o papel para crear.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Baila y canta al ritmo de canciones infantiles.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Representa personas u objetos mediante dibujos simples.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Coordina movimientos con música o instrumentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Explora sonidos del cuerpo y objetos para crear ritmos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Imitación y juegos de roles',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Imita acciones de personas conocidas (familiares, docentes).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en dramatizaciones de cuentos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Crea personajes e historias en juegos simbólicos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Utiliza disfraces y materiales para representar roles sociales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Asume roles de grupo en situaciones de juego organizado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Educación vial y ciudadana',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de tránsito básicas (alto, paso peatonal...).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Aplica normas de seguridad al transitar por la calle.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en simulacros de emergencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Conoce los colores del semáforo y su significado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Demuestra respeto por normas de convivencia escolar.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Salud integral y hábitos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Practica hábitos de higiene personal diariamente.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica alimentos saludables y no saludables.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en rutinas de alimentación, descanso y aseo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce señales de malestar y pide ayuda.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Colabora con la limpieza y orden del aula.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areaId = DB::table('eilearningareas')->insertGetId([
            'grado_id' => 24,
            'name' => 'Identidad, historia y cultura',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Dice su nombre, apellido y el de familiares cercanos.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Identifica símbolos patrios como bandera, himno y escudo.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Participa en celebraciones patrias o tradicionales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Reconoce personajes históricos locales en cuentos o murales.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('eilearningexpectations')->insert([
            'eilearningarea_id' => $areaId,
            'description' => 'Manifiesta orgullo por su comunidad y cultura.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
