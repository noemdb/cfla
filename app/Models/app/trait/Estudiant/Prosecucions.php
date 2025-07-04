<?php
namespace App\Models\app\trait\Estudiant;

use Carbon\Carbon;

trait Prosecucions
{
    /**
     * Obtiene la fecha de prosecución formateada en zona horaria local
     *
     * @param string|null $timezone Zona horaria específica (opcional)
     * @return string|null Fecha formateada como dd-mm-yyyy o null si no existe
     */
    public function getFormattedDateProsecution($timezone = null)
    {
        // Si no hay fecha de prosecución, retornar null
        if (!$this->date_prosecution) {
            return null;
        }

        // Obtener la zona horaria configurada o usar la por defecto de la aplicación
        $timezone = $timezone ?? config('app.timezone', 'America/Caracas');

        try {
            // Convertir la fecha UTC a la zona horaria local y formatear
            return Carbon::parse($this->date_prosecution)
                ->setTimezone($timezone)
                ->format('d-m-Y');
        } catch (\Exception $e) {
            // En caso de error, retornar null
            return null;
        }
    }

    /**
     * Obtiene la fecha y hora de prosecución formateada en zona horaria local
     *
     * @param string|null $timezone Zona horaria específica (opcional)
     * @return string|null Fecha y hora formateada como dd-mm-yyyy HH:mm:ss o null si no existe
     */
    public function getFormattedDateTimeProsecution($timezone = null)
    {
        // Si no hay fecha de prosecución, retornar null
        if (!$this->date_prosecution) {
            return null;
        }

        // Obtener la zona horaria configurada o usar la por defecto de la aplicación
        $timezone = $timezone ?? config('app.timezone', 'America/Caracas');

        try {
            // Convertir la fecha UTC a la zona horaria local y formatear con hora
            return Carbon::parse($this->date_prosecution)
                ->setTimezone($timezone)
                ->format('d-m-Y H:i:s');
        } catch (\Exception $e) {
            // En caso de error, retornar null
            return null;
        }
    }

    /**
     * Accessor para obtener la fecha de prosecución formateada automáticamente
     * Se puede acceder como $estudiant->date_prosecution_formatted
     *
     * @return string|null
     */
    public function getDateProsecutionFormattedAttribute()
    {
        return $this->getFormattedDateProsecution();
    }

    /**
     * Accessor para obtener la fecha y hora de prosecución formateada automáticamente
     * Se puede acceder como $estudiant->date_prosecution_formatted_full
     *
     * @return string|null
     */
    public function getDateProsecutionFormattedFullAttribute()
    {
        return $this->getFormattedDateTimeProsecution();
    }

    /**
     * Verifica si el estudiante fue confirmado en una fecha específica
     *
     * @param string $date Fecha en formato Y-m-d
     * @param string|null $timezone Zona horaria específica (opcional)
     * @return bool
     */
    public function wasConfirmedOnDate($date, $timezone = null)
    {
        if (!$this->date_prosecution) {
            return false;
        }

        $timezone = $timezone ?? config('app.timezone', 'America/Caracas');

        try {
            $prosecutionDate = Carbon::parse($this->date_prosecution)
                ->setTimezone($timezone)
                ->format('Y-m-d');

            return $prosecutionDate === $date;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scope para filtrar estudiantes confirmados en un rango de fechas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate Fecha inicio (Y-m-d)
     * @param string $endDate Fecha fin (Y-m-d)
     * @param string|null $timezone Zona horaria específica (opcional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmedBetweenDates($query, $startDate, $endDate, $timezone = null)
    {
        $timezone = $timezone ?? config('app.timezone', 'America/Caracas');

        // Convertir las fechas locales a UTC para la consulta
        $startDateUTC = Carbon::createFromFormat('Y-m-d', $startDate, $timezone)
            ->startOfDay()
            ->utc();

        $endDateUTC = Carbon::createFromFormat('Y-m-d', $endDate, $timezone)
            ->endOfDay()
            ->utc();

        return $query->whereBetween('date_prosecution', [$startDateUTC, $endDateUTC]);
    }
}
