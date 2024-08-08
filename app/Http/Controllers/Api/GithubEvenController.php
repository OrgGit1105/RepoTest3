<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\GithubEvenRepositoryInterface;
use Illuminate\Http\Request;

class GithubEvenController extends Controller
{
    public $repository;
    public function __construct(GithubEvenRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createIssues(Request $request){
        return $this->repository->createIssues($request);
    }

}
