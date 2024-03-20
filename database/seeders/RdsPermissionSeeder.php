<?php

namespace Database\Seeders;

use App\Models\Database;
use App\Models\RDSPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RdsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        RDSPermission::truncate();
        Schema::enableForeignKeyConstraints();
        RDSPermission::query()->insert([
            [
                RDSPermission::NAME => 'SELECT',
                RDSPermission::TYPE => 1
            ],
            [
                RDSPermission::NAME => 'INSERT',
                RDSPermission::TYPE => 1
            ],
            [
                RDSPermission::NAME => 'UPDATE',
                RDSPermission::TYPE => 1
            ],
            [
                RDSPermission::NAME => 'DELETE',
                RDSPermission::TYPE => 1
            ],
//                [RDSPermission::NAME => 'FILE'],
            [
                RDSPermission::NAME => 'CREATE',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'ALTER',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'INDEX',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'DROP',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'CREATE TEMPORARY TABLES',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'SHOW VIEW',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'CREATE ROUTINE',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'ALTER ROUTINE',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'EXECUTE',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'CREATE VIEW',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'EVENT',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'TRIGGER',
                RDSPermission::TYPE => 2
            ],
            [
                RDSPermission::NAME => 'GRANT',
                RDSPermission::TYPE => 3
            ],
//                [RDSPermission::NAME => 'SUPER'],
//                [RDSPermission::NAME => 'PROCESS'],
//                [RDSPermission::NAME => 'RELOAD'],
//                [RDSPermission::NAME => 'SHUTDOWN'],
//                [RDSPermission::NAME => 'SHOW DATABASES'],
            [
                RDSPermission::NAME => 'LOCK TABLES',
                RDSPermission::TYPE => 3
            ],
            [
                RDSPermission::NAME => 'REFERENCES',
                RDSPermission::TYPE => 3
            ]
            ,
//                [RDSPermission::NAME => 'REPLICATION CLIENT'],
//                [RDSPermission::NAME => 'REPLICATION SLAVE'],
//                [RDSPermission::NAME => 'CREATE USER'],
            [
                RDSPermission::NAME => 'ALL PRIVILEGES',
                RDSPermission::TYPE => 4
            ],
        ]);
    }
}
