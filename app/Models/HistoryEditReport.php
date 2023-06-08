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

class HistoryEditReport extends Model
{
  use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'history_edit_reports';

    protected $fillable = [
      'report_id',
      'in_time',
      'out_time',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

}
