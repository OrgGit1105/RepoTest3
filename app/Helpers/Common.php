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
    /**
     * @param UploadedFile $file
     * @param $path
     * @param null $shopId
     * @return mixed
     */
//    public static function uploadFile(UploadedFile $file, $path = '', $shopId = null)
//    {
//        $shopId = $shopId ?? data_get(Auth::user(), 'shop.id');
//        $fileName = now()->format('d-m-Y--H-i-s') . "_" . $file->getClientOriginalName();
//        return $file->storeAs("shops/{$shopId}/{$path}", $fileName);
//    }

    public static function uploadFile(UploadedFile $file, $path = '', $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $fileName = time().'.'.$file->getClientOriginalExtension();
        return $file->storeAs($path, $fileName);
    }

    public static function checkTokenFB($token){
        try{
            $client = new \GuzzleHttp\Client();
            $url    = 'https://graph.facebook.com/me?access_token=' . $token;
            $res    = $client->request('GET', $url);
            if ($res->getStatusCode() == 200) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }


  /**
   * Change input encoding for by file csv, xlsx
   *
   *
   * @param $path : string path to file check encoding
   */
  public static function changeInputEncodingByFile($pathFile)
  {
    $fileContent = file_get_contents($pathFile);
    $enc = mb_detect_encoding($fileContent, mb_list_encodings(), true);
    \Config::set('excel.imports.csv.input_encoding', $enc);
  }

  public static function convertEncodingJpToUtf8($string, $internalEncoding = "UTF-8", $convertEncoding = "ISO-2022-JP")
  {
    mb_internal_encoding($internalEncoding);
    $out_encoding = mb_convert_encoding($string, $internalEncoding, $convertEncoding);
    return mb_decode_mimeheader($out_encoding);
  }
  public static function paginate($items, $perPage, $page, $options = [])
  {
    $perPage=$perPage?$perPage:100;
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
  }
}
