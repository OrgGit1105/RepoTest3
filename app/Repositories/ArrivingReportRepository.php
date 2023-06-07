<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\ArrivingReport;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ArrivingReportRepository extends BaseRepository implements ArrivingReportRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param ArrivingReport $model
       */

    public function model()
    {
        return ArrivingReport::class;
    }


}
