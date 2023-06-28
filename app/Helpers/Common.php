<?php


namespace Helper;


use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
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
}
