<?php

namespace App\Jobs;

use Helper\ResponseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SSHTunnelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $username;
    private $password;
    private $port;
    private $urlEndPoint;
    private $ec2Username;
    private $ec2IpAddress;
    private $host;
    private $database;

    /**
     * Create a new job instance.
     *
     * @param array $attributes
     * @param int $port
     * @param string $filePath
     */
    public function __construct(array $attributes, int $port, string $filePath)
    {
        $this->filePath = $filePath;
        $this->username = $attributes['username'];
        $this->password = $attributes['password'];
        $this->port = $port;
        $this->urlEndPoint = $attributes['url_end_point'];
        $this->ec2Username = $attributes['ec2_username'];
        $this->ec2IpAddress = $attributes['ec2_ip_address'];
        $this->host = config('database.connections.mysql.host');
        $this->database = '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            exec("ssh -i $this->filePath -L $this->port:$this->urlEndPoint:3306 $this->ec2Username@$this->ec2IpAddress", $output);
            $connection = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);
            if (!$connection) {
                return ResponseService::responseJsonError(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('api.rds_manager.connect_failed'),
                    trans('api.rds_manager.connect_failed'));
            }
            mysqli_close($connection);
//        $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->database;chaset=utf8mb4";
//        $option = [
//            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//            \PDO::ATTR_EMULATE_PREPARES => false,
//        ];
//
//        $pdo = new \PDO($dsn, $this->username, $this->password, $option);
//        $stmt = $pdo->query('SHOW DATABASES');
//        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//        dd($result);
            exec('exist');
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (\Exception $exception) {
            return ResponseService::responseJson(500, $exception->getMessage());
        }

    }
}
