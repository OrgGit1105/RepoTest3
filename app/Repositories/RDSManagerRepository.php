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

    private function checkConnect($attributes, $port, $filePath)
    {
        dispatch(new SSHTunnelJob($attributes, $port, $filePath, 'open'));
        sleep(5);
        $host = config('database.connections.mysql.host');
        $username = $attributes['username'];
        $password = $attributes['password'];
        $database = '';

        $connection = mysqli_connect($host, $username, $password, $database, $port);
        mysqli_close($connection);
        dispatch(new SSHTunnelJob($attributes, $port, $filePath, 'close'));

        if (!$connection) {
            return ResponseService::responseJsonError(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                trans('api.rds_manager.connect_failed'),
                trans('api.rds_manager.connect_failed'));
        }
        return ResponseService::responseJson(CODE_SUCCESS);
    }

    public function create(array $attributes)
    {
//        $file = $attributes['key_file'];
        $filePath = 'C:/xampp/htdocs/v-face/tests/V-face_test.pem';
//        Storage::disk('s3')->put($filePath, file_get_contents($file));
        $attributes['port'] = $this->random_port();
        $attributes[RDSManager::KEY_FILE] = $filePath;

        $connect = $this->checkConnect($attributes, $attributes['port'], $filePath);
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
