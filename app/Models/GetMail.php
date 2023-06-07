<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-08-02
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetMail extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $table = 'get_mail_logs';

  protected $fillable = [
    'status',
    'message_log',
    'mail_id',
    'mail_subject',
    'mail_from',
    'mail_to',
    'mail_attachment_file_name',
    'mail_attachment_path',
    'mail_date',
  ];

  protected $dates = ['deleted_at'];

  protected $casts = [
    'data' => 'array'
  ];

}
