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
use Exception;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $arrivingReport = ArrivingReport::selectRaw("DATE_FORMAT(arriving_reports.in_time, '%Y-%m-%d') AS start, arriving_reports.type_date as title, users.name")
            ->whereIn('arriving_reports.type_date', $typDate)->whereNull('arriving_reports.deleted_at')
            ->join('users', 'users.id', '=' , 'arriving_reports.user_id');
        
        if($yearMonth) {
            $arrivingReport->whereRaw("DATE_FORMAT(arriving_reports.in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonth])
                           ->whereRaw("DATE_FORMAT(arriving_reports.in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonth]) ;
        } else {
            $arrivingReport->whereRaw("DATE_FORMAT(arriving_reports.in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonthNow])
                ->whereRaw("DATE_FORMAT(arriving_reports.in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonthNow]);
        }
        return $arrivingReport->get();
    }

    public function scheduleOneDay($request)
    {
        $typDate = [2, 3];
        $date = $request->get('year_month', null);
        $firstOfMonth = Carbon::parse($date)->format('Y-m-d 0:0:0');
        $endOfMonth   = Carbon::parse($date)->format('Y-m-d 23:59:59');

        return  ArrivingReport::selectRaw("DATE_FORMAT(in_time, '%Y-%m-%d') AS in_date, type_date")->whereIn('type_date', $typDate)
            ->whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') >= ?", [$firstOfMonth])
            ->whereRaw("DATE_FORMAT(in_time, '%Y-%m-%d %h:%i:%s') <= ?", [$endOfMonth])
            ->get();
    }

    public function getChatGPT($request)
    {
        try {
            $url = 'http://vf-chat-gpt.vw-dev.com/api/chatGPT';
            if($request->count > 1) 
            {
                $question = $request->question ;
            } else {
                $question = "You are an SQL expert and will only answer the SQL statement that will search the database for the question.
No explanation is required.
If they cannot answer, they will reply with, No applicable data found, please change your query. If you cannot answer the question, reply with No applicable data found, please change the query.
The SQL statement should follow the following rules
The table definition is as follows.
Table ‘arriving_reports’ has columns: id (integer), user_id (integer), in_time (datetime), out_time (datetime),remark (text), registration_type (string), type_date (integer), link_face_in (string) link_face_out (string), status (integer), created_at (datetime), updated_at (datetime), link_check_in (string),link_check_out (string), type_date with a value of one is going to work and value of two is remote and value of three is day off
Table ‘users’ has columns: id (integer), name (string), email (string), password (string), role_id (integer),jwt_active (text), retirement_date (datetime), status (integer),created_at (datetime), updated_at (datetime), deleted_at (datetime).". $request->question ;
            }
            $response = Http::withoutVerifying()->get($url, [
                'question' =>  $question,
            ]);
            $body = json_decode($response->getBody());
            if($body) {
                return  $question;
            } else {
                return [];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
