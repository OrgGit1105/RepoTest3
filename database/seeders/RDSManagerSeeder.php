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
            RDSManager::PHPMYADMIN_URL => 'http://phpmyadmin2.vw-dev.com/',
            RDSManager::EC2_IP_ADDRESS => '18.180.33.240',
            RDSManager::TYPE => 0 //server local
        ], [
            RDSManager::NAME => 'Server Dev',
        ]);
    }
}
