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
        // Lấy dữ liệu từ SNS
        $data = $request->all();

        // Ghi log dữ liệu nhận được để kiểm tra
        Log::info('Received SNS Notification:', $data);

        // Kiểm tra nếu đây là yêu cầu xác thực
        if (isset($data['Type']) && $data['Type'] === 'SubscriptionConfirmation') {
            $subscribeUrl = $data['SubscribeURL'];

            // Thực hiện yêu cầu GET tới SubscribeURL để xác nhận
            $response = Http::get($subscribeUrl);

            // Ghi log kết quả xác nhận
            Log::info('Subscription confirmation response:', ['status' => $response->status(), 'body' => $response->body()]);

            return response()->json(['message' => 'Subscription confirmed'], 200);
        }

        // Xử lý các loại thông báo khác ở đây

        return response()->json(['message' => 'Notification received'], 200);
//        $alarmName = $request->input('alarmName');
//        $alarmDescription = $request->input('description', 'No description provided.');
//
//        // Thay đổi URL và Token với thông tin GitHub của bạn
//        $githubRepo = 'VEHO-Develop';
//        $githubToken = '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';
//        $githubUrl = "https://api.github.com/repos/TeckVeho/{$githubRepo}/issues";
//
//        $headers = [
//            'Authorization' => "token {$githubToken}",
//            'Accept' => 'application/vnd.github.v3+json'
//        ];
//
//        $issueData = [
//            'title' => "CloudWatch Alarm Triggered: {$alarmName}",
//            'body' => "Alarm Description: {$alarmDescription}",
//            'assignees' => ['tuancuongth88'], // Chuyển thành mảng
//            'milestone' => 1, // GitHub API cần ID milestone, không phải tên
//            'labels' => ['low priority'] // Chuyển thành mảng
//        ];
//
//        $response = Http::withHeaders($headers)->post($githubUrl, $issueData);
//
//        if ($response->successful()) {
//            return ResponseService::responseJson(Response::HTTP_OK, 'add issue success');
//        } else {
//            return response()->json([
//                'statusCode' => $response->status(),
//                'body' => $response->body()
//            ], $response->status());
//        }
        return response()->json(['message' => 'Notification received'], 200);
//        return ResponseService::responseJson(Response::HTTP_OK, 'add issue success');
    }
}
