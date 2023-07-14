<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emotion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'emotions';
    public $timestamps = false;

    protected $fillable = [
      'user_id',
      'arriving_id',
      'time',
      'type_check',
      'happy',
      'sad',
      'angry',
      'confused',
      'disgusted',
      'surprised',
      'calm',
      'fear',
      'created_at',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

}
