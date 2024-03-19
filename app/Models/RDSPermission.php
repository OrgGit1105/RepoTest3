<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RDSPermission extends Model
{
    use HasFactory;

    const NAME = 'name';

    protected $table = 'rds_permission';

    protected $fillable = [
        self::NAME,
    ];

    public function databases()
    {
        return $this->belongsToMany(Database::class, 'database_permission', 'rds_permission_id', 'database_id');
    }
}
