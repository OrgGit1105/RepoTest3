<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBranch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_branchs';

    protected $fillable = [ 'name', 'address', 'description', 'created_by_user', 'updated_by_user','role_id'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

    public function enrollments(){
      return $this->hasMany(Enrollment::class, 'company_branch_id');
    }

  //  public function enrollments(){
//    return $this->belongsTo(Enrollment::class, 'id');
//  }

//    public function datamanagement(){
//      return $this->hasMany(DataManagement::class, 'company_branch_id');
//  }

    public function users(){
      return $this->belongsTo(User::class, 'department_id');
  }
}
