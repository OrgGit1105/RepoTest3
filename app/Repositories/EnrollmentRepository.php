<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace Repository;

use App\Models\CompanyBranch;
use App\Models\ConfigRange;
use App\Models\DataManagement;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Repositories\Contracts\ConfigRangeRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
  protected $configRepository;
  protected $dataRepository;

  public function __construct(Application $app,
                              ConfigRangeRepository $configRepository)
  {
    parent::__construct($app);
    $this->configRepository = $configRepository;
  }

  /**
   * Instantiate model
   *
   * @param Enrollment $model
   */

  public function model()
  {
    return Enrollment::class;
  }

  public function create(array $attributes)
  {
    if ($attributes['is_accepted'] == 'accepted') {
      $attributes['is_accepted'] = Enrollment::ENROLLMENT_IS_ACCEPTED;
    } else {
      $attributes['is_accepted'] = Enrollment::ENROLLMENT_NOT_ACCEPTED;
    }
    if (!isset($attributes['created_by']))
      $attributes['created_by'] = Auth::id();
    return parent::create($attributes);
  }

  public function update(array $attributes, $id)
  {
    if (!isset($attributes['updated_by']))
      $attributes['updated_by'] = Auth::id();
    return parent::update($attributes, $id);
  }

  public function delete($id)
  {
    if (!isset($attributes['deleted_by']))
      $attributes['deleted_by'] = Auth::id();
    return parent::delete($id);
  }

  public function getCompanyBranchidbyEnrollment($id)
  {
    return Enrollment::with('company_branchs')->find($id);
  }

  public function getCompanyBranchbyEnrollment()
  {
    return Enrollment::with('company_branchs')->get();
  }

  public function detail($id, $request)
  {
    $enrollment = $this->model->with('company_branchs')->find($id);
    if (!$enrollment) {
      return false;
    }
    /**
     * @desc get string review and point of enrollment.example:A,B,C,D
     */
    $married = $this->getConfigByRange($enrollment->spouse, ConfigRange::TYPE_PERSON_MARRIED);
    $old = $this->getConfigByRange($enrollment->joining_age, ConfigRange::TYPE_AGE);
    $time = $this->getConfigByRange($enrollment->shortest_service, ConfigRange::TYPE_RANGE_TIME);
    $dependents = $this->getConfigByRange($enrollment->dependents, ConfigRange::TYPE_NUMBER_OF_DEPENDENTS);
    $company = $this->getConfigByRange($enrollment->worked_years, ConfigRange::TYPE_NUMBER_COMPANY);
    $education = $this->getConfigByRange($enrollment->final_education, ConfigRange::TYPE_CERTIFICATE);
    /**
     * @desc count predicted number of working months
     */
    $sum_average_total = ($old->average_total + $married->average_total + $dependents->average_total +
        $company->average_total + $education->average_total + $time->average_total) * 2;
    $point_total = ($old->number_of_studies * $old->average_total) + ($married->number_of_studies * $married->average_total)
      + ($dependents->number_of_studies * $dependents->average_total) + ($company->number_of_studies * $company->average_total)
      + ($education->number_of_studies * $education->average_total) + ($time->number_of_studies * $time->average_total);

    $point_result = ((($point_total / $sum_average_total) - 25) * 2.5) + 25;
    $review = $this->countReviewTotal($point_result);
    $users = Employee::query()->with('company')->orderBy('employee_name');
    if (isset($request->company) || isset($request->user_code) || isset($request->user_name)) {
      if (isset($request->company)) {
        $users = $users->where('company_branch', $request->company);
      }
      if (isset($request->user_code)) {
        $users = $users->where('employee_code', $request->user_code);
      }
      if (isset($request->user_name)) {
        $users = $users->where('employee_name', 'like', '%' . $request->user_name . '%');
      }
    }
    $users = $users->get()->toArray();
    $new_users=[];
    if (count($users)) {
      /**
       * @desc convert string review to number review
       */
      $point_old_enrollment = $this->convertReviewToNumber($old->ranges);
      $point_time_enrollment = $this->convertReviewToNumber($time->ranges);
      $point_married_enrollment = $this->convertReviewToNumber($married->ranges);
      $point_dependent_enrollment = $this->convertReviewToNumber($dependents->ranges);
      $point_company_enrollment = $this->convertReviewToNumber($company->ranges);
      $point_education_enrollment = $this->convertReviewToNumber($education->ranges);
      foreach ($users as $user) {
        $diference_point = (1 - (abs($point_old_enrollment - $user['oldJoinCompany'])
              + abs($point_time_enrollment - $user['rangeWork']) + abs($point_married_enrollment - $user['married'])
              + abs($point_dependent_enrollment - $user['dependent']) + abs($point_company_enrollment - $user['companyWorked'])
              + abs($point_education_enrollment - $user['education'])) / 24) * 100;
        $user['diference_point'] = $diference_point;
        array_push($new_users,$user);
      }
      usort($new_users, function ($a, $b) {
        return strcmp($a['diference_point'], $b['diference_point']);
      });
    }
    $config = [
      'old' => $old->ranges,
      'married' => $married->ranges,
      'dependents' => $dependents->ranges,
      'company' => $company->ranges,
      'education' => $education->ranges,
      'time' => $time->ranges,
      'point' => $point_result,
      'review' => $review
    ];
    return [
      ['enrollment' => $enrollment, 'config' => $config, 'users' => array_reverse(array_slice($new_users,-20))]
    ];
  }

  private function getConfigByRange($rank, $type)
  {
    if (is_null($rank)) {
      $data = $this->configRepository->getByType($type)->whereNull('rank')->first();
      if (!$data){
        $data = $this->configRepository->getByType($type)->where('rank', 0)->first();
      }
    } else {
      if ($type == ConfigRange::TYPE_PERSON_MARRIED) {
        $data = $this->configRepository->getByType($type)->where('rank', $rank)->first();
      } else {
        $data = $this->configRepository->getByType($type)->where('from', '<=', $rank)->where('to', '>', $rank)->first();
      }
    }


    return $data;
  }

  private function convertReviewToNumber($string)
  {
    $common = config('common.range');
    foreach ($common as $key => $item) {
      if ($key == $string) {
        return $item;
      }
    }
    return '';
  }

  private function countReviewTotal($point)
  {
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

  public function findByEnrollment(Request $request)
  {
    $data = Enrollment::orderBy('created_at','desc');
    if (Auth::user() && Auth::user()->role_id != 1 && Auth::user()->department_id) {
      $data=$data->where('company_branch_id', Auth::user()->department_id);
    }
    if($request->column_name && $request->sort == 'true') {
      $data = Enrollment::orderByRaw("{$request->column_name} ASC");
    }
    elseif($request->column_name && $request->sort == 'false') {
      $data = Enrollment::orderByRaw("{$request->column_name} DESC");
    }
    $data=$data->where('is_accepted', Enrollment::ENROLLMENT_IS_ACCEPTED)->where(function ($query) use ($request) {
//      $this->repository->with('company_branchs');
      $start_date = Carbon::parse($request->get('start_date'))->startOfDay()->format('Y-m-d H:i:s');
      $end_date = Carbon::parse($request->get('end_date'))->endOfDay()->format('Y-m-d H:i:s');
      if ($request->has('candidate_name')) {
        $query->where('candidate_name', 'like', "%{$request['candidate_name']}%");
      }
      if ($request->has('company_branch_id')) {
        $query->where('company_branch_id', '=', "{$request['company_branch_id']}");
      }
      if ($request->has('start_date') && $request->has('end_date')) {
        $query->where('interview_date', '<=', $end_date)->where('interview_date', '>=', $start_date);
      } else if ($request->has('start_date')) {
        $query->where('interview_date', '>=', $start_date);
      } else if ($request->has('end_date')) {
        $query->where('interview_date', '<=', $end_date);
      }
    })->with('company_branchs')->paginate($request->per_page);
    return $data;
  }
}
