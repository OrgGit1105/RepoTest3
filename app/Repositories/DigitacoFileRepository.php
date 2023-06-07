<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace Repository;

use App\Imports\FileImport;
use App\Models\DigitacoFile;
use App\Repositories\Contracts\DigitacoFileRepositoryI;
use Helper\Common;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class DigitacoFileRepository extends BaseRepository implements DigitacoFileRepositoryI
{

  public function __construct(Application $app)
  {
    parent::__construct($app);

  }

  /**
   * Instantiate model
   *
   * @param DigitacoFile $model
   */

  public function model()
  {
    return DigitacoFile::class;
  }

  public function SaveFile($attributes)
  {
    $this->insert($attributes);
    return true;
  }

  public function getAll($request)
  {
    return $this->model->orderBy('getting_date', 'desc')->paginate($request->per_page);
  }

  public function getDigitaco($id, $type)
  {
    $data = $this->model->find($id);
    $headings = [];
    $content = [];
    if ($type == DigitacoFile::TYPE_POINT) {
      Common::changeInputEncodingByFile(Storage::path($data->file_path_data_point));
      $content = Excel::toArray(new FileImport, Storage::path($data->file_path_data_point));
      $headings = (new HeadingRowImport)->toArray($data->file_path_data_point);
    } else {
      Common::changeInputEncodingByFile(Storage::path($data->file_path_data_driving));
      $content = Excel::toArray(new FileImport, Storage::path($data->file_path_data_driving));
      $headings = (new HeadingRowImport)->toArray($data->file_path_data_driving);
    }
    return ['header' => $headings, 'content' => $content];
  }
}
