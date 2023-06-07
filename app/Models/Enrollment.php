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

class Enrollment extends Model
{
  use HasFactory;
  use SoftDeletes;

  const ENROLLMENT_IS_ACCEPTED = 1;
  const ENROLLMENT_NOT_ACCEPTED = 0;
  protected $table = 'enrollments';

  protected $fillable = ['interview_date', 'candidate_name', 'joining_age', 'spouse', 'dependents',
    'worked_years', 'final_education', 'shortest_service', 'company_branch_id', 'created_by',
    'updated_by', 'is_accepted'];

  protected $dates = ['deleted_at'];

  protected $casts = [
    'data' => 'array'
  ];
  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
//    'company_branch_id',
  ];

  public function company_branchs()
  {
    return $this->belongsTo(CompanyBranch::class, 'company_branch_id')->select(['id', 'name', 'address', 'description']);;
  }

}
