<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\Database;
use App\Models\DatabasePermission;
use App\Models\HistoryEditReport;
use App\Models\RDSInfo;
use App\Models\RDSManager;
use App\Models\RDSPermission;
use App\Models\User;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use App\Repositories\Contracts\VIAMRDSRepositoryInterface;
use Aws\Rds\RdsClient;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use function Clue\StreamFilter\fun;

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
            $arrayData[$key]['permission_id'] = [];
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = 0;
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = 0;
            $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = 0;
            $count[TYPE_RDS_PERMISSION_DATA] = 0;
            $count[TYPE_RDS_PERMISSION_STRUCTURE] = 0;
            $count[TYPE_RDS_PERMISSION_ADMINISTRATION] = 0;
            foreach ($value->databases as $database) {
                $arrayData[$key]['database_id'] = $database->id;
                $arrayData[$key]['database_name'] = $database->name;
                $arrayData[$key]['status'] = true;
                foreach ($database->rdsPermissions as $permission) {
                    $arrayData[$key]['permission_id'][] = $permission->id;
                    $type = $permission->type;
                    if ($type == TYPE_RDS_PERMISSION_ALL) {
                        $count[TYPE_RDS_PERMISSION_DATA] = $countType[TYPE_RDS_PERMISSION_DATA];
                        $count[TYPE_RDS_PERMISSION_STRUCTURE] = $countType[TYPE_RDS_PERMISSION_STRUCTURE];
                        $count[TYPE_RDS_PERMISSION_ADMINISTRATION] = $countType[TYPE_RDS_PERMISSION_ADMINISTRATION];
                    } else {
                        $count[$type]++;
                    }
                    $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_DATA]] = $count[TYPE_RDS_PERMISSION_DATA];
                    $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_STRUCTURE]] = $count[TYPE_RDS_PERMISSION_STRUCTURE];
                    $arrayData[$key]['count_' . TYPE_RDS_PERMISSION[TYPE_RDS_PERMISSION_ADMINISTRATION]] = $count[TYPE_RDS_PERMISSION_ADMINISTRATION];
                }
            }
        }
        return (new Common)->myPaginate($arrayData);
    }

    public function getListDatabase(array $attributes)
    {
        $databases = DB::select('SHOW DATABASES');
        $databaseNames = array_map('current', $databases);
        $data = [];

        foreach ($databaseNames as $databaseName) {
            $data[] = $databaseName;
        }
        return $data;
    }

    public function create(array $attributes)
    {
        $rds_manager_id = $attributes['rds_manager_id'];
        $database_name = $attributes['database_name'];
        $user_id = $attributes['user_id'];
        $permission = $attributes['permission'];

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
        foreach ($permission as $p_id) {
            $dataInsert [] = [
                DatabasePermission::DATABASE_ID => $database->id,
                DatabasePermission::RDS_PERMISSION_ID => $p_id
            ];
            $permissionText .= $permissionList[$p_id] . ', ';
        }
        $permissionText = trim($permissionText, ', ');
        DatabasePermission::query()->insert($dataInsert);

        try {
            $users = DB::select("SELECT User FROM mysql.user");
            $usernames = array_map('current', $users);
            $userCreate = $this->model->find($user_id);
            $name = $userCreate->name;
            if (!in_array($name, $usernames)) {
                DB::statement("CREATE USER '{$name}'@'localhost' IDENTIFIED BY '12345678';");
            }
            DB::statement("GRANT {$permissionText} ON `{$database_name}`.* TO '{$name}'@'localhost';");

            return ResponseService::responseJson(CODE_SUCCESS,
                trans('messages.mes.create_success'),
                trans('messages.mes.create_success')
            );
        } catch (\Exception $exception) {
            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function update(array $attributes, $id)
    {
        dd('update');
        return parent::update($attributes, $id); // TODO: Change the autogenerated stub
    }

    public function delete($id)
    {
        dd('delete');
        return parent::delete($id); // TODO: Change the autogenerated stub
    }
}
