<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-25
 */

namespace Repository;

use App\Models\ConfigRange;
use App\Repositories\Contracts\ConfigRangeRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ConfigRangeRepository extends BaseRepository implements ConfigRangeRepositoryInterface
{
  protected $model;

  public function __construct(Application $app)
  {
    parent::__construct($app);
  }

  /**
   * Instantiate model
   *
   * @param ConfigRange $model
   */

  public function model()
  {
    return ConfigRange::class;
  }

  public function getByType($type)
  {
    return $this->model->select('ranges','number_of_studies','average_total')->where('type', $type);
  }

  public function getNullByType($type)
  {
    return $this->model->select('ranges','number_of_studies','average_total')->where('type', $type)->whereNull('rank')->first();
  }
}
