<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace Repository;

use App\Models\ArrivingReport;
use App\Models\Emotion;
use App\Models\User;
use App\Repositories\Contracts\AnalyticRepositoryInterface;
use Carbon\Carbon;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Arr;

class AnalyticRepository extends BaseRepository implements AnalyticRepositoryInterface
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

            if ($analytic->isNotEmpty()) {
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

    public function getEmotions($request) 
    {
        $startMonth = Carbon::parse(Carbon::now())->firstOfMonth()->format('Y-m-d h:i:s');
        $endMonth   = Carbon::parse(Carbon::now())->endOfMonth()->format('Y-m-d h:i:s');
        $searchAll  = $request->get('search', null);
        $userId     = $request->get('user_id', null);
        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 20);
        $typeCheck = 'in';
        $getEmotions   = Emotion::where('user_id', $userId)->where('type_check', $typeCheck);

        if($searchAll != null) {
            return  $getEmotions->paginate($perPage);
        }

        $getEmotions->whereBetween('time', [$startMonth, $endMonth]);
        return   $getEmotions->paginate($perPage);
    }
}
