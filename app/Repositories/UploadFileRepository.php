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
            return UploadFile::create([
                'file_path' => 'storage/app/public/' . $file->storeAs('rds', $fileName),
                'file_name' => $file->getClientOriginalName(),
                "file_extension" => $file->getClientOriginalExtension(),
                "file_size" => $file->getSize(),
            ]);
        }
    }

    public function downloadFidelity($id){
        $file = UploadFile::find($id);
        if (!$file) return ResponseService::responseData(CODE_NO_ACCESS, 'error', trans('messages.data_does_not_exist'));
//        $convertNameFile = Str::replace('storage/','',$file->file_path);
        return response()->download($file->file_path);
    }
}
