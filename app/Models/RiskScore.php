<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-08-04
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskScore extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'risk_scores';

    protected $fillable = ['month_year','employee_id','retirement_score', 'retirement_score_percent'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array',
        'month_year' => 'datetime:m/Y',
    ];

  public function employees(){
    return $this->belongsTo(Employee::class, 'employee_id','employee_code');
  }

//  public function getCreatedAtAttribute($date){
//   $this->attributes['month_year'] =  Carbon::createFromFormat('Y-m-d', $date)->format('m/Y');
//  }
//
//  public function getUpdatedAtAttribute($date)
//  {
//    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
//  }

//  public function getCreatedAtAttribute($value)
//  {
//    $date = Carbon::parse($value);
//    return $date->format('datetime:m/Y');
//  }
}
