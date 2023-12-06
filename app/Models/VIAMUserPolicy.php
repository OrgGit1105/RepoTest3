<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VIAMUserPolicy extends Model
{
    use HasFactory;

    protected $table = 'viam_user_policy';

    const VIAM_USER_ID = 'viam_user_id';
    const POLICY_ID = 'policy_id';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        self::VIAM_USER_ID, self::POLICY_ID
    ];
}
