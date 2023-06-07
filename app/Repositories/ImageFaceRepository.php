<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\ImageFace;
use App\Repositories\Contracts\ImageFaceRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ImageFaceRepository extends BaseRepository implements ImageFaceRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param ImageFace $model
       */

    public function model()
    {
        return ImageFace::class;
    }


}
