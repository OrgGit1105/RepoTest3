<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace App\Http\Controllers\Api;

use App\Exports\AnalyticExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyticRequest;
use App\Repositories\Contracts\AnalyticRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AnalyticResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(AnalyticRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/analytic",
     *   tags={"Analytic"},
     *   summary="List analytic",
     *   operationId="analytic_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
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
    public function index(AnalyticRequest $request)
    {
        $data = $this->repository->getListAnalytic($request);
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Get(
     *   path="/api/analytic/{id}",
     *   tags={"Analytic"},
     *   summary="Detail Analytic",
     *   operationId="analytic_show",
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
            $department = $this->repository->find($id);
            return $this->responseJson(200, new BaseResource($department));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Post(
     *   path="/api/analytic/{id}",
     *   tags={"Analytic"},
     *   summary="Update Analytic",
     *   operationId="analytic_update",
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
    public function update(AnalyticRequest $request, $id)
    {
        $attributes = $request->except([]);
        $data = $this->repository->update($attributes, $id);
        return $this->responseJson(200, new BaseResource($data));
    }

    /**
     * @OA\Delete(
     *   path="/api/analytic/{id}",
     *   tags={"Analytic"},
     *   summary="Delete ..............",
     *   operationId="analytic_delete",
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
     * @OA\Get(
     *   path="/api/analytic/emotions",
     *   tags={"Analytic"},
     *   summary="List emotion",
     *   operationId="analytic_emotion",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
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
    public function getEmotions(AnalyticRequest $request)
    {
        $data = $this->repository->getEmotions($request);
        return $this->responseJson(200, AnalyticResource::collection($data));
    }

    /**
     * @OA\Get(
     *   path="/api/analytic/download",
     *   tags={"Analytic"},
     *   summary="Download ..............",
     *   operationId="analytic_download",
     *   @OA\Parameter(
     *     name="start_date",
     *     in="query",
     *   description="Y-m-d",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="end_date",
     *     in="query",
     *   description="Y-m-d",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="user_id",
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
     *      example={"code":200,"data":"Send request success"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function download(Request $request)
    {
        $data = $this->repository->getListAnalytic($request);
        $fileName = 'analytic.xlsx';
        return Excel::download(
            new AnalyticExport($data),
            $fileName,
            null,
            ['Content-Type' => 'application/octet-stream; charset=SJIS-win', 'Content-Transfer-Encoding' => 'Binary', 'Charset' => 'SJIS-win']
        );
    }

}
