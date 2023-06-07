<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace App\Repositories\Contracts;


interface DigitacoFileRepositoryI extends BaseRepositoryInterface
{
  //
  public function getAll($request);

  public function getDigitaco($id, $type);
}
