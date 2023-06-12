<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\ArrivingReport;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use Carbon\Carbon;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ArrivingReportRepository extends BaseRepository implements ArrivingReportRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param ArrivingReport $model
       */

    public function model()
    {
        return ArrivingReport::class;
    }

  public function getList($request)
  {
    $data = $this->model->with('user');
    $start_of_week=Carbon::now()->startOfWeek()->format('Y-m-d');
    $end_of_week=Carbon::now()->startOfWeek()->copy()->addDay(6)->format('Y-m-d');
    if (request()->has('start_date') && $request->start_date && request()->has('end_date') && $request->end_date) {
      $data = $data->whereBetween("in_time", [$request->start_date, $request->end_date]);
    }
    if (!request()->has('start_date') || request()->has('end_date')) {
      $data = $data->whereBetween("in_time", [$start_of_week, $end_of_week]);
    }
    if (request()->has('key_search') && $request->key_search) {
      $data = $data->where('in_time', 'like', "%" . $request->key_search . "%")
        ->orWhere('out_time', 'like', "%" . $request->key_search . "%")
        ->orWhere('registration_type', 'like', "%" . $request->key_search . "%")
      ;
    }
    return $data->paginate($request->per_page);
  }

  public function detail($id)
  {
    return $this->model->with('user')->find($id);
  }
}
