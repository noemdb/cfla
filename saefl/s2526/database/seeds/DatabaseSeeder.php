<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EILearningSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DiagnosticsSeeder::class);
    }
}
