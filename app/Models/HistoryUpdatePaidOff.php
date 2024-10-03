<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class HistoryUpdatePaidOff extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'history_update_paid_off';

    const USER_ID = 'user_id';
    const TYPE = 'type';
    const REPORT_ID = 'report_id';
    const PAID_OFF_BEFORE = 'paid_off_before';
    const PAID_OFF_AFTER = 'paid_off_after';

    protected $fillable = [
        self::USER_ID,
        self::TYPE,
        self::REPORT_ID,
        self::PAID_OFF_BEFORE,
        self::PAID_OFF_AFTER,
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

}
