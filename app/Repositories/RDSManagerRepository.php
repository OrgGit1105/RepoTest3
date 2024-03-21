<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\HistoryEditReport;
use App\Models\RDSManager;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

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

    private function checkConnect(array $attributes) {
        $endpoint = $attributes['url_end_point'];
        $port = $attributes['port'];
        $dsn = "mysql:host={$endpoint};port={$port}";
        try {
            $pdo = new \PDO($dsn, $attributes['username'], $attributes['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (\PDOException $e) {
            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, trans('api.rds_manager.connect_failed'), trans('api.rds_manager.connect_failed'));
        }
    }

    public function create(array $attributes)
    {
        $connect = $this->checkConnect($attributes);
        if($connect->original['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::create($attributes));
    }

    public function update(array $attributes, $id)
    {
        $rdsManager = $this->model->find($id);
        if(!$rdsManager) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('messages.mes.data_not_found'), trans('messages.mes.data_not_found'));
        }

        $data = $rdsManager->whereHas('viam_users', fn ($query) => $query->where('rds_manager_id', $id))->exists();
        if($attributes['url_end_point'] != $rdsManager->url_end_point && $data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'update']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }

        $connect = $this->checkConnect($attributes);
        if($connect->original['code'] != CODE_SUCCESS) {
            return $connect;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function delete($id)
    {
        $rdsManager = $this->model->find($id);
        if(!$rdsManager) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('messages.mes.data_not_found'), trans('messages.mes.data_not_found'));
        }

        $data = $rdsManager->whereHas('viam_users', fn ($query) => $query->where('rds_manager_id', $id))->exists();
        if($data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'delete']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS,null, trans('messages.mes.delete_success'));
    }
}
