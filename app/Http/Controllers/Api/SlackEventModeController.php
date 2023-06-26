<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Requests\ArrivingReportRequest;
use Carbon\Carbon;
use App\Models\ArrivingReport;
use App\Models\User;
use DateTime;

class SlackEventModeController extends Controller
{
    public function handleVerification(Request $request)
    {
        \Log::info('aaaaaaaaaaaa');
        \Log::info($request);
        $challenge = $request->input('challenge');

        $input = $request->all();

        $input = array (
            'token' => 'mI0mHALUUpLOeMYqRURjAgIw',
            'team_id' => 'T04S4SYEAQP',
            'team_domain' => 'youandi-num5367',
            'channel_id' => 'C04SMU7AF44',
            'channel_name' => 'yai',
            'user_id' => 'U04SKD7HTJ6',
            'user_name' => 'ngan',
            'command' => '/hybot',
            'text' => 'take off,2023-06-27,2023-06-29,bị ốm',
            'api_app_id' => 'A05DS3GPSR2',
            'is_enterprise_install' => 'false',
            'response_url' => 'https://hooks.slack.com/commands/T04S4SYEAQP/5488244932340/VXOTgJnvWUvuq5UicW584max',
            'trigger_id' => '5479150648550.4888916486839.ed4f8faa25b0acaac10f19676a17454d',
        );

        $messages = explode(',', $input['text']);

        $user = User::where('email', 'like', '%' . $input['user_name'] . '%')->first();

        if(!$user) {
            return 'Không có dữ liệu người dùng';
        }

        if(count($messages) != 3 && count($messages) != 4) {
            return 'Vui lòng viết đúng định dạng: loại yêu cầu, ngày nghỉ, lí do hoặc loại yêu cầu, ngày bắt đầu, ngày kết thúc, lí do';
        }

        if(count($messages) == 3) {
            if(!$this->validateDate($messages[1])) {
                return 'Vui lòng viết đúng định dạng: loại yêu cầu, ngày nghỉ, lí do';
            }
            ArrivingReport::insert([
                'user_id' => $user->id,
                'in_time' => Carbon::parse($messages['1'])->format('Y-m-d 08:30:00'),
                'out_time' => Carbon::parse($messages['1'])->format('Y-m-d 18:00:00'),
                'type_date' => $messages['0'] == 'remote' ? 2 : 3,
                'status' => 1,
            ]);

            return 'Bạn đã xin ' . ($messages['0'] == 'remote' ? 'remote' : 'nghỉ') . ' ngày ' . $messages[1] . ' vì lí do ' . $messages['2'];
        }

        if(count($messages) == 4) {
            if(!$this->validateDate($messages[1]) || !$this->validateDate($messages[2])) {
                return 'Vui lòng viết đúng định dạng: loại yêu cầu, ngày bắt đầu, ngày kết thúc, lí do';
            }

            if($messages['2'] < $messages['1']) {
                return 'Vui lòng chọn ngày bắt đầu nhỏ hơn ngày kết thúc';
            }

            $diffInDays = (Carbon::parse($messages['2'])->diffInDays($messages['1'])) + 1;
            $index = 0;
            $dataInsert = [];
            for ($i=0; $i < $diffInDays; $i++) { 
                array_push($dataInsert, [
                    'user_id' => $user->id,
                    'in_time' => Carbon::parse($messages['1'])->addDays($index)->format('Y-m-d 08:30:00'),
                    'out_time' => Carbon::parse($messages['1'])->addDays($index)->format('Y-m-d 18:00:00'),
                    'type_date' => $messages['0'] == 'remote' ? 2 : 3,
                    'status' => 1,
                ]);

                $index++;
            }

            ArrivingReport::insert($dataInsert);
        }

        return 'Bạn đã xin ' . ($messages['0'] == 'remote' ? 'remote' : 'nghỉ') . ' từ ngày ' . $messages['1'] . ' đến ngày ' . $messages['2'] . ' vì lí do ' . $messages['3'];

        // return response($challenge, 200)
        //     ->header('Content-Type', 'text/plain');
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}
