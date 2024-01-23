<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'policies';

    const NAME = 'name';
    const TYPE = 'type';
    const INSTANCE_ID = 'instance_id';
    const PROJECT_NAME = 'project_name';

    protected $fillable = [
        self::NAME, self::TYPE, self::INSTANCE_ID, self::PROJECT_NAME
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function viam_users()
    {
        return $this->belongsToMany(VIAMUser::class, 'viam_user_policy','policy_id', 'viam_user_id', );
    }
}
