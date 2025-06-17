<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Models\ArrivingReport;
use App\Models\BreakTime;
use App\Models\HistoryEditReport;
use App\Models\HistoryUpdatePaidOff;
use App\Models\User;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use DateTimeZone;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
        $startDateBreak = date("Y-m-d", strtotime($startDate));
        $endDateBreak = date("Y-m-d", strtotime($endDate));
        $userId = Arr::get($request, 'user_id', []);
        $keySearch = Arr::get($request, 'key_search', []);

        $breakTimes = BreakTime::query()->get();
        $arrivings = ArrivingReport::whereBetween('in_time', [$startDate, $endDate])->with('user');

        if (!empty($userId)) {
            $arrivings = $arrivings->where('user_id', $userId);
            $breakTimes = $breakTimes->where(BreakTime::USER_ID, $userId);
        }
        if (!empty($keySearch)) {
            $arrivings = $arrivings->where(function ($query) use ($keySearch) {
                $query->orWhereHas('user', function ($q) use ($keySearch) {
                    $q->where('name', 'like', '%' . $keySearch . '%');
                });
            });
        }

        $data = [];
        $arrivings = $arrivings->orderBy('in_time', 'desc')->orderBy('id', 'desc');
        $arrivings = $arrivings->get();
        $breakTimes = BreakTime::query()->get();

        foreach ($arrivings as $key => $value) {
            $data[$key]['id'] = $value->id;
            $data[$key]['user_name'] = $value->user ? $value->user->name : '';
            $data[$key]['registration_type'] = $value->registration_type;
            $data[$key]['type_date'] = $value->type_date ? __('analytic.type.' . $value->type_date) : __('analytic.type.1');
            $data[$key]['remark'] = $value->remark;
            $data[$key]['late'] = $value->late == 0 ? null : 'Late';
            $data[$key]['in_time'] = date("H:i:s", strtotime($value->in_time));
            $data[$key]['out_time'] = empty($value['out_time']) ? '' : date("H:i:s", strtotime($value->out_time));
            $data[$key]['date'] = date("Y-m-d", strtotime($value->in_time));
            $data[$key]['sum_break_time'] = $value->user ? $breakTimes
                ->where(BreakTime::USER_ID, $value->user->id)
                ->where(BreakTime::DATE, $data[$key]['date'])
                ->count() : 0;
            if ($value->in_time == null || $value->out_time == null) {
                $data[$key]['warning'] = 'Warning';
            } else {
                $data[$key]['warning'] = null;
            }

        }

        return (new Common)->myPaginate($data);
    }

    public function create(array $attributes)
    {
        $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['in_time']);
        $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['out_time']);
        $type_date = $attributes['type_date'];
        $morning = DateTime::createFromFormat('H:i', '12:00')->format('H:i:s');
        $afternoon = DateTime::createFromFormat('H:i', '13:30')->format('H:i:s');
        $history = null;
        // Kiểm tra ngày này đã check-in check-out chưa
        $arrivingIn_time = $this->model
            ->where("user_id", $attributes['user_id'])
            ->whereDate("in_time", $in_time->format('Y-m-d'))
            ->whereRaw('TIMEDIFF(out_time, in_time) >= "08:00:00"')
            ->first();
        if ($arrivingIn_time) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.time_in_is_check'), trans('api.arriving_report.time_in_is_check'));
        }

        $checkPeriodMorning = $this->model
            ->where("user_id", $attributes['user_id'])
            ->whereDate("in_time", $in_time->format('Y-m-d'))
            ->when($in_time->format('H:i:s') < $morning, function ($e) use ($morning) {
                $e->whereTime("in_time", "<", $morning);
            }, function ($e) use ($morning) {
                $e->whereTime('in_time', '>=', $morning);
            })
            ->first();
        if ($checkPeriodMorning) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.in_time_exist'), trans('api.arriving_report.in_time_exist'));
        }

        if ($out_time) {
            // Kiểm tra xem có cùng ngày không
            if ($in_time->format('Y-m-d') != $out_time->format('Y-m-d')) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.must_same_date'), trans('api.arriving_report.must_same_date'));
            }

            // Kiểm tra xem có check out có phải là tương lai check in không, nếu không báo lỗi
            if ($in_time->getTimestamp() > $out_time->getTimestamp()) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.time_in_more_than_time_out'), trans('api.arriving_report.time_in_more_than_time_out'));
            }

            // Kiểm tra ngày này đã check-out chưa
            $checkPeriodAfternoon = $this->model
                ->where("user_id", $attributes['user_id'])
                ->whereDate("in_time", $in_time->format('Y-m-d'))
                ->when($out_time->format('H:i:s') <= $afternoon, function ($e) use ($afternoon) {
                    $e->whereTime("out_time", "<=", $afternoon);
                }, function ($e) use ($morning, $afternoon, $in_time) {
                    $e->where(function ($e) use ($afternoon, $morning, $in_time) {
                        $e->whereTime('out_time', '>=', $afternoon)
                            ->orWhere(function ($query) use ($morning, $in_time) {
                                $query->whereNull('out_time')
                                    ->whereDate('in_time', $in_time)
                                    ->whereTime('in_time', '>=', $morning);
                            });
                    });
                })
                ->first();
            if ($checkPeriodAfternoon) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.out_time_exist'), trans('api.arriving_report.out_time_exist'));
            }
        } else {
            $out_time = null;
        }

        if ($type_date == config('analytic.type.take off')) { // chỉ trừ paid_off với nhân viên chinh thuc
            $user = User::query()->find($attributes['user_id']);
            $entry_date = Carbon::parse($user->entry_date)->addMonth(2);
            $day_off = Carbon::parse($in_time);

            if ($day_off >= $entry_date) {
                $paid_off = $user->paid_off;
                $paid_off_before = $paid_off;

                if ($in_time->diff($out_time)->h >= 8) {
                    $paid_off = $paid_off - 1;
                } else {
                    $paid_off = $paid_off - 0.5;
                }
                $user->paid_off = $paid_off;
                $user->save();

                $history = HistoryUpdatePaidOff::create([
                    HistoryUpdatePaidOff::USER_ID => $user->id,
                    HistoryUpdatePaidOff::TYPE => UPDATE_PAID_OFF_REGISTER_REPORT,
                    HistoryUpdatePaidOff::PAID_OFF_BEFORE => $paid_off_before,
                    HistoryUpdatePaidOff::PAID_OFF_AFTER => $paid_off
                ]);
            }
        }

        $late = 0;
        if ($type_date == config('analytic.type.work')) {
            $checkin_time = Carbon::parse($in_time);
            if ($checkin_time->hour < 12 && $checkin_time->between($checkin_time->copy()->setHour(9)->setMinute(1), $checkin_time->copy()->setHour(18)->setMinute(0))) {
                $late = 1;
            }
        }

        $attributes['status'] = 1;
        $attributes['created_at'] = Carbon::now();
        $attributes['late'] = $late;

        $report = parent::create($attributes);
        if ($history) {
            $history->report_id = $report->id;
            $history->save();
        }

        return ResponseService::responseJson(200, new BaseResource($report));
    }

    public function detail($id)
    {
        $arriving = $this->model->with('user')->find($id);
        $type_date_text = $arriving->type_date ? __('analytic.type.' . $arriving->type_date) : __('analytic.type.1');
        $break_time = BreakTime::query()
            ->select('id', BreakTime::DATE, BreakTime::GO_OUT_TIME, BreakTime::GO_INTO_TIME)
            ->where(BreakTime::DATE, Carbon::parse($arriving->in_time)->format('Y-m-d'))
            ->where(BreakTime::USER_ID, $arriving->user->id)
            ->get();

        $arriving->setAttribute('type_date_text', $type_date_text);
        $arriving->setAttribute('break_time', $break_time);
        $arriving->setAttribute('break_time_sum', $break_time->count());
        return $arriving;
    }

    public function update(array $attributes, $id)
    {
        $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['in_time']);
        $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $attributes['out_time']);
        $type_update = $attributes['type_date'];
        $morning = DateTime::createFromFormat('H:i', '12:00')->format('H:i:s');
        $afternoon = DateTime::createFromFormat('H:i', '13:30')->format('H:i:s');

        $report = $this->model->find($id);
        if (!$report) {
            return false;
        }

        $checkPeriodMorning = $this->model
            ->where("id", '!=', $id)
            ->where("user_id", $report->user_id)
            ->whereDate("in_time", $in_time->format('Y-m-d'))
            ->when($in_time->format('H:i:s') < $morning, function ($e) use ($morning) {
                $e->whereTime("in_time", "<", $morning);
            }, function ($e) use ($morning) {
                $e->whereTime('in_time', '>=', $morning);
            })
            ->first();
        if ($checkPeriodMorning) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.in_time_exist'), trans('api.arriving_report.in_time_exist'));
        }

        if ($out_time) {
            // Kiểm tra xem có cùng ngày không
            if ($in_time->format('Y-m-d') != $out_time->format('Y-m-d')) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.must_same_date'), trans('api.arriving_report.must_same_date'));
            }

            // Kiểm tra xem có check out có phải là tương lai check in không, nếu không báo lỗi
            if ($in_time->getTimestamp() > $out_time->getTimestamp()) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.time_in_more_than_time_out'), trans('api.arriving_report.time_in_more_than_time_out'));
            }

            $checkPeriodAfternoon = $this->model
                ->where("id", '!=', $id)
                ->where("user_id", $report->user_id)
                ->whereDate("in_time", $in_time->format('Y-m-d'))
                ->when($out_time->format('H:i:s') <= $afternoon, function ($e) use ($afternoon) {
                    $e->whereTime("out_time", "<=", $afternoon);
                }, function ($e) use ($morning, $afternoon, $in_time, $id) {
                    $e->where(function ($query) use ($morning, $afternoon, $in_time, $id) {
                        $query->whereTime('out_time', '>=', $afternoon)
                            ->orWhere(function ($query) use ($morning, $afternoon, $in_time, $id) {
                                $query->whereNull('out_time')
                                    ->whereTime('in_time', '>=', $morning);
                            });
                    });
                })
                ->first();
            if ($checkPeriodAfternoon) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.arriving_report.out_time_exist'), trans('api.arriving_report.out_time_exist'));
            }
        } else {
            $out_time = null;
        }

        $this->updatePaidOff($report, $in_time, $out_time, $type_update);

        $late = 0;
        if ($type_update == config('analytic.type.work')) {
            $checkin_time = Carbon::parse($in_time);
            if ($checkin_time->hour <= 12 && $checkin_time->between($checkin_time->copy()->setHour(9)->setMinute(1), $checkin_time->copy()->setHour(18)->setMinute(0))) {
                $late = 1;
            }
        }
        $attributes['late'] = $late;
        $attributes['updated_at'] = Carbon::now();
        HistoryEditReport::create([
            'in_time' => $report->in_time,
            'out_time' => $report->out_time,
            'report_id' => $report->id
        ]);
        return ResponseService::responseJson(200, new BaseResource(parent::update($attributes, $id)));
    }

    private function updatePaidOff($report, $in_time, $out_time, $type_update)
    {
        $type_take_off = config('analytic.type.take off');
        $user = User::query()->find($report->user_id);
        $entry_date = Carbon::parse($user->entry_date)->addMonth(2);
        $day_off_update = Carbon::parse($in_time);
        $day_off_report = Carbon::parse($report->intime);
        $is_official_update = $day_off_update >= $entry_date;
        $is_official_report = $day_off_report >= $entry_date;
        $paid_off_before = $user->paid_off;

        //Update time với type date cũ và mới đều là take_off
        if ($type_update == $report->type_date && $report->type_date == $type_take_off) {
            $report_in_time = DateTime::createFromFormat('Y-m-d H:i:s', $report->in_time);
            $report_out_time = DateTime::createFromFormat('Y-m-d H:i:s', $report->out_time);

            if ($report_in_time != $in_time || $report_out_time != $out_time) {
                if ($is_official_report) {
                    if ($report_in_time->diff($report_out_time)->h >= 8) {
                        $user->paid_off = $user->paid_off + 1;
                    } else {
                        $user->paid_off = $user->paid_off + 0.5;
                    }
                }
                if ($is_official_update) {
                    if ($in_time->diff($out_time)->h >= 8) {
                        $user->paid_off = $user->paid_off - 1;
                    } else {
                        $user->paid_off = $user->paid_off - 0.5;
                    }
                }
            }
        }

        //update type từ take_off -> khác hoặc từ loại khác về take_off
        if ($type_update != $report->type_date) {
            if ($type_update == $type_take_off && $is_official_update) {
                if ($in_time->diff($out_time)->h >= 8) {
                    $user->paid_off = $user->paid_off - 1;
                } else {
                    $user->paid_off = $user->paid_off - 0.5;
                }
            }
            if ($report->type_date == $type_take_off && $is_official_report) {
                $in_time_report = DateTime::createFromFormat('Y-m-d H:i:s', $report->in_time);
                $out_time_report = DateTime::createFromFormat('Y-m-d H:i:s', $report->out_time);

                if ($in_time_report->diff($out_time_report)->h >= 8) {
                    $user->paid_off = $user->paid_off + 1;
                } else {
                    $user->paid_off = $user->paid_off + 0.5;
                }
            }
        }
        $user->save();

        if ($paid_off_before != $user->paid_off) {
            HistoryUpdatePaidOff::create([
                HistoryUpdatePaidOff::USER_ID => $user->id,
                HistoryUpdatePaidOff::REPORT_ID => $report->id,
                HistoryUpdatePaidOff::TYPE => UPDATE_PAID_OFF_REGISTER_REPORT,
                HistoryUpdatePaidOff::PAID_OFF_BEFORE => $paid_off_before,
                HistoryUpdatePaidOff::PAID_OFF_AFTER => $user->paid_off
            ]);
        }
    }

    public function createArriving($input = [])
    {
        try {
            // check channel
            if ($input['channel_name'] !== config('app.channel')) {
                return __('analytic.not_found_bot');
            }

            $messages = explode(',', str_replace(', ', ',', $input['text']));
            if (count($messages) !== 3 && count($messages) !== 4) {
                return __('analytic.err_format');
            }

            $user = User::where('email', 'like', '%' . $input['user_name'] . '%')->first();
            if (!$user) {
                return __('analytic.no_user');
            }

            $commands = ['take off', 'take off morning', 'take off afternoon', 'remote', 'remote morning', 'remote afternoon'];
            if (!in_array($messages[0], $commands, true)) {
                return __('analytic.check_command');
            }

            $typeDate = $this->getTypeDate($messages[0]);
            $officialStaff = Carbon::parse($user->entry_date)->addMonths(2);
            if (count($messages) === 3) {
                return $this->handleSingleDate($messages, $typeDate, $user, $officialStaff);
            }

            if (count($messages) === 4) {
                return $this->handleMultiDate($messages, $user, $typeDate, $officialStaff);
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    private function handleSingleDate(array $messages, int $typeDate, User $user, Carbon $official)
    {
        [$command, $dateOff, $remark] = $messages;

        if (!$this->validateDate($dateOff)) {
            return __('analytic.err_format_one_date');
        }

        if (Carbon::now()->startOfDay()->gt(Carbon::parse($dateOff)->startOfDay())) {
            return __('analytic.check_date');
        }

        if (!$this->holiday($dateOff)) {
            return __('analytic.holiday');
        } else {
            $report = ArrivingReport::query()->firstOrCreate([
                'user_id' => $user->id,
                'in_time' => $this->inTimeDate($command, $dateOff),
                'out_time' => $this->outTimeDate($command, $dateOff),
                'type_date' => $typeDate
            ], [
                'remark' => $remark,
                'status' => 1
            ]);

            if ($report->wasRecentlyCreated && $typeDate === config('analytic.type.take off') && Carbon::parse($dateOff)->startOfDay()->gte($official->startOfDay())) {
                $paid = Str::contains($command, ['morning', 'afternoon']) ? 0.5 : 1;
                $this->handlePaidOffDeduction($user, $report, $paid);
            }
            return $this->respondSuccess($user);
        }
    }

    private function handleMultiDate(array $messages, User $user, int $typeDate, Carbon $officialDate)
    {
        [$command, $startDate, $endDate, $remark] = $messages;

        if (!$this->validateDate($startDate) || !$this->validateDate($endDate)) {
            return __('analytic.err_format_two_date');
        }

        if (Carbon::now()->startOfDay()->gt(Carbon::parse($startDate)->startOfDay())) {
            return __('analytic.check_date');
        }

        if (Carbon::parse($endDate)->startOfDay()->lte(Carbon::parse($startDate)->startOfDay())) {
            return __('analytic.date_err');
        }

        $paidOffCount = 0;
        $report = null;

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $dateOff) {
            if (!$this->holiday($dateOff)) {
                continue;
            }

            $report = ArrivingReport::query()->firstOrCreate([
                'user_id' => $user->id,
                'in_time' => $this->inTimeDate($command, $dateOff),
                'out_time' => $this->outTimeDate($command, $dateOff),
                'type_date' => $typeDate
            ], [
                'remark' => $remark,
                'status' => 1
            ]);

            if ($report->wasRecentlyCreated && $typeDate === config('analytic.type.take off') && $dateOff->startOfDay()->gte($officialDate->startOfDay())) {
                $paidOffCount++;
            }
        }

        if ($paidOffCount > 0) {
            $this->handlePaidOffDeduction($user, $report, $paidOffCount);
        }

        return $this->respondSuccess($user);
    }

    private function handlePaidOffDeduction($user, $report, $paid)
    {
        $before = $user->paid_off;
        $user->paid_off -= $paid;
        $user->save();

        $this->createHistory($user, $report, $before);
    }

    private function createHistory($user, $report, $before)
    {
        HistoryUpdatePaidOff::create([
            'user_id' => $user->id,
            'report_id' => $report->id,
            'type' => UPDATE_PAID_OFF_REGISTER_REPORT,
            'paid_off_before' => $before,
            'paid_off_after' => $user->paid_off
        ]);
    }

    private function respondSuccess($user)
    {
        return response()->json([
            'response_type' => 'in_channel',
            'text' => $user->name . ' ' . __('analytic.success'),
        ]);
    }

    private function getTypeDate($messages)
    {
        $type = config('analytic.type');
        if (Str::contains($messages, 'take off')) {
            return $type['take off'];
        } elseif (Str::contains($messages, 'special')) {
            return $type['special'];
        } else {
            return $type['remote'];
        }
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    private function holiday($date)
    {
//    Use when you need to create records with holidays
//        if (Carbon::parse($date)->isSaturday()) {
//            return true;
//        }
//        if (Carbon::parse($date)->isSunday()) {
//            return true;
//        }

//    Use when you need to create records without holidays
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

    public function downloadArrivingreport($request = [])
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
            $arrivings = $arrivings->where(function ($query) use ($keySearch) {
                $query->orWhereHas('user', function ($q) use ($keySearch) {
                    $q->where('name', 'like', '%' . $keySearch . '%');
                });
            });
        }

        $data = [];
        $arrivings = $arrivings->orderBy('in_time', 'desc')->orderBy('type_date', 'asc');
        $arrivings = $arrivings->get();
        foreach ($arrivings as $key => $value) {
            $data[$key]['id'] = $value->id;
            $data[$key]['user_name'] = $value->user ? $value->user->name : '';
            $data[$key]['registration_type'] = $value->registration_type;
            $data[$key]['type_date'] = $value->type_date ? __('analytic.type.' . $value->type_date) : __('analytic.type.1');
            $data[$key]['remark'] = $value->remark;
            $data[$key]['late'] = $value->late == 0 ? null : 'Late';
            $data[$key]['in_time'] = date("H:i:s", strtotime($value->in_time));
            $data[$key]['out_time'] = empty($value['out_time']) ? '' : date("H:i:s", strtotime($value->out_time));
            $data[$key]['date'] = date("Y-m-d", strtotime($value->in_time));
            if ($value->in_time == null || $value->out_time == null) {
                $data[$key]['warning'] = 'Warning';
            } else {
                $data[$key]['warning'] = null;
            }
        }
        return $data;
    }

    public function delete($id)
    {
        $data = $this->model->find($id);
        if ($data->type_date == config('analytic.type.take off')) {
            $user = User::query()->find($data->user_id);
            $in_time = DateTime::createFromFormat('Y-m-d H:i:s', $data->in_time);
            $out_time = DateTime::createFromFormat('Y-m-d H:i:s', $data->out_time);
            $entry_date = Carbon::parse($user->entry_date);
            $day_off = Carbon::parse($data->in_time);

            if ($day_off >= $entry_date->addMonth(2)) {
                if ($in_time->diff($out_time)->h >= 8) {
                    $user->paid_off = $user->paid_off + 1;
                } else {
                    $user->paid_off = $user->paid_off + 0.5;
                }
                $user->save();
            }
        }
        return parent::delete($id); // TODO: Change the autogenerated stub
    }
}
