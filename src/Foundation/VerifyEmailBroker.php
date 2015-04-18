<?php
namespace Taketwo\Foundation;

use Closure;
use Taketwo\Contracts\CanVerifyEmail;
use Taketwo\Contracts\VerifyEmailBroker as VerifyEmailBrokerContract;
use Taketwo\Contracts\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use UnexpectedValueException;

class VerifyEmailBroker implements VerifyEmailBrokerContract
{

    /**
     * The token repository.
     *
     * @var \Taketwo\Contracts\TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * The mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * The view of the email verify link e-mail.
     *
     * @var string
     */
    protected $emailView;

    /**
     * Create a new email verify instance.
     *
     * @param  \Taketwo\Contracts\TokenRepositoryInterface $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider $users
     * @param  \Illuminate\Contracts\Mail\Mailer $mailer
     * @param  string $emailView
     */
    public function __construct(
        TokenRepositoryInterface $tokens,
        UserProvider $users,
        MailerContract $mailer,
        $emailView
    ) {
        $this->users = $users;
        $this->mailer = $mailer;
        $this->tokens = $tokens;
        $this->emailView = $emailView;
    }

    /**
     * Send a email varify link to a user.
     *
     * @param  array $credentials
     * @param  \Closure|null $callback
     * @return string
     */
    public function sendVerifyLinkWithCredentials(array $credentials, Closure $callback = null)
    {
        $user = $this->getUser($credentials);
        if (is_null($user)) {
            return VerifyEmailBrokerContract::INVALID_USER;
        }

        return $this->sendVerifyLinkWithUser($user, $callback);
    }

    /**
     * Send a email varify link to a user.
     *
     * @param  \Taketwo\Contracts\CanVerifyEmail $user
     * @param  \Closure|null $callback
     * @return string
     */
    public function sendVerifyLinkWithUser(CanVerifyEmail $user, Closure $callback = null)
    {
        if ($user->wasEmailVerified()) {
            return VerifyEmailBrokerContract::WAS_VERIFIED;
        }


        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = $this->tokens->create($user);

        $this->emailVeriryLink($user, $token, $callback);

        return VerifyEmailBrokerContract::VERIFY_LINK_SENT;
    }

    /**
     * Send the email varify link via e-mail.
     *
     * @param  \Taketwo\Contracts\CanVerifyEmail $user
     * @param  string $token
     * @param  \Closure|null $callback
     * @return int
     */
    public function emailVeriryLink(CanVerifyEmail $user, $token, Closure $callback = null)
    {
        // We will use the reminder view that was given to the broker to display the
        // password reminder e-mail. We'll pass a "token" variable into the views
        // so that it may be displayed for an user to click for password reset.
        $view = $this->emailView;

        return $this->mailer->send($view, compact('token', 'user'), function ($m) use ($user, $token, $callback) {
            $m->to($user->getEmailForVerify());

            if (!is_null($callback)) {
                call_user_func($callback, $m, $user, $token);
            }
        });
    }

    /**
     * Verify the email address for the given token.
     *
     * @param  array $credentials
     * @param  \Closure $callback
     * @return mixed
     */
    public function verify(array $credentials, Closure $callback)
    {
        $user = $this->validateVerify($credentials);

        if (!$user instanceof CanVerifyEmail) {
            return $user;
        }

        call_user_func($callback, $user);

        $this->tokens->delete($credentials['token']);

        return VerifyEmailBrokerContract::VERIFIED_SUCCESS;
    }

    /**
     * Validate a email verify for the given credentials.
     *
     * @param  array $credentials
     * @return \Taketwo\Contracts\CanVerifyEmail
     */
    protected function validateVerify(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return VerifyEmailBrokerContract::INVALID_USER;
        }

        if (!$this->tokens->exists($user, $credentials['token'])) {
            return VerifyEmailBrokerContract::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array $credentials
     * @return \Taketwo\Contracts\CanVerifyEmail
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(array $credentials)
    {
        $credentials = array_except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && !$user instanceof CanVerifyEmail) {
            throw new UnexpectedValueException("User must implement CanResetPassword interface.");
        }

        return $user;
    }

    /**
     * Get the email verify token repository implementation.
     *
     * @return \Taketwo\Contracts\TokenRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->tokens;
    }
}
