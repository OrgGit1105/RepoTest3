<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RDSInfo extends Model
{
    use HasFactory;

    const USER_ID = 'user_id';
    const RDS_MANAGER_ID = 'rds_manager_id';

    protected $table = 'rds_info';
    public $timestamps = true;

    protected $fillable = [
        self::USER_ID,
        self::RDS_MANAGER_ID,
    ];

    public function databases()
    {
        return $this->hasMany(Database::class, 'rds_info_id', 'id');
    }
}
