<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabasePermission extends model
{
    use hasfactory;
    protected $table = 'database_permission';
    public $timestamps = true;

    const DATABASE_ID = 'database_id';
    const RDS_PERMISSION_ID = 'rds_permission_id';

    protected $fillable = [
        self::DATABASE_ID, self::RDS_PERMISSION_ID
    ];
}
