<?php


namespace App\Repositories\Contracts;


interface EmployeeRepositoryInterface extends BaseRepositoryInterface
{
  public function index($request);

  public function getAll($request);

  public function getById($id);

//  public function getByEmployeeId($employee_id,$month_year);

}
