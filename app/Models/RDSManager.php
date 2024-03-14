<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RDSManager extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rds_manager';

    const NAME = 'name';
    const URL_END_POINT = 'url_end_point';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const PORT = 'port';

    protected $fillable = [
        self::NAME,
        self::URL_END_POINT,
        self::USERNAME,
        self::PASSWORD,
        self::PORT,
    ];

    protected $hidden = [
        self::PASSWORD
    ];

    protected $dates = ['deleted_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function viam_users()
    {
        return $this->belongsToMany(VIAMUser::class, 'rds_info','rds_manager_id', 'viam_user_id');
    }
}
