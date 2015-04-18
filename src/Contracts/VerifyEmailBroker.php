<?php
namespace Taketwo\Contracts;

use Closure;

interface VerifyEmailBroker
{
    const VERIFY_LINK_SENT = 'verify_email.sent';
    const WAS_VERIFIED = 'verify_email.was_verified';
    const VERIFIED_SUCCESS = 'verify_email.verified_success';
    const INVALID_USER = 'verify_email.user';
    const INVALID_TOKEN = 'verify_email.token';

    /**
     * Send a verify link to a user.
     *
     * @param  array $credentials
     * @param  \Closure|null $callback
     * @return string
     */
    public function sendVerifyLink(array $credentials, Closure $callback = null);

    /**
     * Verify the email address.
     *
     * @param  array $credentials
     * @param  \Closure $callback
     * @return mixed
     */
    public function verify(array $credentials, Closure $callback);
}
