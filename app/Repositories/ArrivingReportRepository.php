<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Models\ArrivingReport;
use App\Models\HistoryEditReport;
use App\Models\User;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Arr;

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
    $data = $this->model->select('arriving_reports.*')->with('user')->join('users', 'users.id', '=', 'arriving_reports.user_id');
    $start_of_week=Carbon::now()->startOfWeek()->format('Y-m-d');
    $end_of_week=Carbon::now()->startOfWeek()->copy()->addDay(6)->format('Y-m-d');
    if (request()->has('start_date') && $request->start_date && request()->has('end_date') && $request->end_date) {
      $data = $data->whereBetween("in_time", [$request->start_date, $request->end_date]);
    }
    if (!request()->has('start_date') || request()->has('end_date')) {
      $data = $data->whereBetween("in_time", [$start_of_week, $end_of_week]);
    }
    if (request()->has('key_search') && $request->key_search) {
      $data = $data
        ->where('users.name', 'like', "%" . $request->key_search . "%")
        ->orWhere('in_time', 'like', "%" . $request->key_search . "%")
        ->orWhere('out_time', 'like', "%" . $request->key_search . "%")
        ->orWhere('registration_type', 'like', "%" . $request->key_search . "%")
      ;
    }
    if (request()->has('user_id') && $request->user_id) {
      $data = $data
        ->where('users.id', $request->user_id);
    }
    return $data->paginate($request->per_page);
  }

  public function create(array $attributes)
  {
    $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['in_time']);
    $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['out_time']);
    if ($in_time->getTimestamp() > $out_time->getTimestamp()){
      return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_in_more_than_time_out'), trans('api.arriving_report.time_in_more_than_time_out'));
    }

    // Kiểm tra xem hôm nay có đúng ngày hôm nay khôn đã check in chưa?
    $dateTimeNow = new DateTime('now');
    if ($dateTimeNow->format('Y-m-d') != $in_time->format('Y-m-d')){
      return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_must_today'), trans('api.arriving_report.time_must_today'));
    }

    // Kiểm tra xem hôm nay đã check in chưa?
    $arrivingIn_time = $this->model
      ->whereDate("in_time",$dateTimeNow->format('Y-m-d'))
      ->where("user_id",$attributes['user_id'])
      ->first();
    // Nếu nếu ngày hôm nay đã check in rồi thì báo lỗi
    if ($arrivingIn_time){
      return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_in_is_check'), trans('api.arriving_report.time_in_is_check'));
    }

    // Nếu nếu ngày hôm nay đã check out rồi thì báo lỗi
    $arrivingOut_time = $this->model
      ->whereDate("out_time",$dateTimeNow->format('Y-m-d'))
      ->where("user_id",$attributes['user_id'])
      ->first();
    if ($arrivingOut_time){
      return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_out_is_check'), trans('api.arriving_report.time_out_is_check'));
    }

    $attributes['status'] = 1;
    $attributes['created_at'] = Carbon::now();

    return ResponseService::responseJson(200, new BaseResource(parent::create($attributes)));
  }

  public function detail($id)
  {
    return $this->model->with('user')->find($id);
  }
   public function update(array $attributes, $id)
   {
     $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['in_time']);
     $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['out_time']);
     if ($in_time->getTimestamp() > $out_time->getTimestamp()){
       return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_in_more_than_time_out'), trans('api.arriving_report.time_in_more_than_time_out'));
     }

     $report = $this->model->find($id);
     if (!$report) {
       return false;
     }
     $attributes['updated_at'] = Carbon::now();

     $attribute_history = [
       'in_time' => $report->in_time,
       'out_time' => $report->out_time,
       'report_id' => $report->id
     ];
     HistoryEditReport::create($attribute_history);
     return ResponseService::responseJson(200, new BaseResource(parent::update($attributes, $id)));
   }

    public function getListAnalytic($input = [])
    {
        $defaulStartDate = Carbon::now()->startOfMonth();
        $defaulEndDate = Carbon::now()->endOfMonth();
        $startDate = Arr::get($input, 'start_date', $defaulStartDate);
        $endDate = Arr::get($input, 'end_date', $defaulEndDate);
        $startDate = date("Y-m-d 00:00", strtotime($startDate));
        $endDate = date("Y-m-d 23:59", strtotime($endDate));
        $userId = Arr::get($input, 'user_id', []);

        $analytics = ArrivingReport::whereBetween('in_time', [$startDate, $endDate])->with('user')->get();
        if (!empty($userId)) {
            $analytics = $analytics->where('user_id', $userId);
        }
        $arrUserId = User::get()->pluck('id')->toArray();

        $data = [];
        foreach ($arrUserId as $key => $value) {
            $analytic = $analytics->where('user_id', $value);
            if($analytic->isNotEmpty()) {
                $analytic = $analytic->first();

                $sumWorked = $analytic->where('user_id', $value)->where(function ($q1) {
                    $q1->where('type_date', config('analytic.type.work'))
                        ->orWhere('type_date', config('analytic.type.half_day_work'));
                })->sum('number_day');
                $sumRemoted = $analytic->where('user_id', $value)->where(function ($q2) {
                    $q2->where('type_date', config('analytic.type.remote'))
                        ->orWhere('type_date', config('analytic.type.half_day_remote'));
                })->sum('number_day');
                $sumTakeOff = $analytic->where('user_id', $value)->where(function ($q3) {
                    $q3->where('type_date', config('analytic.type.off'))
                        ->orWhere('type_date', config('analytic.type.half_day_off'));
                })->sum('number_day');

                $data[] = [
                    'user_id' => $analytic->user_id,
                    'user_name' => $analytic->user ? $analytic->user->name : '',
                    'work_day' => $sumWorked,
                    'remote_day' => $sumRemoted,
                    'off_day' => $sumTakeOff,
                ];
            }
        }

		return $data;
    }
}
