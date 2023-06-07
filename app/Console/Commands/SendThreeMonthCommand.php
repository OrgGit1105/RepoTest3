<?php

namespace App\Console\Commands;

use App\Models\DigitacoFile;
use App\Models\RiskScore;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Psr7;
class SendThreeMonthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendThreeFile:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $array_digitaco=DigitacoFile::get();
      foreach ($array_digitaco as $digitaco){
        $client = new \GuzzleHttp\Client();
        $response = $client->request("POST", 'https://api.vw-dev.com/retirement2', [
          'multipart' => [
            [
              'name' => 'file',
              'contents' => Psr7\Utils::tryFopen(Storage::path($digitaco->file_path_data_point), 'r')
            ],
            [
              'name' => 'file',
              'contents' => Psr7\Utils::tryFopen(Storage::path($digitaco->file_path_data_driving), 'r'),
            ],
          ]
        ]);
        $saveData = [];
        if ($response->getStatusCode() != 200) {
          $to_email = 'system@veho-works.com';
          $content = [
            'title' => 'Error: Failed to get data from the external calculation system (Retirement risk prediction system)',
            'body' => 'Error:　Retirement risk prediction system [' . Carbon::now() . '] We can not get data from the external calculation system. Please confirm that.'];
          $emailJob = new \App\Jobs\SendMail($to_email, $content);
          dispatch($emailJob);
          Log::info('Email ' . $to_email);
          return false;
        } else {
          $body = json_decode($response->getBody());
          if (isset($body->error)) {
            $to_email = 'system@veho-works.com';
            $content = [
              'title' => 'Error: Failed to get data from the external calculation system (Retirement risk prediction system)',
              'body' => 'Error:　Retirement risk prediction system [' . Carbon::now() . '] We can not get data from the external calculation system.Because ' . $body->error . ' Please confirm that.'];
            $emailJob = new \App\Jobs\SendMail($to_email, $content);
            dispatch($emailJob);
            Log::info('Email ' . $to_email);
            return false;
          } else {
            $employees = $body->name;
            $point = $body->point;
            foreach ($employees as $key => $value) {
              $employee = DB::table('employees')->select('employee_code')
                ->whereRaw("REPLACE(`employees`.`employee_name`,'　', '') like '%" . $value . "%'")
                ->first();
              if ($employee) {
                $saveData[] = [
                  'month_year' => Carbon::create($digitaco->getting_date)->format('Y-m-d'),
                  'employee_id' => $employee->employee_code,
                  'retirement_score' => $point->$key,
                  'retirement_score_percent' => ($point->$key) * 100
                ];
              }
            }
            RiskScore::insert($saveData);
          }
        }
      }
    }
}
