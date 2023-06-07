<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-08-02
 */

namespace App\Repositories\Contracts;


interface DigitacoPointRepositoryInterface extends BaseRepositoryInterface
{
  public function getByFileName($filename);
}
