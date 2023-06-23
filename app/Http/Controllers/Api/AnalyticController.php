<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Requests\ArrivingReportRequest;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    /**
     * var Repository
     */
    protected $repository;

    public function __construct(ArrivingReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArrivingReportRequest $request)
    {
        $data = $this->repository->getListAnalytic($request);

        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $in_time = $request['in_time'];
        $out_time = $request['out_time'];
        $a = Carbon::create($out_time)->diff(Carbon::create($in_time));
        dd('aaa', $a);
        // logic: if type = 1|2|3 number day = out_time - in_time + 1
        // logic: if type = 4|5|6 number day = 0.5

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
