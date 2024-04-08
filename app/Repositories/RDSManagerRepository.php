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

    public function create(array $attributes)
    {
        $filePath = UploadFile::query()->find($attributes['file_id'])->file_path;
        $connect = Common::connectRDS($attributes, $filePath);

        if ($connect['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::create($attributes));
    }

    private function getIdRDSLocal($id)
    {
        return RDSManager::query()
            ->where(RDSManager::URL_END_POINT, config('database.connections.mysql.host'))
            ->where(RDSManager::USERNAME, config('database.connections.mysql.username'))
            ->where(RDSManager::PASSWORD, config('database.connections.mysql.password'))
            ->where(RDSManager::TYPE, RDS_LOCAL)
            ->first()->id;
    }

    public function update(array $attributes, $id)
    {
        $rdsManager = $this->model->find($id);

        if (!$rdsManager) {
            return ResponseService::responseJsonError(
                Response::HTTP_NOT_FOUND,
                trans('messages.mes.data_not_found'),
                trans('messages.mes.data_not_found'));
        }

        $openConnect = $this->getIdRDSLocal($id) != $id; // connect rds only when not local rds
        $filePath = UploadFile::query()->find($attributes['file_id'])->file_path;
        $connect = Common::connectRDS($attributes, $filePath, $openConnect);
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
        if ($this->getRDSLocal($id) == $id) {
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
