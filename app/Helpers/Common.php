<?php


namespace Helper;


use Aws\Iam\IamClient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Common
{
    public function myPaginate($items, $perPage = 20, $page = null, $options = [])
    {
        $result = $this->paginate($items, $perPage, $page);

        return [
            'result' => array_values($result->all()),
            'pagination' => [
                'display' => $result->count(),
                'total_records' => $result->total(),
                'per_page' => $perPage,
                'current_page' => $result->currentPage(),
                'total_pages' => $result->lastPage()
            ]
        ];
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function configAwsSDK()
    {
        if (!App::environment('local')) {
            $param = [
                'version' => 'latest',
                'region' => config('services.aws.AWS_DEFAULT_REGION')
            ];
        } else {
            $param = [
                'version' => 'latest',
                'region' => config('services.aws.AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => config('services.aws.AWS_ACCESS_KEY_ID'),
                    'secret' => config('services.aws.AWS_SECRET_ACCESS_KEY'),
                ]
            ];
        }
        return $param;
    }
}
