<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace Repository;

use App\Models\AlcoholCheckLog;
use App\Models\DigitacoFile;
use App\Models\Employee;
use App\Models\GetMail;
use App\Models\RiskScore;
use App\Repositories\Contracts\GetMailRepositoryI;
use Carbon\Carbon;
use Helper\Common;
use Helper\Pop3Retrieve;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laratrust\Helper;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use ZBateson\MailMimeParser\Header\HeaderConsts;
use ZipArchive;
use ZBateson\MailMimeParser\MailMimeParser;
use GuzzleHttp\Psr7;

class GetMailRepository extends BaseRepository implements GetMailRepositoryI
{

  protected $digitacoFileRepository;

  public function __construct(Application $app, DigitacoFileRepository $digitacoFileRepository)
  {
    parent::__construct($app);

    $this->digitacoFileRepository = $digitacoFileRepository;
  }

  /**
   * Instantiate model
   *
   * @param GetMail $model
   */

  public function model()
  {
    return GetMail::class;
  }


  /**
   * Get mail from serve with pop3 protocol
   *
   * return: save log get mail to db, save Digitaco file path to db
   */
  public function GetMailPOP3()
  {
    $to_email = 'system@veho-works.com';
    $content = [
      'title' => 'Error: Failed to get degitaco data (Retirement risk prediction system)',
      'body' => 'Error:　Retirement risk prediction system [' . Carbon::now() . '] We can not get digitaco data. Please confirm that.'
    ];
    $mailParser = new MailMimeParser();
    $pop3 = $this->getPop3();
    $count = $pop3->status();
    if (!$count) {
      $emailJob = new \App\Jobs\SendMail($to_email, $content);
      dispatch($emailJob);
      Log::info('Email ' . $to_email);
    }
    //if count=0->mail empty
    $start = @(int)file_get_contents(storage_path("mail-count.txt"));


    // filter mail with from 3th previous month and BEFORE 3th now month
    $previousMonth3th = Carbon::now()->subMonth()->startofMonth()->addDays(2);
    //$nowMonth2th = Carbon::now()->startofMonth()->addDays(2)->format('d F Y P h:i:s P');
    $nowMonth2th = Carbon::now()->startofMonth()->addDays(31);
    $j = 0;
    $k = $start - 1;
    for ($i = $start; $i <= $count; $i++) {
      $mail = $mailParser->parse($pop3->retrieve($i), true);
      $mailDate = Carbon::parse($mail->getHeaderValue(HeaderConsts::DATE));
      if (!$mailDate->between($previousMonth3th, $nowMonth2th)) {
        $k++;
        continue;
      }
      if (!$mail->getAttachmentPart(0) || !$mail->getHeader(HeaderConsts::MESSAGE_ID)) {

        $emailJob = new \App\Jobs\SendMail($to_email, $content);
        dispatch($emailJob);
        Log::info('Email ' . $to_email);
        continue;
      }
      $messageId = $mail->getHeader(HeaderConsts::MESSAGE_ID)->getValue();
      $path_file = 'attachments/' . Str::slug($messageId, '_');
      $archiveFile = $path_file . '/' . $mail->getAttachmentPart(0)->getFilename();
      //save file to storage path
      Storage::put($archiveFile, $mail->getAttachmentPart(0)->getContent());
      //$mail->getAttachmentPart(0)->save(Storage::path($archiveFile));
      $archiveFilePath = Storage::path($archiveFile);
      //  log to db get mail
      $getMailLog = [
        'status' => 0,
        'mail_subject' => Common::convertEncodingJpToUtf8($mail->getHeader(HeaderConsts::SUBJECT)->getValue()),
        'mail_from' => $mail->getHeader(HeaderConsts::FROM)->getValue(),
        'mail_to' => $mail->getHeader(HeaderConsts::TO)->getValue(),
        'mail_date' => $mailDate->format('Y/m/d'),
        'mail_attachment_file_name' => $mail->getAttachmentPart(0)->getFilename(),
        'mail_attachment_path' => $archiveFilePath,
        'mail_id' => $messageId
      ];
      $this->create($getMailLog);
      $this->saveDigitacoFile($archiveFile, $path_file);
      //$this->mail($mail->getTextContent());
      file_put_contents(storage_path("mail-count.txt"), $i);
      $j++;
      //$pop3->delete($i);
    }
    if ($k == $count) {
      $emailJob = new \App\Jobs\SendMail($to_email, $content);
      dispatch($emailJob);
      Log::info('Email ' . $to_email);
    }
    $pop3->close();
  }

  public function sendFile()
  {
    
    $month = 4;
    $digitaco = $this->digitacoFileRepository->whereMonth('getting_date', '=', 10)
      ->whereYear('getting_date', '=', 2021)->first();
    // dd($digitaco); die();
    if (!$digitaco) {
      $to_email = 'system@veho-works.com';
      $content = [
        'title' => 'Error: Failed to get data from the external calculation system (Retirement risk prediction system)',
        'body' => 'Error:　Retirement risk prediction system [' . Carbon::now() . '] We can not get data from the external calculation system. Please confirm that.'];
      $emailJob = new \App\Jobs\SendMail($to_email, $content);
      dispatch($emailJob);
      Log::info('Email ' . $to_email);
      return false;
    }
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
      ],
        'verify' => false
    ]);
    $saveData = [];
    // dd($response); die();
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
      // dd($body);
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
              'month_year' => '2021-10-03',
              'employee_id' => $employee->employee_code,
              'retirement_score' => $point->$key,
              'retirement_score_percent' => ($point->$key) * 100
            ];
          }
        }
        RiskScore::insert($saveData);
        return true;
      }
    }
  }

  private function saveDigitacoFile($zipFilePath, $extractPath)
  {
    $zip = new ZipArchive();
    $archiveFilePath = Storage::path($zipFilePath);
    $extractToPath = Storage::path($extractPath);
    if (file_exists($archiveFilePath) && $zip->open($archiveFilePath) == TRUE) {

//      if (!Storage::exists($extractPath . '\01.得点一覧表〇\/')) {
//        Storage::makeDirectory($extractPath . '\01.得点一覧表〇\/');
//      }
//      if (!Storage::exists($extractPath . '\03_個人別安全運転順位表▲\/')) {
//        Storage::makeDirectory($extractPath . '\03_個人別安全運転順位表▲\/');
//      }

      $zip->extractTo($extractToPath);
      $zip->close();
      $allFilePoint = [];
      $allFileDriving = [];
      $allfile = Storage::allFiles($extractPath);
      $saveFile = [];
      foreach ($allfile as $key => $val) {
        $isFilePoin = Str::contains(basename($val), "得点一覧表.csv");
        $isFileDriving = Str::contains(basename($val), "個人別安全運転順位表.csv");
        if ($isFilePoin) {
          array_push($allFilePoint, $val);
        }
        if ($isFileDriving) {
          array_push($allFileDriving, $val);
        }
      }
      // dd($allfile);
      foreach ($allFilePoint as $key => $val) {
        $mail_log = DigitacoFile::where('file_name_data_point', 'like', '%' . basename($val) . '%')->orWhere('file_name_data_driving','like', '%' . basename($allFileDriving[$key]) . '%')->first();
        if ($mail_log) {
          continue;
        }
        $saveFile[] = [
          'getting_date' => $this->getDate($allFilePoint, basename($val)),
          'file_name_data_point' => basename($val),
          'file_path_data_point' => $val,
          'file_name_data_driving' => basename($allFileDriving[$key]),
          'file_path_data_driving' => $allFileDriving[$key],
          'status' => 0,
          'created_at' => now()
        ];
      }
      DigitacoFile::insert($saveFile);
    }
  }


  private function getDate($allFile, $fileName)
  {
//    $date = Carbon::now()->format('Y/m/d');
//    if (count($allFile) > 2) {
    $month = (int)Str::substr($fileName, 4, 2);
    $year = (int)Str::substr($fileName, 0, 4);
    if ($month == 12) {
      $date = ($year + 1) . '/01/03';
    } else {
      $date = Str::substr($fileName, 0, 4) . '/' . ($month + 1) . '/03';
    }
//    }
    return $date;
  }

  private function getPop3()
  {
    $host = 'tcp://sv6148.xserver.jp';
    $user = 'logdata@veho-works.com';
    $pass = '$acsa$3407';
    $port = 110;
    $pop3 = new Pop3Retrieve();
    $pop3->open($host, $user, $pass, $port);
    return $pop3;
  }
}
