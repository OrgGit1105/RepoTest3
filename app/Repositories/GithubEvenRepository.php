<?php

namespace Repository;

use App\Repositories\Contracts\GithubEvenRepositoryInterface;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubEvenRepository extends BaseRepository implements GithubEvenRepositoryInterface
{
    protected $githubToken;
    protected $githubUrl;

    public function __construct()
    {
        $this->githubToken = config('common.github.github_token');
        $this->githubUrl = config('common.github.veho_develop.api_issues');
    }

    public function model()
    {
    }

    public function createIssues(Request $request)
    {
        $data = $this->logAndDecodeRequest($request->getContent());
        // Xác nhận đăng ký nếu yêu cầu là SubscriptionConfirmation
        if ($this->isSubscriptionConfirmation($data)) {
            return $this->handleSubscriptionConfirmation($data['SubscribeURL']);
        }

        // Xử lý thông báo Alarm từ Cloudwatch
        if ($this->isNotification($data)) {
            $alarmDetails = json_decode($data['Message'], true);
            if (isset($alarmDetails['AlarmName'])){
                $alarmName = $alarmDetails['AlarmName'];
                $contentTop = $this->getAlarmTopServerInfo($alarmDetails);

                $issueBody = $this->buildIssueBody($alarmDetails, $contentTop, $data['UnsubscribeURL']);
                $response = $this->createGithubIssue($alarmName, $issueBody, $alarmDetails['Trigger']['Dimensions'][0]['value']);

                return $this->buildResponse($response);
            }else if(isset($alarmDetails['detail-type']) && $alarmDetails['detail-type'] === 'GuardDuty Finding'){
                $parsedMessage = $alarmDetails['detail'];
                $resourceDetails = @$parsedMessage['resource']['instanceDetails'];
                $instanceId = @$resourceDetails['instanceId'];
                $publicIp = @$resourceDetails['networkInterfaces'][0]['publicIp'];
                $issueTemplate = "# AWS GuardDuty Finding: {$parsedMessage['type']}

                                **Finding ID**: {$parsedMessage['id']}
                                **Severity**: {$parsedMessage['severity']}
                                **Instance ID**: {$instanceId}
                                **Public IP**: {$publicIp}
                                **Description**: {$parsedMessage['description']}

                                ---

                                **Title**: {$parsedMessage['title']}
                                **Timestamp**: {$alarmDetails['time']}";


                $response = $this->createGithubIssue('New GuardDuty Finding'. $parsedMessage['type'], $issueTemplate, $alarmDetails['account'], 2);
                return $this->buildResponse($response);
            }

        }

        return response()->json(['message' => 'Notification received'], 200);
    }

    private function logAndDecodeRequest($data)
    {
        Log::info('Cloudwatch Alarm:', ['data' => $data]);
        return json_decode($data, true);
    }

    private function isSubscriptionConfirmation($data)
    {
        return isset($data['Type']) && $data['Type'] === 'SubscriptionConfirmation';
    }

    private function handleSubscriptionConfirmation($subscribeUrl)
    {
        $response = Http::get($subscribeUrl);
        Log::info('Subscription confirmation response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        return response()->json(['message' => 'Subscription confirmed'], 200);
    }

    private function isNotification($data)
    {
        return isset($data['Type']) && $data['Type'] === 'Notification';
    }

    private function getAlarmTopServerInfo($alarmDetails)
    {
        $instanceId = $alarmDetails['Trigger']['Dimensions'][0]['value'];
        $metricName = $alarmDetails['Trigger']['MetricName'];
        if ($this->isInstanceValid($instanceId)) {
            return $this->getInfoTopServer($instanceId, $metricName == 'mem_used_percent' ? 'MEM' : 'CPU')['StandardOutputContent'];
        }
        return null;
    }

    private function isInstanceValid($instanceId)
    {
        return explode('-', $instanceId)[0] == 'i' && count(explode('-', $instanceId)) > 1;
    }

    private function buildIssueBody($alarmDetails, $contentTop, $unsubscribeURL)
    {
        $alarmName = $alarmDetails['AlarmName'];
        $stateChangeReason = $alarmDetails['NewStateReason'];
        $timestamp = $alarmDetails['StateChangeTime'];
        return "
You are receiving this email because your Amazon CloudWatch Alarm \"{$alarmName}\" has entered the ALARM state, because \"{$stateChangeReason}\" at \"{$timestamp}\".

-- Info CPU Ram(Command top server):
{$contentTop}

If you wish to stop receiving notifications from this topic, please unsubscribe:
{$unsubscribeURL}
";
    }

    private function createGithubIssue($title, $issueBody, $instance, $milestone = 1)
    {
        $assignee = $this->getAssignee($instance);

        $issueData = [
            'title' => "ALARM: {$title}",
            'body' => $issueBody,
            'assignees' => [$assignee],
            'milestone' => $milestone,
            'labels' => ['low priority']
        ];
        return Http::withHeaders($this->getGithubHeaders())
            ->post($this->githubUrl, $issueData);
    }

    private function getGithubHeaders()
    {
        return [
            'Authorization' => "token {$this->githubToken}",
            'Accept' => 'application/vnd.github.v3+json'
        ];
    }

    private function getAssignee($instance)
    {
        $instanceIzumi = ['i-0d8bc9faee44b0f2b', 'izumi'];
        return in_array($instance, $instanceIzumi) ? 'phuongcodeunited' : 'tuancuongth88';
    }

    private function buildResponse($response)
    {
        if ($response->successful()) {
            return response()->json(['statusCode' => 200, 'body' => 'Issue created successfully.']);
        } else {
            return response()->json(['statusCode' => $response->status(), 'body' => $response->body()], $response->status());
        }
    }

    private function getInfoTopServer($instanceId, $sortBy = 'CPU')
    {
        try {
            $param = Common::configAwsSDK($instanceId);
            $ssmClient = new SsmClient($param);
            $response = $ssmClient->sendCommand([
                'InstanceIds' => [$instanceId],
                'DocumentName' => 'AWS-RunShellScript',
                'Parameters' => [
                    'commands' => ["export COLUMNS=500; top -c -b -o +%{$sortBy} | head -n 20"],
                ],
            ]);

            $commandId = $response['Command']['CommandId'];

            return $this->waitForCommandResult($ssmClient, $commandId, $instanceId);
        }catch (\Exception $exception){
            dd($exception);
        }

    }

    private function waitForCommandResult(SsmClient $ssmClient, $commandId, $instanceId)
    {
        $maxAttempts = 10;
        for ($attempts = 0; $attempts < $maxAttempts; $attempts++) {
            sleep(5);
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);

            if ($output['Status'] === 'Success') {
                return $output;
            }
        }

        return $output;
    }
}
