<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VIAMUser extends Model
{
    use HasFactory;

    protected $table = 'viam_users';

    const NAME = 'name';
    const DESCRIPTION = 'description';

    protected $fillable = [
        self::NAME, self::DESCRIPTION
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'viam_user_policy', 'viam_user_id', 'policy_id');
    }
}
