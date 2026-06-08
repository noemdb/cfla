<?php
namespace App\Models\app\Educational;

use App\Models\app\Pescolar\Pensum;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebateQuestion extends Model
{
    use HasFactory;
    protected $fillable = ['debate_id', 'user_id', 'pensum_id', 'category', 'text', 'time', 'weighting', 'observation', 'option_max', 'status_active', 'attachment', 'context', 'time_elapsed', 'status_answer', 'status_under_review'];

    const COLUMN_COMMENTS = [
        'debate_id'           => 'Debates',
        'user_id'             => 'Última revisión',
        'pensum_id'           => 'Área de formación',
        'category'            => 'Categorías',
        'text'                => 'Texto de la pregunta',
        'time'                => 'Tiempo[segundos]',
        'weighting'           => 'Ponderación',
        'observation'         => 'Observación adicional',
        'option_max'          => 'Máxima cantidad de opciones [4 por defecto]',
        'status_active'       => 'Estado',
        'attachment'          => 'Archivo adjunto',
        'time_elapsed'        => 'Tiempo trascurrido',
        'status_answer'       => 'Respondida',
        'status_under_review' => 'En revisión',
        'context'             => 'Contexto',
    ];

    const CATEGORY = [
        '[21000] Lengua'                                     => 'Lengua',
        '[21000] Inglés'                                     => 'Inglés',
        '[21000] Matemática'                                 => 'Matemática',
        '[21000] Ciencias Sociales'                          => 'Ciencias Sociales',
        '[21000] Ciencias Naturales y Robótica'              => 'Ciencias Naturales y Robótica',
        '[21000] Estética'                                   => 'Estética',
        '[21000] Cultura General'                            => 'Cultura General',
        '[21000] Educación Física'                           => 'Educación Física',
        '[21000] Formación Humana Cristiana'                 => 'Formación Humana Cristiana',
        '[21000] Socio emocional'                            => 'Socio emocional',
        '[21000] Robótica'                                   => 'Robótica',

        '[31059] Castellano'                                 => 'Castellano',
        '[31059] Inglés'                                     => 'Inglés',
        '[31059] Matemáticas'                                => 'Matemáticas',
        '[31059] Física'                                     => 'Física',
        '[31059] Química'                                    => 'Química',
        '[31059] Biología'                                   => 'Biología',
        '[31059] Ciencias Naturales'                         => 'Ciencias Naturales',
        '[31059] Ciencias de la tierra'                      => 'Ciencias de la tierra',
        '[31059] Geografía, historia y ciudadanía [GHC]'     => 'Geografía, historia y ciudadanía [GHC]',
        '[31059] Formación para la Soberanía Nacional [FSN]' => 'Formación para la Soberanía Nacional [FSN]',
        '[31059] Innovación tecnológica'                     => 'Innovación tecnológica',
        '[31059] Robótica'                                   => 'Robótica',
        '[31059] Informática'                                => 'Informática',
        '[31059] Emprendimiento'                             => 'Emprendimiento',
        '[31059] Educación física'                           => 'Educación física',
        '[31059] Arte y Patrimonio'                          => 'Arte y Patrimonio',
        '[31059] Seminario de investigación'                 => 'Seminario de investigación',
        '[31059] Orientación y convivencia'                  => 'Orientación y convivencia',
        '[31059] Orientación vocacional'                     => 'Orientación vocacional',
        '[31059] Cultura General'                            => 'Cultura General',
        '[31059] Formación Humana Cristiana'                 => 'Formación Humana Cristiana',
        '[31059] Finanzas'                                   => 'Finanzas',
        '[31059] Craneos'                                    => 'Craneos',
    ];

    /**
     * Correspondencia semántica entre categorías de Primaria [21000] y Media General [31059].
     * DIRECCIÓN: [21000] => [31059]
     * El inverso (31059->21000) se genera automáticamente en getCategoryEquivalent().
     */
    const CATEGORY_MAP = [
        '[21000] Lengua'                        => '[31059] Castellano',
        '[21000] Inglés'                        => '[31059] Inglés',
        '[21000] Matemática'                    => '[31059] Matemáticas',
        '[21000] Ciencias Sociales'             => '[31059] Geografía, historia y ciudadanía [GHC]',
        '[21000] Ciencias Naturales y Robótica' => '[31059] Ciencias Naturales',
        '[21000] Estética'                      => '[31059] Arte y Patrimonio',
        '[21000] Cultura General'               => '[31059] Cultura General',
        '[21000] Educación Física'              => '[31059] Educación física',
        '[21000] Formación Humana Cristiana'    => '[31059] Formación Humana Cristiana',
        '[21000] Socio emocional'               => '[31059] Orientación y convivencia',
        '[21000] Robótica'                      => '[31059] Robótica',
    ];

    /**
     * Correspondencias adicionales [31059] => [21000] para categorías de Media General
     * que son many-to-one respecto a sus equivalentes de Primaria.
     * Estas NO pueden generarse con array_flip() ya que múltiples [31059]
     * apuntan al mismo [21000].
     */
    const CATEGORY_MAP_INVERSE_EXTRA = [
        '[31059] Física'                                     => '[21000] Ciencias Naturales y Robótica',
        '[31059] Química'                                    => '[21000] Ciencias Naturales y Robótica',
        '[31059] Biología'                                   => '[21000] Ciencias Naturales y Robótica',
        '[31059] Ciencias de la tierra'                      => '[21000] Ciencias Naturales y Robótica',
        '[31059] Formación para la Soberanía Nacional [FSN]' => '[21000] Ciencias Sociales',
        '[31059] Innovación tecnológica'                     => '[21000] Robótica',
        '[31059] Informática'                                => '[21000] Robótica',
        '[31059] Emprendimiento'                             => '[21000] Cultura General',
        '[31059] Orientación vocacional'                     => '[21000] Socio emocional',
        '[31059] Finanzas'                                   => '[21000] Cultura General',
        '[31059] Seminario de investigación'                 => '[21000] Ciencias Sociales',
    ];

    /**
     * Devuelve la categoría equivalente dado un código de plan de estudio destino.
     *
     * Para targetCode='31059': busca en CATEGORY_MAP directo [21000] -> [31059].
     * Para targetCode='21000': busca primero en el inverso automático (array_flip de CATEGORY_MAP),
     *   y si no encuentra, consulta CATEGORY_MAP_INVERSE_EXTRA para cubrir los casos many-to-one.
     *
     * @param string $sourceCategory  Categoría actual (ej: '[21000] Lengua')
     * @param string $targetCode      Código destino ('21000' o '31059')
     * @return string|null            Categoría equivalente, o null si no hay correspondencia
     */
    public static function getCategoryEquivalent(string $sourceCategory, string $targetCode): ?string
    {
        if ($targetCode === '31059') {
            // [21000] -> [31059]: búsqueda directa
            return self::CATEGORY_MAP[$sourceCategory] ?? null;
        }

        if ($targetCode === '21000') {
            // [31059] -> [21000]: primero el inverso del mapa principal
            $inverse = array_flip(self::CATEGORY_MAP);
            if (isset($inverse[$sourceCategory])) {
                return $inverse[$sourceCategory];
            }
            // Si no, consultar el mapa inverso extendido (many-to-one)
            return self::CATEGORY_MAP_INVERSE_EXTRA[$sourceCategory] ?? null;
        }

        return null;
    }

    public static function getListCategory()
    {
        $groupedCategories = [];

        foreach (DebateQuestion::CATEGORY as $key => $category) {
            preg_match('/\[(\d+)\]/', $key, $matches);
            $code                           = $matches[1];
            $groupedCategories[$code][$key] = $category;
        }

        return $groupedCategories;
    }

    // Relación
    public function options()
    {return $this->hasMany(DebateOption::class, 'question_id');}
    public function answers()
    {return $this->hasMany(DebateAnswer::class, 'question_id');}
    public function debate()
    {return $this->belongsTo(Debate::class, 'debate_id');}
    public function pensum()
    {return $this->belongsTo(Pensum::class, 'pensum_id');}
    public function user()
    {return $this->belongsTo(User::class, 'user_id');}

    // Scope para obtener las preguntas activas
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }
    // Scope para obtener las preguntas inactivas
    public function scopeInactive($query)
    {
        return $query->where('status_active', false);
    }

    // Accessor para obtener la respuesta correcta
    public function getOptionCorrectAttribute()
    {
        return $this->options()->where('status_option_correct', true)->first();
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/' . $this->attachment) : null;
    }

    public function getStatusTimeElapsedAttribute()
    {
        return ($this->time_elapsed >= $this->time) ? true : false;
    }

    public function status_answer($competition_id)
    {
        $answer = DebateAnswer::query()
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debate_answers.competition_id')
            ->join('debate_questions', 'debate_questions.id', '=', 'debate_answers.question_id')
            ->where('debate_answers.competition_id', $competition_id)
            ->where('debate_questions.id', $this->id)
            ->first();
        return ($answer) ? true : false;
    }

    public static function getByGradoId(int $grado_id, ?int $debate_id = null)
    {
        return self::when($debate_id, function ($query) use ($debate_id) {
            $query->where('debate_id', $debate_id);
        })
            ->whereHas('pensum', function ($query) use ($grado_id) {
                $query->where('grado_id', $grado_id);
            })
            ->get();
    }

}
