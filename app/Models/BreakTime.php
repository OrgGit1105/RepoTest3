<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'break_times';
    public $timestamps = true;

    const USER_ID = 'user_id';
    const DATE = 'date';
    const GO_OUT_TIME = 'go_out_time';
    const GO_INTO_TIME = 'go_into_time';

    protected $fillable = [
        self::USER_ID,
        self::DATE,
        self::GO_OUT_TIME,
        self::GO_INTO_TIME,
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
