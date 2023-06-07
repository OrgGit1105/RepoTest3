<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-08-02
 */

namespace Repository;

use App\Imports\FileImport;
use App\Models\DigitacoFile;
use App\Models\DigitacoPoint;
use App\Repositories\Contracts\DigitacoPointRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Helper\Common;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DigitacoPointRepository extends BaseRepository implements DigitacoPointRepositoryInterface
{
  protected $digitaco_file;

  public function __construct(Application $app, DigitacoFileRepository $digitacoFileRepository)
  {
    parent::__construct($app);
    $this->digitaco_file = $digitacoFileRepository;

  }

  /**
   * Instantiate model
   *
   * @param DigitacoPoint $model
   */

  public function model()
  {
    return DigitacoPoint::class;
  }

  public function getByFileName($filename)
  {
    // TODO: Implement getById() method.

  }

}
