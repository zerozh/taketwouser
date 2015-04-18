<?php
namespace Taketwo\Contracts;

use Taketwo\Contracts\CanVerifyEmail as CanVerifyEmailContract;

interface TokenRepositoryInterface
{

    /**
     * Create a new token.
     *
     * @param  \Taketwo\Contracts\CanVerifyEmail $user
     * @return string
     */
    public function create(CanVerifyEmailContract $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Taketwo\Contracts\CanVerifyEmail $user
     * @param  string $token
     * @return bool
     */
    public function exists(CanVerifyEmailContract $user, $token);

    /**
     * Delete a token record.
     *
     * @param  string $token
     * @return void
     */
    public function delete($token);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();

}
