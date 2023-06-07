<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace App\Repositories\Contracts;


interface CompanyBranchRepositoryInterface extends BaseRepositoryInterface
{
  //
  public function getByRole();

  public function getByUser();

  public function orderByRole();
}
