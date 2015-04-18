<?php
namespace Taketwo\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Taketwo\Contracts\CanVerifyEmail as CanVerifyEmailContract;

class User extends \Eloquent implements AuthenticatableContract, CanResetPasswordContract, CanVerifyEmailContract
{
    use Authenticatable;

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
    protected $fillable = ['name', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['remember_token'];

    /**
     * Relation
     */
    public function auths()
    {
        return $this->hasMany('Taketwo\Models\UserAuth');
    }

    public function getAuthPassword()
    {
        return $this->auths()->where('type', 'email')->first()->credential;
    }

    public function getEmailForPasswordReset()
    {
        return $this->auths()->where('type', 'email')->first()->identifier;
    }

    public function getEmailForVerify()
    {
        return $this->auths()->where('type', 'email')->first()->identifier;
    }

    public function wasEmailVerified()
    {
        return $this->auths()->where('type', 'email')->first()->verified;
    }
}
