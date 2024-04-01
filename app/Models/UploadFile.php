<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadFile extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'files';

    const FILE_NAME = 'file_name';
    const FILE_EXTENSION = 'file_extension';
    const FILE_PATH = 'file_path';
    const FILE_SIZE = 'file_size';


    protected $fillable = [
        self::FILE_NAME,
        self::FILE_EXTENSION,
        self::FILE_PATH,
        self::FILE_SIZE,
    ];

    public function rds_manager()
    {
        return $this->hasOne(RDSManager::class, 'file_id', 'id');
    }
}
