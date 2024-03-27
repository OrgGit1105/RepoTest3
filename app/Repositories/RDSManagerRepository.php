<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\HistoryEditReport;
use App\Models\RDSManager;
use App\Models\User;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use Aws\Rds\RdsClient;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
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

    private function checkConnect(array $attributes) {
//        $param = Common::configAwsSDK();
//        $ssmClient = new SsmClient($param);
//        $instanceId = "i-0553d99830b279164";

//        $endpoint = $attributes['url_end_point'];
//        $port = $attributes['port'];
//        $dsn = "mysql:host={$endpoint};port={$port}";
        $process = new Process([
            'ssh',
            '-i',
            'tests/atmtc-admin.pem',
            '-L',
            '3308:atmtc.ct0kdec3soaj.ap-northeast-1.rds.amazonaws.com:3306',
            'admin@18.176.73.142',
        ]);
        $process->start();
        sleep(5);
        $errorOutput = $process->getErrorOutput();
        echo $errorOutput;
        if ($process->isRunning()) {
            $host = 'localhost';
            $port = '3308';
            $database = '';
            $username = 'admin';
            $password = 'atmtc22222';

            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new \PDO($dsn, $username, $password, $options);
            $stmt = $pdo->query("SHOW DATABASES");
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            dd($result);
        } else {
            echo 'Failed to start SSH tunnel.';
        }
        $process = new Process([
            'pkill',
            '-f',
            'ssh',
        ]);
        $process->start();
//        $host = 'localhost';
//        $port = '3308';
//        $database = '';
//        $username = 'admin';
//        $password = 'atmtc22222';
//
//        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
//        $options = [
//            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//            \PDO::ATTR_EMULATE_PREPARES => false,
//        ];
//
//        $pdo = new \PDO($dsn, $username, $password, $options);
//        $stmt = $pdo->query("SHOW DATABASES");
//        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//        dd($result);
//        $command = "ssh -i $key -L 3306:atmtc.ct0kdec3soaj.ap-northeast-1.rds.amazonaws.com:3306 admin@18.176.73.142";
//        exec($command);
//        exec("mysql -h atmtc.ct0kdec3soaj.ap-northeast-1.rds.amazonaws.com -P 3306 -u admin -patmtc22222", $output);
//        dd($output);
//        $parameters = [
//            'InstanceIds' => [$instanceId],
//            'DocumentName' => 'AWS-RunShellScript',
//            'commands' => ["mysql -h {$endpoint} -P {$port} -u {$attributes['username']} --password={$attributes['password']}", "show databases;"]
//        ];
//        $rds = RdsClient::factory();
//        $dbInstance = $rds->describeDBInstances([
//
//        ]);
//        dd($parameters);
//        $response = $ssmClient->sendCommand($parameters);
//        $commandId = $response['Command']['CommandId'];
//        $waitTime = 1;
//        $maxAttempts = 10;
//        $attempts = 0;
//        do {
//            sleep($waitTime);
//            $output = $ssmClient->getCommandInvocation([
//                'CommandId' => $commandId,
//                'InstanceId' => $instanceId,
//            ]);
//            $status = $output['Status'];
//            if($status == 'Success') {
//                dd($output);
//            }
//            $attempts++;
//        } while ($status != 'Success' && $attempts <= $maxAttempts);
//        try {
//            $pdo = new \PDO($dsn, $attributes['username'], $attributes['password']);
//            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//            return ResponseService::responseJson(CODE_SUCCESS);
//        } catch (\PDOException $e) {
//            return ResponseService::responseJsonError(Response::HTTP_INTERNAL_SERVER_ERROR, trans('api.rds_manager.connect_failed'), trans('api.rds_manager.connect_failed'));
//        }
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

        $data = $rdsManager->whereHas('users', fn ($query) => $query->where('rds_manager_id', $id))->exists();
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

        $data = $rdsManager->whereHas('users', fn ($query) => $query->where('rds_manager_id', $id))->exists();
        if($data) {
            $msg = trans('api.rds_manager.action_error', ['action' => 'delete']);
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $msg, $msg);
        }
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS,null, trans('messages.mes.delete_success'));
    }
}
