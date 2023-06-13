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
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
      'name',
      'email',
      'password',
      'role_id',
      'retirement_date',
      'status',
      'created_at',
      'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'password', 'jwt_active',
    ];

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function role(){
      return $this->belongsTo(Role::class,'role_id','id');
    }

    public function scopeFindByName($query)
    {
      if (request()->filled('name')) {
        $query
          ->where('name', 'LIKE', '%' . request()->get('name') . '%');
      }
      return $query;
    }

    public function scopeFindByRole($query)
    {
      if (request()->filled('role_id')) {
        $query->where('role_id', request()->get('role_id'));
      }
      return $query;
    }

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
}
