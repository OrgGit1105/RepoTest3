<?php


namespace App\Repositories;


use App\Imports\DigitacoDrivingImport;
use App\Models\DigitacoDriving;
use App\Models\DigitacoFile;
use App\Repositories\Contracts\DigitacoDrivingInterface;
use Helper\Common;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Repository\BaseRepository;
use Repository\DigitacoFileRepository;

class DigitacoDrivingRepository extends BaseRepository implements DigitacoDrivingInterface
{
  protected $digitaco_file;

  public function __construct(Application $app, DigitacoFileRepository $digitacoFileRepository)
  {
    parent::__construct($app);
    $this->digitaco_file = $digitacoFileRepository;
  }

  public function model()
  {
    // TODO: Implement model() method.
    return DigitacoDriving::class;
  }

  public function getByFileName($filename)
  {
    // TODO: Implement getById() method.
  }
}
