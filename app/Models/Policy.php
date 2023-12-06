<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $table = 'policies';

    const NAME = 'name';
    const TYPE = 'type';
    protected $fillable = [
        self::NAME, self::TYPE
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function viam_users()
    {
        return $this->belongsToMany(VIAMUser::class, 'viam_user_policy', 'viam_user_id', 'policy_id');
    }
}
