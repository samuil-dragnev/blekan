<?php

namespace Blekan\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class MatchesPassword extends AbstractRule
{
    protected $password_old;

    public function __construct($password_old)
    {
        $this->password_old = $password_old;
    }

    public function validate($input)
    {
        return password_verify($input, $this->password_old);
    }
}
