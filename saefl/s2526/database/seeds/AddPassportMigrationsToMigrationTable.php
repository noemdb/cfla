<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPassportMigrationsToMigrationTable extends Seeder
{
    public function run()
    {
        $migrations = [
            ['migration' => '2016_06_01_000001_create_oauth_auth_codes_table', 'batch' => 1],
            ['migration' => '2016_06_01_000002_create_oauth_access_tokens_table', 'batch' => 1],
            ['migration' => '2016_06_01_000003_create_oauth_refresh_tokens_table', 'batch' => 1],
            ['migration' => '2016_06_01_000004_create_oauth_clients_table', 'batch' => 1],
            ['migration' => '2016_06_01_000005_create_oauth_personal_access_clients_table', 'batch' => 1],
        ];

        DB::table('migrations')->insert($migrations);
    }
}
