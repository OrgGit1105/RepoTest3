<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Repository\GetMailRepository;

class GetDataMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetMail:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Mail Service .';

    /**
     * Create a new command instance.
     *
     * @return void
     */
  protected $getMailRepository;

  public function __construct(GetMailRepository $getMailRepository)
  {
    parent::__construct();
    $this->getMailRepository = $getMailRepository;
  }

    /**
     * Execute the console command.
     *
     * @return int
     */
  public function handle()
  {
    $this->getMailRepository->GetMailPOP3();
    $this->info('Successfully get mail server.');
  }
}
