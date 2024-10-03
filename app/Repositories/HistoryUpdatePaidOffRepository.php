<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Models\HistoryEditReport;
use App\Models\HistoryUpdatePaidOff;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\HistoryUpdatePaidOffRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class HistoryUpdatePaidOffRepository extends BaseRepository implements HistoryUpdatePaidOffRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param HistoryUpdatePaidOff $model
       */

    public function model()
    {
        return HistoryUpdatePaidOff::class;
    }


}
