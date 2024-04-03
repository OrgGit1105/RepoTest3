<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\Database;
use App\Models\DatabasePermission;
use App\Models\RDSInfo;
use App\Models\RDSManager;
use App\Models\RDSPermission;
use App\Models\User;
use App\Repositories\Contracts\VIAMRDSRepositoryInterface;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;

class VIAMRDSRepository extends BaseRepository implements VIAMRDSRepositoryInterface
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
        return User::class;
    }

    public function list(array $attributes)
    {
        $rds_manager_id = $attributes['rds_manager_id'];
        $database_name = $attributes['database_name'];

        $data = $this->model
            ->select('id', 'name')
            ->with([
                'rdsManagers' => function ($query) use ($rds_manager_id) {
                    $query->select('rds_manager.id', 'rds_manager.name')
                        ->where('rds_manager.id', $rds_manager_id);
                },
                'databases' => function ($query) use ($database_name) {
                    $query->where('database.name', $database_name)
                        ->with('rdsPermissions:id,name,type');
                }
            ])->get();
        $countType = RDSPermission::query()->select('id', 'type', DB::raw('count(*) as total'))
            ->groupBy(RDSPermission::TYPE)
            ->pluck('total', 'type')->toArray();

        $arrayData = [];
        foreach ($data as $key => $value) {
            $arrayData[$key]['user_id'] = $value->id;
            $arrayData[$key]['username'] = $value->name;
            $arrayData[$key]['rds_manager_id'] = [];
            $arrayData[$key]['status'] = false;

            foreach ($value->rdsManagers as $manager) {
                $arrayData[$key]['rds_manager_id'] = $manager->id;
            }

            $arrayData[$key]['database_name'] = [];
            $arrayData[$key]['database_id'] = [];
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = 0;
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = 0;
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = 0;
            $arrayData[$key][TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = [];
            $arrayData[$key][TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = [];
            $arrayData[$key][TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = [];
            $arrayData[$key][TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ALL]] = [];

            $count[TYPE_RDS_PERMISSION_DATA] = 0;
            $count[TYPE_RDS_PERMISSION_STRUCTURE] = 0;
            $count[TYPE_RDS_PERMISSION_ADMINISTRATION] = 0;
            foreach ($value->databases as $database) {
                $arrayData[$key]['database_id'] = $database->id;
                $arrayData[$key]['database_name'] = $database->name;
                $arrayData[$key]['status'] = true;
                foreach ($database->rdsPermissions as $permission) {
                    array_push($arrayData[$key][TYPE_RDS_PERMISSION[$permission->type]], $permission->id);
                    $type = $permission->type;
                    if ($type == TYPE_RDS_PERMISSION_ALL) {
                        $count[TYPE_RDS_PERMISSION_DATA] = $countType[TYPE_RDS_PERMISSION_DATA];
                        $count[TYPE_RDS_PERMISSION_STRUCTURE] = $countType[TYPE_RDS_PERMISSION_STRUCTURE];
                        $count[TYPE_RDS_PERMISSION_ADMINISTRATION] = $countType[TYPE_RDS_PERMISSION_ADMINISTRATION];
                    } else {
                        $count[$type]++;
                    }
                }
                $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = $count[TYPE_RDS_PERMISSION_DATA];
                $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = $count[TYPE_RDS_PERMISSION_STRUCTURE];
                $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = $count[TYPE_RDS_PERMISSION_ADMINISTRATION];
            }
        }
        return (new Common)->myPaginate($arrayData);
    }

    public function permissionDetail(array $attributes)
    {
        $rds_manager_id = $attributes['rds_manager_id'];
        $database_name = $attributes['database_name'];
        $user_id = $attributes['user_id'];

        $data = $this->model
            ->where('id', $user_id)
            ->select('id', 'name')
            ->with([
                'rdsManagers' => function ($query) use ($rds_manager_id) {
                    $query->select('rds_manager.id', 'rds_manager.name')
                        ->where('rds_manager.id', $rds_manager_id);
                },
                'databases' => function ($query) use ($database_name) {
                    $query->where('database.name', $database_name)
                        ->with('rdsPermissions:id,name,type');
                }
            ])->get();

        $arrayData = [];
        foreach ($data as $key => $value) {
            $arrayData['user_id'] = $value->id;
            $arrayData['username'] = $value->name;
            $arrayData['rds_manager_id'] = [];
            $arrayData['status'] = false;

            foreach ($value->rdsManagers as $manager) {
                $arrayData['rds_manager_id'] = $manager->id;
            }

            $arrayData['database_name'] = [];
            $arrayData['database_id'] = [];

            $arrayData[TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = [];
            $arrayData[TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = [];
            $arrayData[TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = [];
            $arrayData[TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ALL]] = [];

            foreach ($value->databases as $database) {
                $arrayData['database_id'] = $database->id;
                $arrayData['database_name'] = $database->name;
                $arrayData['status'] = true;
                foreach ($database->rdsPermissions as $permission) {
                    array_push($arrayData[TYPE_RDS_PERMISSION[$permission->type]], $permission->id);
                }
            }
        }
        return (new Common)->myPaginate($arrayData);
    }

    private function getData($rds_manager_id)
    {
        $rdsManager = RDSManager::query()->find($rds_manager_id);
        $rdsManagerLocal = RDSManager::query()
            ->where(RDSManager::URL_END_POINT, config('database.connections.mysql.host'))
            ->where(RDSManager::USERNAME, config('database.connections.mysql.username'))
            ->where(RDSManager::PASSWORD, config('database.connections.mysql.password'))
            ->where(RDSManager::PORT, config('database.connections.mysql.port'))
            ->first();
        $openConnect = ($rds_manager_id != $rdsManagerLocal->id);
        $filePath = @$rdsManager->file->file_path;
        $data = [
            RDSManager::USERNAME => $rdsManager->username,
            RDSManager::PASSWORD => $rdsManager->password,
            RDSManager::PORT => $rdsManager->port,
            RDSManager::URL_END_POINT => $rdsManager->url_end_point,
            RDSManager::EC2_USERNAME => $rdsManager->ec2_username,
            RDSManager::EC2_IP_ADDRESS => $rdsManager->ec2_ip_address,
        ];
        return compact('data', 'filePath', 'openConnect');
    }

    public function getListDatabase($rds_manager_id)
    {
        try {
            $rdsManager = RDSManager::query()->find($rds_manager_id);
            if (!$rdsManager) {
                return ResponseService::responseJsonError(
                    Response::HTTP_NOT_FOUND,
                    trans('messages.mes.data_not_found'),
                    trans('messages.mes.data_not_found'));
            }

            $data = $this->getData($rds_manager_id);
            $query = 'SHOW DATABASES';
            $connect = Common::connectRDS($data['data'], $data['filePath'], $data['openConnect'], $query);
            if ($connect['code'] != CODE_SUCCESS) {
                return $connect;
            }
            $databaseNames = array_map('current', $connect['data']);
            return ResponseService::responseJson(CODE_SUCCESS, $databaseNames);
        } catch (\PDOException $e) {
            return ResponseService::responseJsonError(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function create(array $attributes)
    {
        try {
            $rds_manager_id = $attributes['rds_manager_id'];
            $database_name = $attributes['database_name'];
            $user_id = $attributes['user_id'];
            $permission = $attributes['permission'];

            $dataConnect = $this->getData($rds_manager_id);
            $connect = Common::connectRDS($dataConnect['data'], $dataConnect['filePath'], $dataConnect['openConnect']);
            if ($connect->original['code'] != CODE_SUCCESS) {
                return $connect;
            }
            $pdo = $connect->original['data'];

            $permissionList = RDSPermission::query()->pluck('name', 'id')->toArray();
            $rds_info = RDSInfo::query()->firstOrCreate([
                RDSInfo::USER_ID => $user_id,
                RDSInfo::RDS_MANAGER_ID => $rds_manager_id
            ]);

            $databaseExisted = Database::query()
                ->where(Database::NAME, $database_name)
                ->where(Database::RDS_INFO_ID, $rds_info->id)
                ->exists();
            if ($databaseExisted) {
                return ResponseService::responseJsonError(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('api.viam_rds.database_exist'),
                    trans('api.viam_rds.database_exist')
                );
            }
            $database = Database::query()->create([
                Database::NAME => $database_name,
                Database::RDS_INFO_ID => $rds_info->id
            ]);

            $dataInsert = [];
            $permissionText = '';
            $grantOption = '';
            $isGrantPermission = true;
            foreach ($permission as $p_id) {
                $dataInsert [] = [
                    DatabasePermission::DATABASE_ID => $database->id,
                    DatabasePermission::RDS_PERMISSION_ID => $p_id
                ];
                if ($permissionList[$p_id] != PERMISSION_GRANT) {
                    $permissionText .= $permissionList[$p_id] . ', ';
                    $isGrantPermission = false;
                }
                if (in_array($permissionList[$p_id], [PERMISSION_GRANT, PERMISSION_ALL_PRIVILEGES])) {
                    $grantOption = "WITH GRANT OPTION";
                }
            }
            $permissionText = trim($permissionText, ', ');
            DatabasePermission::query()->insert($dataInsert);

            $query = $pdo->query("SELECT User FROM mysql.user");
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $usernames = array_map('current', $result);
            $name = $this->model->find($user_id)->name;

            if (!in_array($name, $usernames)) {
                $pdo->query("CREATE USER '{$name}'@'localhost' IDENTIFIED BY '12345678';");
            }

            if ($isGrantPermission) {
                $permissionText = 'USAGE';
            }
            $pdo->query("GRANT {$permissionText} ON `{$database_name}`.* TO '{$name}'@'localhost' {$grantOption};");

            Common::stopJobSSHTunnel($dataConnect['openConnect']);
            return ResponseService::responseJson(CODE_SUCCESS,
                trans('messages.mes.create_success'),
                trans('messages.mes.create_success')
            );
        } catch (\Exception $exception) {
            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function update(array $attributes, $user_id)
    {
        try {
            $rds_manager_id = $attributes['rds_manager_id'];
            $database_id = $attributes['database_id'];

            $dataConnect = $this->getData($rds_manager_id);
            $connect = Common::connectRDS($dataConnect['data'], $dataConnect['filePath'], $dataConnect['openConnect']);
            if ($connect->original['code'] != CODE_SUCCESS) {
                return $connect;
            }
            $pdo = $connect->original['data'];

            $checkData = $this->model->where('id', $user_id)
                ->whereHas('databases', function ($e) use ($database_id) {
                    $e->where('database.id', $database_id);
                })->exists();
            if (!$checkData) {
                return ResponseService::responseJson(Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('messages.mes.data_not_found'),
                    trans('messages.mes.data_not_found')
                );
            }

            $permission = $attributes['permission'];
            $permissionList = RDSPermission::query()->pluck('name', 'id')->toArray();
            $permissionGrant = array_search(PERMISSION_GRANT, $permissionList);
            $permissionAllPrivileges = array_search(PERMISSION_ALL_PRIVILEGES, $permissionList);

            $username = $this->model->find($user_id)->name;
            $database_name = Database::query()->find($database_id)->name;
            $databasePermission = DatabasePermission::query()->where(DatabasePermission::DATABASE_ID, $database_id);
            $listPermissionOld = $databasePermission->pluck(DatabasePermission::RDS_PERMISSION_ID)->toArray();
            $databasePermission->delete();

            $dataInsert = [];
            $permissionText = '';
            $grantOption = '';
            $isGrantPermission = true;
            foreach ($permission as $p_id) {
                $dataInsert [] = [
                    DatabasePermission::DATABASE_ID => $database_id,
                    DatabasePermission::RDS_PERMISSION_ID => $p_id
                ];
                if ($permissionList[$p_id] != PERMISSION_GRANT) {
                    $isGrantPermission = false;
                    $permissionText .= $permissionList[$p_id] . ', ';
                }
                if (in_array($permissionList[$p_id], [PERMISSION_GRANT, PERMISSION_ALL_PRIVILEGES])) {
                    $grantOption = "WITH GRANT OPTION";
                }
            }
            $permissionText = trim($permissionText, ', ');
            DatabasePermission::query()->insert($dataInsert);

            if (!$permission) { // permission = null => delete RDS
                $this->deleteAccountRDS($database_id, $user_id, $username, $rds_manager_id, $pdo);
            } else {
                $pdo->query("REVOKE ALL PRIVILEGES ON `{$database_name}`.* FROM '{$username}'@'localhost';");

                if (array_intersect([$permissionGrant, $permissionAllPrivileges], $listPermissionOld)) {
                    $pdo->query("REVOKE GRANT OPTION ON `{$database_name}`.* FROM '{$username}'@'localhost';");
                }
                if ($isGrantPermission) {
                    $permissionText = 'USAGE';
                }
                $pdo->query("GRANT {$permissionText} ON `{$database_name}`.* TO '{$username}'@'localhost' {$grantOption};");
            }

            Common::stopJobSSHTunnel($dataConnect['openConnect']);
            return ResponseService::responseJson(CODE_SUCCESS,
                trans('messages.mes.update_success'),
                trans('messages.mes.update_success')
            );
        } catch (\Exception $exception) {
            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

    }

    public function deleteAccountRDS($database_id, $user_id, $username, $rds_manager_id, \PDO $pdo)
    {
        Database::query()->find($database_id)->delete();
        $dbOtherOfAccountExist = $this->model->where('id', $user_id)
            ->whereHas('databases', function ($e) use ($database_id) {
                $e->where('database.id', '!=', $database_id);
            })->exists();
        if (!$dbOtherOfAccountExist) {
            RDSInfo::query()->where(RDSInfo::USER_ID, $user_id)
                ->where(RDSInfo::RDS_MANAGER_ID, $rds_manager_id)
                ->delete();
            $pdo->query("DROP USER '{$username}'@'localhost'");

            return true;
        }
        return false;
    }

    public function deleteRDS(array $attributes, $user_id)
    {
        try {
            $rds_manager_id = $attributes['rds_manager_id'];
            $database_id = $attributes['database_id'];

            $dataConnect = $this->getData($rds_manager_id);
            $connect = Common::connectRDS($dataConnect['data'], $dataConnect['filePath'], $dataConnect['openConnect']);
            if ($connect->original['code'] != CODE_SUCCESS) {
                return $connect;
            }
            $pdo = $connect->original['data'];

            $checkData = $this->model->where('id', $user_id)
                ->whereHas('databases', function ($e) use ($database_id) {
                    $e->where('database.id', $database_id);
                })->exists();
            if (!$checkData) {
                return ResponseService::responseJson(Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('messages.mes.data_not_found'),
                    trans('messages.mes.data_not_found')
                );
            }

            $username = $this->model->find($user_id)->name;
            $database = Database::query()->find($database_id);
            $database_name = $database->name;

            $databasePermission = DatabasePermission::query()->where(DatabasePermission::DATABASE_ID, $database_id);
            $listPermissionOld = $databasePermission->pluck(DatabasePermission::RDS_PERMISSION_ID)->toArray();
            $databasePermission->delete();

            $isDeleteRDS = $this->deleteAccountRDS($database_id, $user_id, $username, $rds_manager_id, $pdo);
            if (!$isDeleteRDS) {
                $permissionList = RDSPermission::query()->pluck('name', 'id')->toArray();
                $permissionGrant = array_search(PERMISSION_GRANT, $permissionList);
                $permissionAllPrivileges = array_search(PERMISSION_ALL_PRIVILEGES, $permissionList);

                $pdo->query("REVOKE ALL PRIVILEGES ON `{$database_name}`.* FROM '{$username}'@'localhost';");
                if (array_intersect([$permissionGrant, $permissionAllPrivileges], $listPermissionOld)) {
                    $pdo->query("REVOKE GRANT OPTION ON `{$database_name}`.* FROM '{$username}'@'localhost';");
                }
            }

            Common::stopJobSSHTunnel($dataConnect['openConnect']);
            return ResponseService::responseJson(CODE_SUCCESS,
                trans('messages.mes.delete_success'),
                trans('messages.mes.delete_success')
            );
        } catch (\Exception $exception) {
            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
