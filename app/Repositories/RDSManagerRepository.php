<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Jobs\SSHTunnelJob;
use App\Models\HistoryEditReport;
use App\Models\RDSManager;
use App\Models\User;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use Aws\Rds\RdsClient;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Helper\ResponseService;
use http\Env\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Namshi\JOSE\Signer\SecLib\RSA;
use phpseclib3\Net\SSH2;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use function Clue\StreamFilter\fun;

class RDSManagerRepository extends BaseRepository implements RDSManagerRepositoryInterface
{

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param RDSManager $model
     */

    public function model()
    {
        return RDSManager::class;
    }

    private function random_port()
    {
        $portExisted = $this->model->pluck(RDSManager::PORT)->toArray();
        $port = rand(1024, 65535);

        while (in_array($port, $portExisted)) {
            $port = rand(1024, 65535);
        }
        return $port;
    }

    public function create(array $attributes)
    {
        $file = $attributes['key_file'];
        $filePath = 'tests/V-face_test.pem';
//        Storage::disk('s3')->put($filePath, file_get_contents($file));
        $port = $this->random_port();
        $connect = dispatch_now(new SSHTunnelJob($attributes, $port, $filePath));
        sleep(1);
        $username = $attributes['username'];
        $password = $attributes['password'];
        $host = config('database.connections.mysql.host');
        $database = '';
        $dsn = "mysql:host=$host;port=$port;dbname=$database;chaset=utf8mb4";
        $option = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new \PDO($dsn, $username, $password, $option);
        $stmt = $pdo->query('SHOW DATABASES');
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        dd($result);
        if ($connect->original['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::create($attributes));
    }

    public function update(array $attributes, $id)
    {
        $rdsManager = $this->model->find($id);
        if (!$rdsManager) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('messages.mes.data_not_found'), trans('messages.mes.data_not_found'));
        }

        $data = $rdsManager->whereHas('users', fn($query) => $query->where('rds_manager_id', $id))->exists();
        if ($attributes['url_end_point'] != $rdsManager->url_end_point && $data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'update']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }

        $connect = $this->checkConnect($attributes);
        if ($connect->original['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function delete($id)
    {
        $rdsManager = $this->model->find($id);
        if (!$rdsManager) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('messages.mes.data_not_found'), trans('messages.mes.data_not_found'));
        }

        $data = $rdsManager->whereHas('users', fn($query) => $query->where('rds_manager_id', $id))->exists();
        if ($data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'delete']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }
}
