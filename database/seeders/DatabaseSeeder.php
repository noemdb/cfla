<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Módulo horario: turnos M/T como catálogo (idempotente, sin datos de prueba).
        $this->call(\Database\Seeders\TimetableShiftsSeeder::class);
    }
}
