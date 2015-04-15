<?php
namespace Taketwo\Services;

use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Taketwo\Providers\TakeTwoUserProvider;
use Taketwo\Models\User, Taketwo\Models\UserAuth;

class Registrar implements RegistrarContract
{

    /**
     * Get a validator for an incoming registration request.
     * (Only for html register)
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $credentials)
    {
        return Validator::make($credentials, [
            'email' => 'required|email|max:255|unique:user_auth,identifier,NULL,id,type,email',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $credentials
     * @return User
     */
    public function create(array $credentials)
    {
        $credentials = TakeTwoUserProvider::initCredentials($credentials);

        $user = new User();
        $user->username = isset($credentials['name']) ? $credentials['name'] : '';
        $user->email = $credentials['type'] == 'email' ? $credentials['identifier'] : '';
        $user->role = User::ROLE_MEMBER;
//        $user->created_ip = \Request::getClientIp();
//        $user->updated_ip = \Request::getClientIp();
        $user->save();

        /**
         * add user_auth
         */
        $userAuth = new UserAuth();
        $userAuth->user_id = $user->id;
        $userAuth->type = $credentials['type'];
        $userAuth->identifier = $credentials['identifier'];

        /**
         * keep emtpy for thrid app
         */
        $userAuth->credential = in_array($credentials['type'], ['email', 'username', 'phone'])
            ? bcrypt($credentials['credential']) : $credentials['credential'];
        $userAuth->save();

        return $user;
    }
}
