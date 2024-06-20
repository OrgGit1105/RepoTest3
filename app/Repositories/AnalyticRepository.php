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
        $roleUser = User::getRoleVFace(Auth::user());

        $analytics = ArrivingReport::whereBetween('in_time', [$startDate, $endDate])->with('user')->get();
        if (!empty($userId) && $roleUser == POLICY_V_FACE_ID['Admin']) {
            $analytics = $analytics->where('user_id', $userId);
        }

        if($roleUser == POLICY_V_FACE_ID['Normal']) {
            $analytics = $analytics->where('user_id', Auth::id());
        }

        $arrUserId = User::get()->pluck('id')->toArray();

        $data = [];
        foreach ($arrUserId as $key => $value) {
            $analytic = $analytics->where('user_id', $value);

            $numberDayWork = [];
            $numberDayRemote = [];
            $numberDayOff = [];
            $numberDaySpecialOff = [];
            $numberDayLate = [];

            foreach ($analytic as $k => $v) {
                $late = $v['late'];

                if (date("H:i:s", strtotime($v['out_time'])) <= "13:30:00" || date("H:i:s", strtotime($v['in_time'])) >= "12:00:00") {
                    if ($v['type_date'] == config('analytic.type.work') || $v['type_date'] == NULL) {
                        if($late)
                            $numberDayLate[] = 1;
                        $numberDayWork[] = 0.5;
                    } elseif ($v['type_date'] == config('analytic.type.remote')) {
                        $numberDayRemote[] = 0.5;
                    } elseif ($v['type_date'] == config('analytic.type.special')) {
                        $numberDaySpecialOff[] = 0.5;
                    } else {
                        $numberDayOff[] = 0.5;
                    }
                } else {
                    if ($v['type_date'] == config('analytic.type.work') || $v['type_date'] == NULL) {
                        $numberDayWork[] = 1;
                        if($late)
                            $numberDayLate[] = 1;
                    } elseif ($v['type_date'] == config('analytic.type.remote')) {
                        $numberDayRemote[] = 1;
                    } elseif ($v['type_date'] == config('analytic.type.special')) {
                        $numberDaySpecialOff[] = 1;
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
                    'special_off_day' => array_sum($numberDaySpecialOff),
                    'late_day' => array_sum($numberDayLate)
                ];
            }
        }

        return $data;
    }

    public function getEmotions($request)
    {
        $typeCheck = $request->get('type_check', 'in');
        $userId = $request->get('user_id', null);
        $yearMonth = $request->get('year_month', Carbon::now()->format('Y-m'));
        $getEmotions = Emotion::query()->where('user_id', $userId)
            ->where('type_check', $typeCheck)
            ->whereRaw("DATE_FORMAT(time, '%Y-%m') = ?", [$yearMonth])
            ->orderByDesc('id')
            ->get();
        return $getEmotions;
    }

    public function exportEmotions($request)
    {
        $userId     = $request->get('user_id', null);
        $typeCheck = 'in';
        return  Emotion::where('user_id', $userId)->where('type_check', $typeCheck)->get();
    }
}
