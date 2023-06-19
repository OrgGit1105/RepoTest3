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
use Illuminate\Notifications\Notifiable;

class ImageFace extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'image_faces';

    protected $fillable = [
      'file',
      'user_id',
      'type',
      'face_rekognition_id',
      'created_at',
      'updated_at',
    ];

    public $timestamps = false;

    const WITH_MASK = "WithMask";
    const WITHOUT_MASK = "WithoutMask";

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function ImageFace(){
      return $this->belongsTo(User::class,'user_id','id');
    }
}
