<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace Repository;

use App\Models\ArrivingReport;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Carbon\Carbon;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
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

    public function getAllSchedule($request)
    {
        $typDate = [2, 3];
        $yearMonth = $request->get('year_month', null);
        $firstOfMonthNow = Carbon::now()->startOfMonth()->format('Y-m-d h:i:s');
        $endOfMonthNow  = Carbon::now()->endOfMonth()->format('Y-m-d h:i:s');
        $firstOfMonth = Carbon::parse($yearMonth)->startOfMonth()->format('Y-m-d h:i:s');
        $endOfMonth   = Carbon::parse($yearMonth)->endOfMonth()->format('Y-m-d h:i:s');
        $arrivingReport = ArrivingReport::whereIn('type_date', $typDate)->whereNull('deleted_at');
        
        if($yearMonth) {
            $arrivingReport->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonth])
                           ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonth]) ;
        }
        return $arrivingReport->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonthNow])
                              ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonthNow])
                              ->get();

    }

    public function scheduleOneDay($request)
    {
        $typDate = [2, 3];
        $date = $request->get('year_month', null);
        $firstOfMonth = Carbon::parse($date)->format('Y-m-d 0:0:0');
        $endOfMonth   = Carbon::parse($date)->format('Y-m-d 23:59:59');

        return  ArrivingReport::whereIn('type_date', $typDate)
            ->whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonth])
            ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonth])
            ->get();
    }

}
