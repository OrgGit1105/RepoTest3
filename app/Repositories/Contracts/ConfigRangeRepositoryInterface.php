<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-25
 */

namespace App\Repositories\Contracts;


interface ConfigRangeRepositoryInterface extends BaseRepositoryInterface
{
  //
  public function getNullByType($type);

  public function getByType($type);
}
