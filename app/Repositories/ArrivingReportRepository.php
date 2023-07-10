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
use Helper\Common;
use Illuminate\Support\Str;

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

	public function getList($request = [])
	{
        $defaulStartWeek = Carbon::now()->startOfWeek();
        $defaulEndWeek = Carbon::now()->startOfWeek()->addDay(4);
        $startDate = Arr::get($request, 'start_date', $defaulStartWeek);
        $endDate = Arr::get($request, 'end_date', $defaulEndWeek);
        $startDate = date("Y-m-d 00:00", strtotime($startDate));
        $endDate = date("Y-m-d 23:59", strtotime($endDate));
        $userId = Arr::get($request, 'user_id', []);
        $keySearch = Arr::get($request, 'key_search', []);

        $arrivings = ArrivingReport::whereBetween('in_time', [$startDate, $endDate])->with('user');
        if (!empty($userId)) {
            $arrivings = $arrivings->where('user_id', $userId);
        }
        if (!empty($keySearch)) {
            $arrivings = $arrivings->where(function($query) use ($keySearch) {
                $query->orWhereHas('user', function ($q) use ($keySearch) {
                    $q->where('name', 'like', '%'.$keySearch.'%');
                });
            });
        }

        $data = [];
        $arrivings = $arrivings->orderBy('id', 'desc');
        $arrivings = $arrivings->get();
        foreach ($arrivings as $key => $value) {    
            $data[$key]['id'] = $value->id;
            $data[$key]['user_name'] = $value->user ? $value->user->name : '';
            $data[$key]['registration_type'] = $value->registration_type;
            $data[$key]['type_date'] = $value->type_date ? __('analytic.type.'.$value->type_date) : __('analytic.type.1');
            $data[$key]['remark'] = $value->remark;
            $data[$key]['in_time'] = date("H:i:s", strtotime($value->in_time));
            $data[$key]['out_time'] = date("H:i:s", strtotime($value->out_time));
            $data[$key]['date'] = date("Y-m-d", strtotime($value->in_time));
		}

		return (new Common)->myPaginate($data);
	}

  public function create(array $attributes)
  {
    $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['in_time']);
    $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['out_time']);
    if ($in_time->getTimestamp() > $out_time->getTimestamp()){
      return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,trans('api.arriving_report.time_in_more_than_time_out'), trans('api.arriving_report.time_in_more_than_time_out'));
    }

    // Kiểm tra xem hôm nay có đúng ngày hôm nay không đã check in chưa?
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
    $attributes['type_date'] = config('analytic.type.work');

    return ResponseService::responseJson(200, new BaseResource(parent::create($attributes)));
  }

  public function detail($id)
  {
	$arriving = $this->model->with('user')->find($id);
	$arriving['type_date'] = $value->type_date ? __('analytic.type.'.$value->type_date) : __('analytic.type.1');

    return $arriving;
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

            $numberDayWork = [];
            $numberDayRemote = [];
            $numberDayOff = [];
            foreach ($analytic as $k => $v) {
                if (date("H:i:s", strtotime($v['out_time'])) == "12:00:00" || date("H:i:s", strtotime($v['in_time'])) == "13:30:00") {
                    if ($v['type_date'] == config('analytic.type.work') || $v['type_date'] == NULL) {
                        $numberDayWork[] = 0.5;
                    } elseif ($v['type_date'] == config('analytic.type.remote')) {
                        $numberDayRemote[] = 0.5;
                    } else {
                        $numberDayOff[] = 0.5;
                    }
                } else {
                    if ($v['type_date'] == config('analytic.type.work') || $v['type_date'] == NULL) {
                        $numberDayWork[] = 1;
                    } elseif ($v['type_date'] == config('analytic.type.remote')) {
                        $numberDayRemote[] = 1;
                    } else {
                        $numberDayOff[] = 1;
                    }
                }
            }

            if($analytic->isNotEmpty()) {
                $analytic = $analytic->first();

                $data[] = [
                    'user_id' => $analytic->user_id,
                    'user_name' => $analytic->user ? $analytic->user->name : '',
                    'work_day' => array_sum($numberDayWork),
                    'remote_day' => array_sum($numberDayRemote),
                    'off_day' => array_sum($numberDayOff),
                ];
            }
        }

        return $data;
    }

    public function createArriving($input = [])
    {
        // check channel
        if ($input['channel_name'] != 'yai') {
            return __('analytic.not_found_bot');
        }

        $messages = explode(',',str_replace(', ', ',', $input['text']));

        $user = User::where('email', 'like', '%' . $input['user_name'] . '%')->first();

        if(!$user) {
            return __('analytic.no_user');
        }

        if(count($messages) != 3 && count($messages) != 4) {
            return __('analytic.err_format');
        }

        if(count($messages) == 3) {
            if(!$this->validateDate($messages['1'])) {
                return __('analytic.err_format_one_date');
            }

            // if (!(Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'))->lte(Carbon::parse($messages['1'])->format('Y-m-d 08:30:00')))) {
            //     return __('analytic.check_date');
            // }

            if (!$this->holiday($messages['1'])) {
                return __('analytic.holiday');
            } else {
                ArrivingReport::create([
                    'user_id' => $user->id,
                    'in_time' => $this->inTimeDate($messages['0'], $messages['1']),
                    'out_time' => $this->outTimeDate($messages['0'], $messages['1']),
                    'type_date' => Str::contains($messages['0'], 'remote') ? config('analytic.type.remote') : config('analytic.type.off'),
                    'status' => 1,
                ]);
            }

            return response()->json([
                'response_type' => 'in_channel',
                'text' => $user->name . ' ' .__('analytic.success'),
            ]);
        }

        if(count($messages) == 4) {
            if(!$this->validateDate($messages[1]) || !$this->validateDate($messages[2])) {
                return __('analytic.err_format_two_date');
            }

			// if (!(Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'))->lte(Carbon::parse($messages['1'])->format('Y-m-d 08:30:00')))) {
            //     return __('analytic.check_date');
            // }

            if($messages['2'] < $messages['1'] || $messages['2'] == $messages['1']) {
                return __('analytic.date_err');
            }

			if (!$this->holiday($messages['1']) && !$this->holiday($messages['2'])) {
                return __('analytic.holiday');
			}

            $diffInDays = (Carbon::parse($messages['2'])->diffInDays($messages['1'])) + 1;
            $index = 0;
            $dataInsert = [];
            for ($i=0; $i < $diffInDays; $i++) {
				if ($this->holiday(Carbon::parse($messages['1'])->addDays($index))) {
					$dataInsert = [
						'user_id' => $user->id,
						'in_time' => Carbon::parse($messages['1'])->addDays($index)->format('Y-m-d 08:30:00'),
						'out_time' => Carbon::parse($messages['1'])->addDays($index)->format('Y-m-d 18:00:00'),
						'type_date' => $messages['0'] == 'remote' ? config('analytic.type.remote') : config('analytic.type.off'),
						'status' => 1,
					];
					ArrivingReport::create($dataInsert);
				}

                $index++;
            }
        }

        return response()->json([
            'response_type' => 'in_channel',
            'text' => $user->name . ' ' .__('analytic.success'),
        ]);
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    private function holiday($date)
    {
        if (Carbon::parse($date)->isSaturday()) {
            return false;
        }
        if (Carbon::parse($date)->isSunday()) {
            return false;
        }

        return true;
    }

    private function inTimeDate($typeDate, $date)
    {
        if (Str::contains($typeDate, 'morning')) {
            return $inTime = Carbon::parse($date)->format('Y-m-d 08:30:00');
        }

        if (Str::contains($typeDate, 'afternoon')) {
            return $inTime = Carbon::parse($date)->format('Y-m-d 13:30:00');
        }

        return $inTime = Carbon::parse($date)->format('Y-m-d 08:30:00');
    }

    private function outTimeDate($typeDate, $date)
    {
        if (Str::contains($typeDate, 'morning')) {
            return $outTime = Carbon::parse($date)->format('Y-m-d 12:00:00');
        }

        if (Str::contains($typeDate, 'afternoon')) {
            return $outTime = Carbon::parse($date)->format('Y-m-d 18:00:00');
        }

        return $outTime = Carbon::parse($date)->format('Y-m-d 18:00:00');
    }
}
