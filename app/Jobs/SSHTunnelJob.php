<?php

namespace App\Jobs;

use App\Models\RDSManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SSHTunnelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $port;
    private $urlEndPoint;
    private $ec2Username;
    private $ec2IpAddress;

    /**
     * Create a new job instance.
     *
     * @param array $attributes
     * @param string $filePath
     */
    public function __construct(array $attributes, string $filePath)
    {
        $this->filePath = $filePath;
        $this->port = $attributes[RDSManager::PORT];
        $this->urlEndPoint = $attributes[RDSManager::URL_END_POINT];
        $this->ec2Username = $attributes[RDSManager::EC2_USERNAME];
        $this->ec2IpAddress = $attributes[RDSManager::EC2_IP_ADDRESS];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = "ssh -i $this->filePath -L $this->port:$this->urlEndPoint:3306 $this->ec2Username@$this->ec2IpAddress -y -f -N";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::info("ssh -i $this->filePath -L $this->port:$this->urlEndPoint:3306 $this->ec2Username@$this->ec2IpAddress -y -f");
        }
    }
}
