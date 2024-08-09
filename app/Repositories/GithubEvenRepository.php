<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Repositories\Contracts\GithubEvenRepositoryInterface;
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
        // Ghi log thông tin ban đầu nhận được
        Log::info('Cloudwatch Alarm:', $request->all());
        $data = $request->getContent();

        // Ghi log dữ liệu nhận được để kiểm tra
        Log::info('Received SNS Notification:', ['data' => $data]);

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

            // Chuẩn bị nội dung chi tiết của issue
            $issueBody = "
                You are receiving this email because your Amazon CloudWatch Alarm \"{$alarmName}\" in the Asia Pacific (Tokyo) region has entered the ALARM state, because \"{$stateChangeReason}\" at \"{$timestamp}\".

                View this alarm in the AWS Management Console:
                https://ap-northeast-1.console.aws.amazon.com/cloudwatch/deeplink.js?region=ap-northeast-1#alarmsV2:alarm/CloudWatch_Alarms_Atmtc_MSJ_%E3%83%A1%E3%83%A2%E3%83%AA%E4%BD%BF%E7%94%A8%E7%8E%87

                Alarm Details:
                - Name:                       {$alarmName}
                - Description:                {$alarmDescription}
                - State Change:               OK -> ALARM
                - Reason for State Change:    {$stateChangeReason}
                - Timestamp:                  {$timestamp}
                - AWS Account:                {$awsAccountId}
                - Alarm Arn:                  {$alarmArn}

                Threshold:
                - The alarm is in the ALARM state when the metric is GreaterThanThreshold 18.0 for at least 1 of the last 1 period(s) of 60 seconds.

                Monitored Metric:
                - MetricNamespace:                     {$namespace}
                - MetricName:                          {$metricName}
                - Dimensions:                          " . $this->formatDimensions($dimensions) . "
                - Period:                              60 seconds
                - Statistic:                           Average
                - Unit:                                not specified
                - TreatMissingData:                    missing


                State Change Actions:
                - OK:
                - ALARM: [arn:aws:lambda:ap-northeast-1:291498043065:function:Add_Issue_Github_Cloudwatch] [arn:aws:sns:ap-northeast-1:291498043065:GuardDutyAlert]
                - INSUFFICIENT_DATA:


                --
                If you wish to stop receiving notifications from this topic, please click or visit the link below to unsubscribe:
                https://sns.ap-northeast-1.amazonaws.com/unsubscribe.html?SubscriptionArn=arn:aws:sns:ap-northeast-1:291498043065:GuardDutyAlert:32841b82-547b-4e1a-b6a2-f2ebb2b4540f&Endpoint=tuancuongth88@gmail.com

                Please do not reply directly to this email. If you have any questions or comments regarding this email, please contact us at https://aws.amazon.com/support
            ";

            // Thay đổi URL và Token với thông tin GitHub của bạn
            $githubRepo = 'VEHO-Develop';
            $githubToken = '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';
            $githubUrl = "https://api.github.com/repos/TeckVeho/{$githubRepo}/issues";

            $headers = [
                'Authorization' => "token {$githubToken}",
                'Accept' => 'application/vnd.github.v3+json'
            ];

            $issueData = [
                'title' => "ALARM: {$alarmName}",
                'body' => $issueBody,
                'assignees' => ['tuancuongth88'],
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

    private function formatDimensions($dimensions)
    {
        $formatted = '';
        foreach ($dimensions as $dimension) {
            $formatted .= "[{$dimension['name']} = {$dimension['value']}] ";
        }
        return $formatted;
    }
}
