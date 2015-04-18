<?php
namespace Taketwo\Foundation;

use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Taketwo\Models\User;
use Taketwo\Models\UserAuth;

class UserProvider implements UserProviderContract
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(HasherContract $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return User::where('id', $identifier)->where('remember_token', $token)->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $credentials = self::initCredentials($credentials);

        if (!isset($credentials['type']) || !$credentials['type']) {
            return null;
        }
        if (!isset($credentials['identifier']) || !$credentials['identifier']) {
            return null;
        }

        $userAuth = UserAuth::where('type', $credentials['type'])->where('identifier',
            $credentials['identifier'])->first();
        if (!$userAuth) {
            return null;
        }

        return User::find($userAuth->user_id);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $credentials = self::initCredentials($credentials);
        /**
         * 第三方登录不验证credential, 本地用户验证密码
         */
        if (in_array($credentials['type'], ['email', 'username', 'phone'])) {
            $userAuth = UserAuth::where('user_id', $user->id)->where('type', $credentials['type'])->first();
            if ($this->hasher->check($credentials['credential'], $userAuth->credential)) {
                $userAuth->touch();

                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Format Laravel login credentials
     * @param $credentials
     * @return mixed
     */
    public static function initCredentials($credentials)
    {
        if (isset($credentials['email'])) {
            $credentials['type'] = 'email';
            $credentials['identifier'] = $credentials['email'];
            $credentials['credential'] = isset($credentials['password']) ? $credentials['password'] : '';
            unset($credentials['email']);
            unset($credentials['password']);
        }

        return $credentials;
    }
}
