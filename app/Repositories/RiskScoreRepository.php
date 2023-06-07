<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-08-04
 */

namespace Repository;

use App\Models\DigitacoFile;
use App\Models\RiskScore;
use App\Repositories\Contracts\RiskScoreRepositoryInterface;
use Carbon\Carbon;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class RiskScoreRepository extends BaseRepository implements RiskScoreRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param RiskScore $model
       */

    public function model()
    {
        return RiskScore::class;
    }

//  public function getByEmployeeId($request)
//  {
//    $year = Carbon::now()->year();
//    if($request->month_year){
//      $year = $request->month_year;
//    }
//    return $this->model->where('employee_id', $request->employee_id)->whereYear('month_year',$year)->with('employees')->with(['company'])->get();
//  }
}
