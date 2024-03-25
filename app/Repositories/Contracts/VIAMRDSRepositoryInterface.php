<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Repositories\Contracts;


interface VIAMRDSRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteRDS(array $attributes, $id);
}
