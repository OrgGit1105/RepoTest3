<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Requests\ArrivingReportRequest;
use Carbon\Carbon;
use DateTime;

class SlackEventModeController extends Controller
{
    /**
     * var Repository
     */
    protected $repository;

    public function __construct(ArrivingReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handleVerification(Request $request)
    {
        $challenge = $request->input('challenge');

        $input = $request->all();

        // $input = array (
        //     'token' => 'mI0mHALUUpLOeMYqRURjAgIw',
        //     'team_id' => 'T04S4SYEAQP',
        //     'team_domain' => 'youandi-num5367',
        //     'channel_id' => 'C04SMU7AF44',
        //     'channel_name' => 'yai',
        //     'user_id' => 'U04SKD7HTJ6',
        //     'user_name' => 'haiyentp.1204',
        //     'command' => '/hybot',
        //     'text' => 'take off, 2023-06-29, 2023-07-02, bị ốm',
        //     'api_app_id' => 'A05DS3GPSR2',
        //     'is_enterprise_install' => 'false',
        //     'response_url' => 'https://hooks.slack.com/commands/T04S4SYEAQP/5488244932340/VXOTgJnvWUvuq5UicW584max',
        //     'trigger_id' => '5479150648550.4888916486839.ed4f8faa25b0acaac10f19676a17454d',
        // );

        return $this->repository->createArriving($input);

        // return response($challenge, 200)
        //     ->header('Content-Type', 'text/plain');
    }
}
