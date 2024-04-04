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
    const FILE_ID = 'file_id';
    const EC2_IP_ADDRESS = 'ec2_ip_address';
    const EC2_USERNAME = 'ec2_username';

    protected $fillable = [
        self::NAME,
        self::URL_END_POINT,
        self::USERNAME,
        self::PASSWORD,
        self::FILE_ID,
        self::EC2_IP_ADDRESS,
        self::EC2_USERNAME,
    ];

    protected $hidden = [];

    protected $dates = ['deleted_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'rds_info', 'rds_manager_id', 'user_id');
    }

    public function databases()
    {
        return $this->hasManyThrough(Database::class, RDSInfo::class, 'rds_manager_id', 'rds_info_id', 'id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(UploadFile::class, 'file_id', 'id');
    }
}
