<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-07-26
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyBranchRequest;
use App\Repositories\Contracts\CompanyBranchRepositoryInterface;
use App\Repositories\Contracts\RetirementPredictionChartRepositoryI;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class RetirementPredictionChartController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(RetirementPredictionChartRepositoryI $repository)
    {
        $this->repository = $repository;
    }




  /**
   * @OA\Get(
   *   path="/api/retirement_prediction_chart",
   *   tags={"Retirement Prediction Chart"},
   *   summary="Danh sách ................",
   *   operationId="retirement_prediction_chart_index",
   *   @OA\Parameter(
   *     name="month",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *      format="array",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *      format="array",
   *     ),
   *   ),
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *       example={"code":200,"data":{"list employee with risk score equal and exceed the threshold value": ".............", "number of employee with risk score equal and exceed the threshold value (the_blue)":  ".............",
   *   "list employee with risk score equal and exceed and lower than the threshold value": ".............", "number of employee with risk score equal and exceed and lower than the threshold value": ".............",  "the_orange": "............."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Đăng nhập thất bại",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    $data = $this->repository->detail($request);
    if (is_int($data)){
      if ($data==1){
        return $this->responseJsonError(CODE_CREATE_FAILED, trans('numberofemployee >= threshold value must be > 0'));
      }else{
        return $this->responseJsonError(CODE_CREATE_FAILED, trans('numberofemployee >= threshold value not exists'));
      }
    }
    return $this->responseJson(CODE_SUCCESS, new BaseResource($data));
  }

  /**
   * @OA\Get(
   *   path="/api/retirement_prediction_chart/show",
   *   tags={"Retirement Prediction Chart"},
   *   summary="retaiment list max month year ................",
   *   operationId="retirement_prediction_chart_show",
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *       example={"code":200,"data":{"list employee with risk score equal and exceed the threshold value": ".............", "number of employee with risk score equal and exceed the threshold value (the_blue)":  ".............",
   *   "list employee with risk score equal and exceed and lower than the threshold value": ".............", "number of employee with risk score equal and exceed and lower than the threshold value": ".............",  "the_orange": "............."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Đăng nhập thất bại",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Request $request)
  {
    $data = $this->repository->show($request);
    return $this->responseJson(CODE_SUCCESS, new BaseResource($data));
  }
}
