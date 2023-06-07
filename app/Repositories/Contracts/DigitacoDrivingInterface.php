<?php


namespace App\Repositories\Contracts;


interface DigitacoDrivingInterface extends BaseRepositoryInterface
{
  public function getByFileName($filename);
}
