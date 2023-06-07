<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigRange extends Model
{
  use HasFactory;

  const TYPE_AGE = 0;//kiểu tuổi vào công ty
  const TYPE_PERSON_MARRIED=1;//kiểu người phối ngẫu
  const TYPE_NUMBER_OF_DEPENDENTS=2;//kiểu số người phụ thuộc
  const TYPE_NUMBER_COMPANY=3;//kiểu số lượng công ty đã làm
  const TYPE_CERTIFICATE=4;//kiểu
  const TYPE_RANGE_TIME=5;
  protected $table = 'config_ranges';

  protected $fillable = ['from', 'to', 'rank', 'type', 'number_of_studies', 'average_total', 'ranges'];

  protected $casts = [
    'data' => 'array'
  ];
}
