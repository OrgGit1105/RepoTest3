<?php

namespace Database\Seeders;

use App\Models\RDSManager;
use Illuminate\Database\Seeder;

class RDSManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       RDSManager::query()->firstOrCreate([
            RDSManager::URL_END_POINT => config('database.connections.mysql.host'),
            RDSManager::USERNAME => config('database.connections.mysql.username'),
            RDSManager::PASSWORD => config('database.connections.mysql.password'),
            RDSManager::PORT => config('database.connections.mysql.port'),
        ], [
            RDSManager::NAME => 'Server Dev',
        ]);
    }
}
