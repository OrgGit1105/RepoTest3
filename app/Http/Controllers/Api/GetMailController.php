<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\GetMailRepositoryI;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class GetMailController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(GetMailRepositoryI $repository)
    {
        $this->repository = $repository;
    }

}
