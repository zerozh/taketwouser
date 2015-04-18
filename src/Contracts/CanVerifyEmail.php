<?php
namespace Taketwo\Contracts;

interface CanVerifyEmail
{
    public function getEmailForVerify();

    public function was_verified();
}
