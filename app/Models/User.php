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
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users';

    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const VIAM_USER_ID = 'viam_user_id';
    const RETIREMENT_DATE = 'retirement_date';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const GENDER = 'gender';
    const BIRTHDAY = 'birthday';
    const ADDRESS = 'address';
    const TELEPHONE = 'telephone';
    const ENTRY_DATE = 'entry_date';
    const PAID_OFF = 'paid_off';
    const PAID_OFF_START = 'paid_off_start';
    const SLACK_ID = 'slack_id';
    const SKYPE_ID = 'skype_id';
    const GITHUB_ID = 'github_id';
    const SSH_PUBLIC_KEY = 'ssh_public_key';
    const GITHUB_GMAIL = 'github_gmail';
    const PASSWD_RDS = 'passwd_rds';

    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::VIAM_USER_ID,
        self::RETIREMENT_DATE,
        self::STATUS,
        self::GENDER,
        self::BIRTHDAY,
        self::ADDRESS,
        self::TELEPHONE,
        self::ENTRY_DATE,
        self::PAID_OFF,
        self::PAID_OFF_START,
        self::SLACK_ID,
        self::SKYPE_ID,
        self::GITHUB_ID,
        self::SSH_PUBLIC_KEY,
        self::GITHUB_GMAIL,
        self::CREATED_AT,
        self::UPDATED_AT,
        'jwt_active',
        self::PASSWD_RDS,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'jwt_active', self::PASSWD_RDS
    ];

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

//    public function role(){
//      return $this->belongsTo(Role::class,'role_id','id');
//    }

    public function scopeFindByName($query)
    {
        if (request()->filled('name')) {
            $query
                ->where('name', 'LIKE', '%' . request()->get('name') . '%');
        }
        return $query;
    }

    public function scopeFindByEmail($query)
    {
        if (request()->filled('email')) {
            $query
                ->where('email', 'LIKE', '%' . request()->get('email') . '%');
        }
        return $query;
    }

    public function scopeFindByVIAMUser($query)
    {
        if (request()->filled('role_id')) {
            $query->where(User::VIAM_USER_ID, request()->get('role_id'));
        }
        return $query;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function viam_user()
    {
        return $this->belongsTo(VIAMUser::class, 'viam_user_id', 'id');
    }

    public static function getRoleVFace($user)
    {
        $policies = $user->viam_user->policies;
        $policy_v_face = '';

        foreach ($policies as $policy)
        {
            if($policy->type == POLICY_TYPE['V_FACE']) {
                $policy_v_face = $policy->id;
            }
        }
        return $policy_v_face;
    }

    public function rdsManagers()
    {
        return $this->belongsToMany(RDSManager::class, 'rds_info', 'user_id', 'rds_manager_id');
    }

    public function databases()
    {
        return $this->hasManyThrough(Database::class, RDSInfo::class,'user_id', 'rds_info_id', 'id', 'id');
    }
}
