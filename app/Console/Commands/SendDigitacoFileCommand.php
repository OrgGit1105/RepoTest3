<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use Repository\GetMailRepository;

class SendDigitacoFileCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'DigitacoFile:sendFile';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send Digitaco File';
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
    $this->getMailRepository->sendFile();
    $this->info('Send digitaco file');
  }
}
