<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Repositories\Contracts\GithubEvenRepositoryInterface;
use Aws\AwsClient;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Arr;


class GithubEvenRepository extends BaseRepository implements GithubEvenRepositoryInterface
{

    public function __construct(Application $app)
    {
    }


    public function model()
    {
        // TODO: Implement model() method.
    }

    public function createIssues(Request $request){

        $data = $request->getContent();

        // Ghi log dữ liệu nhận được để kiểm tra
        Log::info('Cloudwatch Alarm:', ['data' => $data]);
        // Giả sử dữ liệu nhận được là JSON và chuyển đổi nó thành mảng
        $dataArray = json_decode($data, true);
        // Kiểm tra nếu đây là yêu cầu xác thực
        if (isset($dataArray['Type']) && $dataArray['Type'] === 'SubscriptionConfirmation') {
            $subscribeUrl = $dataArray['SubscribeURL'];

            // Thực hiện yêu cầu GET tới SubscribeURL để xác nhận
            $response = Http::get($subscribeUrl);

            // Ghi log kết quả xác nhận
            Log::info('Subscription confirmation response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json(['message' => 'Subscription confirmed'], 200);
        }
        // Kiểm tra nếu đây là thông báo loại Notification
        if (isset($dataArray['Type']) && $dataArray['Type'] === 'Notification') {
            Log::info('Cloudwatch Alarm:', $dataArray);
            // Giải mã JSON bên trong
            $decodedJson = json_decode($dataArray['Message'], true);

            // Trích xuất dữ liệu cần thiết từ JSON
            $alarmName = $decodedJson['AlarmName'];
            $alarmDescription = $decodedJson['AlarmDescription'];
            $stateChangeReason = $decodedJson['NewStateReason'];
            $timestamp = $decodedJson['StateChangeTime'];
            $alarmArn = $decodedJson['AlarmArn'];
            $awsAccountId = $decodedJson['AWSAccountId'];
            $metricName = $decodedJson['Trigger']['MetricName'];
            $namespace = $decodedJson['Trigger']['Namespace'];
            $dimensions = $decodedJson['Trigger']['Dimensions'];
            $unsubscribeURL = $dataArray['UnsubscribeURL'];
            $period = $decodedJson['Trigger']['Period'];
            $statistic = ucwords(strtolower($decodedJson['Trigger']['Statistic']));
            $treatMissingData = $decodedJson['Trigger']['TreatMissingData'];
            $topicArn = $dataArray['TopicArn'];
            $comparisonOperator = $decodedJson['Trigger']['ComparisonOperator'];
            $threshold = $decodedJson['Trigger']['Threshold'];
            $datapointsToAlarm = $decodedJson['Trigger']['DatapointsToAlarm'];
            $instance = $decodedJson['Trigger']['Dimensions'][0]['value'];
            $region = $decodedJson['Region'];
            $contentTop = null;
            if(explode('-', $dimensions[0]['value'])[0] == 'i' && count(explode('-', $dimensions[0]['value'])) > 1){
                $responseTopServer = $this->getInfoTopServer($dimensions[0]['value']);
                $contentTop = $responseTopServer['StandardOutputContent'];
            }
            // Chuẩn bị nội dung chi tiết của issue
            $issueBody = "
You are receiving this email because your Amazon CloudWatch Alarm \"{$alarmName}\" in the {$region} region has entered the ALARM state, because \"{$stateChangeReason}\" at \"{$timestamp}\".

View this alarm in the AWS Management Console:
https://ap-northeast-1.console.aws.amazon.com/cloudwatch/deeplink.js?region=ap-northeast-1#alarmsV2:alarm/{$alarmName}


Alarm Details:
- Name:                       {$alarmName}
- Description:                {$alarmDescription}
- State Change:               OK -> ALARM
- Reason for State Change:    {$stateChangeReason}
- Timestamp:                  {$timestamp}
- AWS Account:                {$awsAccountId}
- Alarm Arn:                  {$alarmArn}

Threshold:
- The alarm is in the ALARM state when the metric is {$comparisonOperator} {$threshold} for at least {$datapointsToAlarm} of the last {$datapointsToAlarm} period(s) of {$period} seconds.

Monitored Metric:
- MetricNamespace:                     {$namespace}
- MetricName:                          {$metricName}
- Dimensions:                          " . $this->formatDimensions($dimensions) . "
- Period:                              {$period} seconds
- Statistic:                           {$statistic}
- Unit:                                not specified
- TreatMissingData:                    {$treatMissingData}


State Change Actions:
- OK:
- ALARM: [{$topicArn}]
- INSUFFICIENT_DATA:


--

Infomation CPU Ram(Command top server)
{$contentTop}

If you wish to stop receiving notifications from this topic, please click or visit the link below to unsubscribe:
{$unsubscribeURL}&Endpoint=tuancuongth88@gmail.com

Please do not reply directly to this email. If you have any questions or comments regarding this email, please contact us at <a href=\"https://aws.amazon.com/support\"> https://aws.amazon.com/support</a>
            ";
            // Thay đổi URL và Token với thông tin GitHub của bạn
            $githubToken = config('common.github.github_token');
            $githubUrl = config('common.github.veho_develop.api_issues');

            $headers = [
                'Authorization' => "token {$githubToken}",
                'Accept' => 'application/vnd.github.v3+json'
            ];
            // check instance  i-0d8bc9faee44b0f2b => assigness phuong
            $assigness = 'tuancuongth88';
            $instanceIzumi = [
                'i-0d8bc9faee44b0f2b',
                'izumi'
            ];
            if(in_array($instance, $instanceIzumi)){
                $assigness = 'phuongcodeunited';
            }
            $issueData = [
                'title' => "ALARM: {$alarmName}",
                'body' => $issueBody,
                'assignees' => [$assigness],
                'milestone' => 1,
                'labels' => ['low priority']
            ];

            $response = Http::withHeaders($headers)->post($githubUrl, $issueData);

            if ($response->successful()) {
                return response()->json([
                    'statusCode' => 200,
                    'body' => 'Issue created successfully.'
                ]);
            } else {
                return response()->json([
                    'statusCode' => $response->status(),
                    'body' => $response->body()
                ], $response->status());
            }
        }

        // Xử lý các loại thông báo khác ở đây (nếu có)
        return response()->json(['message' => 'Notification received'], 200);

    }

    private function getInfoTopServer($instanceId)
    {
        $param = ($instanceId != INSTANCE_ID_240) ? Common::configAwsSDK($instanceId) : Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => ["export COLUMNS=500; top -c -b -o +%CPU | head -n 20"],
            ],
        ];
        $response = $ssmClient->sendCommand($parameters);
        $commandId = $response['Command']['CommandId'];

        $waitTime = 5;
        $maxAttempts = 10;

        for ($attempts = 0; $attempts < $maxAttempts; $attempts++) {
            sleep($waitTime);
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);

            if ($output['Status'] === 'Success') {
                return $output;
            }
        }

        return $output; // Return the last output even if not successful
    }

    private function formatDimensions($dimensions)
    {
        $formatted = '';
        foreach ($dimensions as $dimension) {
            $formatted .= "[{$dimension['name']} = {$dimension['value']}] ";
        }
        return $formatted;
    }
}
