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
use App\Models\UploadFile;
use App\Models\User;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use Aws\Rds\RdsClient;
use Aws\Ssm\SsmClient;
use Carbon\Carbon;
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

    public function detail($id)
    {
        return $this->model->with('file')->find($id);
    }

    private function random_port()
    {
        $portExisted = $this->model->pluck(RDSManager::PORT)->toArray();
        $portExistedInEnv = [1025, 6379];
        $portExisted = array_merge($portExisted, $portExistedInEnv);
        $port = rand(1024, 65535);

        while (in_array($port, $portExisted)) {
            $port = rand(1024, 65535);
        }
        return $port;
    }

    public function create(array $attributes)
    {
        $filePath = UploadFile::query()->find($attributes['file_id'])->file_path;
        $attributes[RDSManager::PORT] = $this->random_port();

        $connect = Common::connectRDS($attributes, $filePath);

        if ($connect['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::create($attributes));
    }

    private function getRDSLocal($id)
    {
        $rdsManagerLocal = RDSManager::query()
            ->where(RDSManager::URL_END_POINT, config('database.connections.mysql.host'))
            ->where(RDSManager::USERNAME, config('database.connections.mysql.username'))
            ->where(RDSManager::PASSWORD, config('database.connections.mysql.password'))
            ->where(RDSManager::PORT, config('database.connections.mysql.port'))
            ->first();
        if($rdsManagerLocal->id == $id)
            return false;
        return true;
    }

    public function update(array $attributes, $id)
    {
        $rdsManager = $this->model->find($id);
        $attributes['port'] = $rdsManager->port;

        if (!$rdsManager) {
            return ResponseService::responseJsonError(
                Response::HTTP_NOT_FOUND,
                trans('messages.mes.data_not_found'),
                trans('messages.mes.data_not_found'));
        }

        //don't update rds local
        if($this->getRDSLocal($id)) {
            return ResponseService::responseJsonError(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.mes.update_fail'),
                trans('messages.mes.update_fail'));
        }

        $data = $rdsManager->whereHas('users', function ($query) use ($id) {
            $query->where('rds_manager_id', $id);
        })->exists();
        if ($attributes['url_end_point'] != $rdsManager->url_end_point && $data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'update']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }

        $filePath = UploadFile::query()->find($attributes['file_id'])->file_path;
        $connect = Common::connectRDS($attributes, $filePath);
        if ($connect['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function delete($id)
    {
        $rdsManager = $this->model->find($id);
        if (!$rdsManager) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND,
                trans('messages.mes.data_not_found'),
                trans('messages.mes.data_not_found'));
        }

        //don't delete rds local
        if($this->getRDSLocal($id)) {
            return ResponseService::responseJsonError(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.mes.delete_fail'),
                trans('messages.mes.delete_fail'));
        }

        $data = $rdsManager->whereHas('users', function ($query) use ($id) {
            $query->where('rds_manager_id', $id);
        })->exists();
        if ($data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'delete']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }

        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }
}
