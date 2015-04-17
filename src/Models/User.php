<?php
namespace Taketwo\Models;

//use App\Services\Extend\IpTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends \Eloquent implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;
//    use IpTrait;

    const STATUS_NORMAL = 1;
    const STATUS_DISABLE = 0;

    const ROLE_ADMINISTRATOR = 9;
    const ROLE_MEMBER = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /**
     * Relation
     */
    public function auths()
    {
        return $this->hasMany('Taketwo\Models\UserAuth');
    }
}
