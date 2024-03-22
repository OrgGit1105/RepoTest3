<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    use HasFactory;

    const NAME = 'name';
    const RDS_INFO_ID = 'rds_info_id';

    protected $table = 'database';
    public $timestamps = true;

    protected $fillable = [
        self::NAME,
        self::RDS_INFO_ID,
    ];

    public function rdsPermissions()
    {
        return $this->belongsToMany(RDSPermission::class, 'database_permission', 'database_id', 'rds_permission_id');
    }

    public function rds_info()
    {
        return $this->belongsTo(RDSInfo::class, 'rds_info_id', 'id');
    }
}
