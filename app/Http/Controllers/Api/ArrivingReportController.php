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
use App\Exports\workingTimes;
use Maatwebsite\Excel\Facades\Excel;

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
     *     name="user_id",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
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

        return $this->responseJson(200, $data);
    }

    /**
     * @OA\Post(
     *   path="/api/arriving_report",
     *   tags={"ArrivingReport"},
     *   summary="Add new arriving_report",
     *   operationId="arriving_report_create",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *            required={"user_id", "type_date", "in_time"},
     *            @OA\Property(
     *              property="user_id",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="type_date",
     *              format="integer",
     *              description="1:working time, 2: remote, 3: take off, 4: special off",
     *              enum={1,2,3,4},
     *              example=1,
     *            ),
     *            @OA\Property(
     *              property="in_time",
     *              format="string",
     *             description="YYYY-mm-dd H:i:s",
     *            ),
     *            @OA\Property(
     *              property="out_time",
     *              format="string",
     *              description="YYYY-mm-dd H:i:s",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"user_id":1,"in_time":"2023-06-16 08:30:00","out_time":"2023-06-16 18:00:00","status":1,"created_at":1686898871,"id":4}}
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
     *      example={"code":200,"data":{"result":{"id":1,"user_id":3,"in_time":"2023-06-16 13:46:14","out_time":"2023-06-16 18:00:00","remark":null,"registration_type":null,"link_face_in":null,"link_face_out":null,"status":1,"created_at":"2023-06-16 13:17:00","updated_at":"2023-06-16 15:46:14","deleted_at":null,"user":{"id":3,"name":"staff","email":"staff@gmail.com","role_id":2,"retirement_date":null,"status":1,"created_at":null,"updated_at":null,"deleted_at":null}}}}
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
            $addFormatResult['result'] = $data;
            return $this->responseJson(200, new BaseResource($addFormatResult));
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
     *          example={"user_id":"integer", "in_time": "string", "out_time": "string", "type_date": "string", "remark": "string"},
     *          @OA\Schema(
     *            required={"user_id", "type_date","in_time"},
     *            @OA\Property(
     *              property="user_id",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="type_date",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="in_time",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="out_time",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="remark",
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
     *      example={"code":200,"data":{"id":1,"user_id":3,"in_time":"2023-06-16 08:30:00","out_time":"2023-06-16 18:00:00","remark":null,"registration_type":null,"link_face_in":null,"link_face_out":null,"status":1,"created_at":1686889020,"updated_at":1686897974,"deleted_at":null}}
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
        $attributes = $request->except(['user_id']);
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
        try {
            $this->repository->delete($id);
            return $this->responseJson(200, null, trans('messages.mes.delete_success'));
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * @OA\Get(
     *   path="/api/arriving_report/download",
     *   tags={"ArrivingReport"},
     *   summary="Download ..............",
     *   operationId="arriving_report_download",
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
     *     name="key_search",
     *     in="query",
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
    $data = $this->repository->downloadArrivingreport($request);
    $fileName= 'arrivingreport.xlsx';
    return Excel::download(new workingTimes($data), $fileName, null,
           ['Content-Type' => 'application/octet-stream; charset=SJIS-win', 'Content-Transfer-Encoding' => 'Binary', 'Charset' => 'SJIS-win']);
  }
}
