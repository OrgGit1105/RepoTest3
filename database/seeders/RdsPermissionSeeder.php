<?php

namespace Database\Seeders;

use App\Models\RDSPermission;
use Illuminate\Database\Seeder;

class RdsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!RDSPermission::first()) {
            RDSPermission::query()->insert([
                [RDSPermission::NAME => 'SELECT'],
                [RDSPermission::NAME => 'INSERT'],
                [RDSPermission::NAME => 'UPDATE'],
                [RDSPermission::NAME => 'DELETE'],
                [RDSPermission::NAME => 'FILE'],
                [RDSPermission::NAME => 'CREATE'],
                [RDSPermission::NAME => 'ALTER'],
                [RDSPermission::NAME => 'INDEX'],
                [RDSPermission::NAME => 'DROP'],
                [RDSPermission::NAME => 'CREATE TEMPORARY TABLES'],
                [RDSPermission::NAME => 'SHOW VIEW'],
                [RDSPermission::NAME => 'CREATE ROUTINE'],
                [RDSPermission::NAME => 'ALTER ROUTINE'],
                [RDSPermission::NAME => 'EXECUTE'],
                [RDSPermission::NAME => 'CREATE VIEW'],
                [RDSPermission::NAME => 'EVENT'],
                [RDSPermission::NAME => 'TRIGGER'],
                [RDSPermission::NAME => 'GRANT'],
                [RDSPermission::NAME => 'SUPER'],
                [RDSPermission::NAME => 'PROCESS'],
                [RDSPermission::NAME => 'RELOAD'],
                [RDSPermission::NAME => 'SHUTDOWN'],
                [RDSPermission::NAME => 'SHOW DATABASES'],
                [RDSPermission::NAME => 'LOCK TABLES'],
                [RDSPermission::NAME => 'REFERENCES'],
                [RDSPermission::NAME => 'REPLICATION CLIENT'],
                [RDSPermission::NAME => 'REPLICATION SLAVE'],
                [RDSPermission::NAME => 'CREATE USER']
            ]);
        }
    }
}
