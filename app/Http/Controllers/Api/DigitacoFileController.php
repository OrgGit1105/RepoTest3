<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DigitacoFileRequest;
use App\Models\DigitacoFile;
use App\Repositories\Contracts\DigitacoFileRepositoryI;
use App\Http\Resources\BaseResource;
use App\Http\Resources\DigitacoFileResource;
use Helper\Common;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitacoFileController extends Controller
{

  /**
   * var Repository
   */
  protected $repository;

  public function __construct(DigitacoFileRepositoryI $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @OA\Get(
   *   path="/api/digitaco_data",
   *   tags={"DigitacoData"},
   *   summary="List digitaco_data",
   *   operationId="digitaco_data_index",
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"getting_date":  ".............","file_name":  ".............","file_path":  ".............","data_type":  ".............","status":  "............."}}
   *     )
   *   ),
   *   @OA\Parameter(
   *     name="page",
   *     in="query",
   *     @OA\Schema(
   *      type="integer",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="per_page",
   *     in="query",
   *     @OA\Schema(
   *      type="integer",
   *     ),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Login false",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Username or password invalid"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(DigitacoFileRequest $request)
  {
    $data = $this->repository->getAll($request);
    return $this->responseJson(200, BaseResource::collection($data));
  }


  /**
   * @OA\Get(
   *   path="/api/digitaco_data/detail/{id}/{type}",
   *   tags={"DigitacoData"},
   *   summary="Detail DigitacoData",
   *   operationId="digitaco_data_show",
   *   @OA\Parameter(
   *     name="id",
   *     in="path",
   *     required=true,
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *      *   @OA\Parameter(
   *     name="type",
   *     in="path",
   *     required=true,
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="page",
   *     in="query",
   *     @OA\Schema(
   *      type="integer",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="per_page",
   *     in="query",
   *     @OA\Schema(
   *      type="integer",
   *     ),
   *   ),
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"name":"......"}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Login false",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Username or password invalid"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function detail($id, $type, Request $request)
  {
    try {
      $data = $this->repository->getDigitaco($id, $type);
      $contents = Common::paginate($data['content'][0], $request->per_page, $request->page);
      return $this->responseJson(200, ['header' => $data['header'], 'contents' => BaseResource::collection($contents)]);
    } catch (\Exception $exception) {
      return $this->responseJsonError(CODE_CREATE_FAILED, trans('Digitaco point not found'));
    }
  }


}
