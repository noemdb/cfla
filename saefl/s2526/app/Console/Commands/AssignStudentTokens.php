<?php

namespace App\Console\Commands;

use App\Models\app\Estudiant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssignStudentTokens extends Command
{
    protected $signature = 'students:assign-tokens';
    protected $description = 'Asigna un token único a cada estudiante que no lo tenga';

    public function handle()
    {
        // $estudiants = Estudiant::whereNull('token')->get();
        $estudiants = Estudiant::all();
        $count = 0;

        foreach ($estudiants as $estudiant) {
            $token = $this->generateUniqueToken();
            $estudiant->token = $token;
            $estudiant->save();
            $count++;
            $this->info("Token asignado a estudiante ID {$estudiant->id}");
        }

        $this->info("Proceso finalizado. Total tokens asignados: $count");
        return 0;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(20));
        } while (Estudiant::where('token', $token)->exists());

        return $token;
    }

}