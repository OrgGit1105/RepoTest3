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

class ArrivingReport extends Model
{
  use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'arriving_reports';

    protected $fillable = [
      'user_id',
      'in_time',
      'out_time',
      'remark',
      'registration_type',
      'link_face_in',
      'link_face_out',
      'status',
      'created_at',
      'updated_at',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user(){
      return $this->belongsTo(User::class,'user_id','id');
    }

}
