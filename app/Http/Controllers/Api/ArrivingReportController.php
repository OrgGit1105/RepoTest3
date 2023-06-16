<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivingReportRequest;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ArrivingReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArrivingReportController extends Controller
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
     * @OA\Get(
     *   path="/api/arriving_report",
     *   tags={"ArrivingReport"},
     *   summary="List arriving_report",
     *   operationId="arriving_report_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="start_date",
     *     in="query",
     *   description="y-m-d",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="end_date",
     *     in="query",
     *   description="y-m-d",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="key_search",
     *     in="query",
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
    public function index(ArrivingReportRequest $request)
    {
        $data = $this->repository->getList($request);
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/arriving_report",
     *   tags={"ArrivingReport"},
     *   summary="Add new arriving_report",
     *   operationId="arriving_report_create",
     *   @OA\Parameter(name="name", in="query", required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name": "......"}}
     *     )
     *   ),
     *   security={},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(ArrivingReportRequest $request)
    {
        try {
//            $data = $this->repository->create($request->all());
//            return $this->responseJson(200, new ArrivingReportResource($data));
            return $this->repository->create($request->all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/arriving_report/{id}",
     *   tags={"ArrivingReport"},
     *   summary="Detail ArrivingReport",
     *   operationId="arriving_report_show",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
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
    public function show($id)
    {
        try {
            $data = $this->repository->detail($id);
            return $this->responseJson(200, new BaseResource($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\PUT(
     *   path="/api/arriving_report/{id}",
     *   tags={"ArrivingReport"},
     *   summary="Update ArrivingReport",
     *   operationId="arriving_report_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"name":"string"},
     *          @OA\Schema(
     *            required={"name"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":  "............."}}
     *     ),
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Access Deny permission",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Access Deny permission"}
     *     ),
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ArrivingReportRequest $request, $id)
    {
        $attributes = $request->except([]);
//        $data = $this->repository->update($attributes, $id);
//        if (!$data){
//          return $this->responseJsonError(Response::HTTP_NOT_FOUND, "report not found", "report not found");
//        }
//        return $this->responseJson(200, new BaseResource($data));
        return $this->repository->update($attributes, $id);
    }

    /**
     * @OA\Delete(
     *   path="/api/arriving_report/{id}",
     *   tags={"ArrivingReport"},
     *   summary="Delete ..............",
     *   operationId="arriving_report_delete",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":"Send request success"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return $this->responseJson(200, null, trans('messages.mes.delete_success'));
    }
  /**
   * @OA\Post(
   *   path="/api/arriving_report/download",
   *   tags={"ArrivingReport"},
   *   summary="Download ..............",
   *   operationId="arriving_report_download",
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":"Send request success"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function download()
  {

  }
}
