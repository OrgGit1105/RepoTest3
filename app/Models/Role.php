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

class Role extends Model
{
  use HasFactory, SoftDeletes;
//    use SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
      'name'
    ];

//    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

}
