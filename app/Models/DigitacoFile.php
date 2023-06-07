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

class DigitacoFile extends Model
{
  use HasFactory;

  const TYPE_DRIVING = 'driving';
  const TYPE_POINT = 'point';
  protected $table = 'digitaco_files';

  protected $fillable = [
    'getting_date',
    'file_name_data_point',
    'file_path_data_point',
    'file_name_data_driving',
    'file_path_data_driving',
    'status',
  ];

  protected $casts = [
    'data' => 'array'
  ];

}
