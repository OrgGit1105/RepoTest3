<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\RDSManager;
use App\Models\UploadFile;
use App\Repositories\Contracts\UploadFileRepositoryInterface;
use Carbon\Carbon;
use Helper\ResponseService;
use Illuminate\Foundation\Application;

class UploadFileRepository extends BaseRepository implements UploadFileRepositoryInterface
{
    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param RDSManager $model
     */

    public function model()
    {
        return UploadFile::class;
    }

    public function upload($request)
    {
        $file = $request->hasFile('file') ? $request->file('file') : '';
        if($file) {
            $fileName = md5(Carbon::now()->format('YmdHis')) . $file->getClientOriginalName();
            $filePath = $file->storeAs('rds', $fileName);

            $storagePath = str_replace('\\', '/', storage_path('app/' . $filePath));
            chmod($storagePath, 0600);

            return UploadFile::create([
                'file_path' => $storagePath,
                'file_name' => $file->getClientOriginalName(),
                "file_extension" => $file->getClientOriginalExtension(),
                "file_size" => $file->getSize(),
            ]);
        }
    }
}
