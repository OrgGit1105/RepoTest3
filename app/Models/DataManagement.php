<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-23
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataManagement extends Model
{
  use HasFactory;
  use SoftDeletes;

//  protected $primaryKey = 'employee_code';

  protected $table = 'data_managements';

//  const IMPORT_ID = 'import_id';
  const EMPLOYEE_CODE = 'employee_code';
  const STAFFS_NAME = 'staffs_name';
  const JOINING_AGE_COMPANY = 'joining_age_company';
  const DATE_JOINING_COMPANY = 'date_joining_company';
  const DATE_OUT_COMPANY = 'date_out_company';
  const SPOUSE = 'spouse';
  const DEPENDENTS = 'dependents';
  const WORKED_YEAR = 'worked_year';
  const FINAL_EDUCATION = 'final_education';
  const SHORTEST_SERVICE = 'shortest_service';
  const TOTAL_WORKED = 'total_worked';
  const COMPANY_BRANCH = 'company_branch';
//  const FILE_NAME = 'file_name';


  protected $fillable = [self::EMPLOYEE_CODE, self::STAFFS_NAME, self::JOINING_AGE_COMPANY, self::DATE_JOINING_COMPANY, self::DATE_OUT_COMPANY, self::SPOUSE, self::DEPENDENTS, self::WORKED_YEAR, self::FINAL_EDUCATION, self::SHORTEST_SERVICE, self::TOTAL_WORKED, self::COMPANY_BRANCH];

//  protected $fillable = ['employee_code', 'staffs_name', 'joining_age_company', 'date_joining_company', 'date_out_company', 'spouse', 'dependents', 'worked_year', 'final_education', 'shortest_service', 'total_worked', 'company_branch'];

  protected $dates = ['deleted_at'];
  protected $appends = ['oldJoinCompany', 'married', 'dependent', 'companyWorked', 'education', 'rangeWork',
    'oldString', 'marriedString', 'dependentString', 'companyString', 'educationString', 'rangeWorkString',
    'timePrediction','overallReview'];
  protected $casts = [
    'data' => 'array'
  ];

  protected $hidden = [
//    'company_branch',
  ];

  public function getOldStringAttribute()
  {
    $data = $this->getConfigOldJoin();
    return $data->ranges;
  }

  public function getOldJoinCompanyAttribute()
  {
    $data = $this->getOldStringAttribute();
    return $this->convertPointToString($data);
  }

  public function getMarriedStringAttribute()
  {
    $data = $this->getConfigMarried();
    return $data->ranges;
  }

  public function getMarriedAttribute()
  {
    $data = $this->getMarriedStringAttribute();
    return $this->convertPointToString($data);
  }

  public function getDependentStringAttribute()
  {
    $data = $this->getConfigDependent();
    return $data->ranges;
  }

  public function getDependentAttribute()
  {
    $data = $this->getDependentStringAttribute();
    return $this->convertPointToString($data);
  }

  public function getCompanyStringAttribute()
  {
    $data = $this->getConfigCompany();
    return $data->ranges;
  }

  public function getCompanyWorkedAttribute()
  {
    $data = $this->getCompanyStringAttribute();
    return $this->convertPointToString($data);
  }

  public function getEducationStringAttribute()
  {
    $data = $this->getConfigEducation();
    return $data->ranges;
  }

  public function getEducationAttribute()
  {
    $data = $this->getEducationStringAttribute();
    return $this->convertPointToString($data);
  }

  public function getRangeWorkStringAttribute()
  {
    $data = $this->getConfigRangeWork();
    return $data->ranges;
  }

  public function getRangeWorkAttribute()
  {
    $data = $this->getRangeWorkStringAttribute();
    return $this->convertPointToString($data);
  }

  /**
   * @desc get time prediction
   * @return float|int
   */
  public function getTimePredictionAttribute()
  {
    $old = $this->getConfigOldJoin();
    $married = $this->getConfigMarried();
    $dependents = $this->getConfigDependent();
    $company = $this->getConfigCompany();
    $education = $this->getConfigEducation();
    $time = $this->getConfigRangeWork();
    $sum_average_total = ($old->average_total + $married->average_total + $dependents->average_total +
        $company->average_total + $education->average_total + $time->average_total) * 2;
    $point_total = ($old->number_of_studies * $old->average_total) + ($married->number_of_studies * $married->average_total)
      + ($dependents->number_of_studies * $dependents->average_total) + ($company->number_of_studies * $company->average_total)
      + ($education->number_of_studies * $education->average_total) + ($time->number_of_studies * $time->average_total);

    $point_result = ((($point_total / $sum_average_total) - 25) * 2.5) + 25;
    return $point_result;
  }

  /**
   * @desc get overall review from time prediction
   * @return string
   */
  public function getOverallReviewAttribute()
  {
    $point = $this->getTimePredictionAttribute();
    if ($point >= (((30 - 25) * 2.5) + 25)) {
      $review = 'A';
    } elseif ($point >= (((27.5 - 25) * 2.5) + 25)) {
      $review = 'B';
    } elseif ($point >= 25) {
      $review = 'C';
    } elseif ($point >= (((22.5 - 25) * 2.5) + 25)) {
      $review = 'D';
    } else {
      $review = 'E';
    }
    return $review;
  }
  public function company(){
    return $this->belongsTo(CompanyBranch::class,'company_branch');
  }
  /**
   * @desc create query config range table
   * @param $type
   * @return \Illuminate\Database\Eloquent\Builder
   */
  private function queryConfigRange($type)
  {
    return ConfigRange::query()->select('ranges', 'number_of_studies', 'average_total')->where('type', $type);
  }

  /**
   * @desc  convert point int to point string(A,B,C,D,E)
   * @param $data
   * @return false|mixed|string
   */
  private function convertPointToString($data)
  {
    $common = config('common.range');
    foreach ($common as $key => $item) {
      if ($key == $data) {
        return $item;
      } elseif ($key == '') {
        return false;
      }
    }
    return '';
  }

  /**
   * @desc get row config type old join company
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigOldJoin()
  {
    return $this->queryConfigRange(ConfigRange::TYPE_AGE)->where('from', '<=', $this->joining_age_company)->where('to', '>', $this->joining_age_company)->first();
  }

  /**
   * @desc get row config type married
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigMarried()
  {
    $data = $this->queryConfigRange(ConfigRange::TYPE_PERSON_MARRIED);
    if ($this->spouse > 1) {
      $data = $data->where('rank', 1)->first();
    } else {
      $data = $data->where('rank', $this->spouse)->first();
    }
    return $data;
  }

  /**
   * @desc get row config type dependents
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigDependent()
  {
    $data = $this->queryConfigRange(ConfigRange::TYPE_NUMBER_OF_DEPENDENTS);
    if ($this->dependents > 3) {
      $data = $data->where('rank', 3)->first();
    } else {
      $data = $data->where('rank', $this->dependents)->first();
    }
    return $data;
  }

  /**
   * @desc get row config type company worked
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigCompany()
  {
    $data = $this->queryConfigRange(ConfigRange::TYPE_NUMBER_COMPANY);
    if ($this->worked_year > 7) {
      $data = $data->where('rank', 7)->first();
    } else {
      $data = $data->where('rank', $this->worked_year)->first();
    }
    return $data;
  }

  /**
   * @desc get row config type education
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigEducation()
  {
    $data = $this->queryConfigRange(ConfigRange::TYPE_CERTIFICATE);
    if ($this->final_education > 7) {
      $data = $data->where('rank', 7)->first();
    } else {
      $data = $data->where('rank', $this->final_education)->first();
    }
    return $data;
  }

  /**
   * @desc get row config type continuous working time
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  private function getConfigRangeWork()
  {
    return $this->queryConfigRange(ConfigRange::TYPE_RANGE_TIME)->where('from', '<=', $this->shortest_service)->where('to', '>', $this->shortest_service)->first();
  }
//  public function companyBranchData(){
//    return $this->belongsTo(CompanyBranch::class, 'company_branch_id');
//  }

}
