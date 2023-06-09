<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\HistoryEditReport;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class HistoryEditReportRepository extends BaseRepository implements HistoryEditReportRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param HistoryEditReport $model
       */

    public function model()
    {
        return HistoryEditReport::class;
    }


}
