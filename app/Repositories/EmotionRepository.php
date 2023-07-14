<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace Repository;

use App\Models\Emotion;
use App\Repositories\Contracts\EmotionRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class EmotionRepository extends BaseRepository implements EmotionRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param Emotion $model
       */

    public function model()
    {
        return Emotion::class;
    }


}
