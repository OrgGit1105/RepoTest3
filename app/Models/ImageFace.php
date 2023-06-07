<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageFace extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'image_faces';

    protected $fillable = [
      'file',
      'user_id',
      'type',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

}
