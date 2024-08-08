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
            Log::info('Subscription confirmation response:', ['status' => $response->status(), 'body' => $response->body()]);

            return response()->json(['message' => 'Subscription confirmed'], 200);
        }

        // Xử lý các loại thông báo khác ở đây

        return response()->json(['message' => 'Notification received'], 200);
    }
}
