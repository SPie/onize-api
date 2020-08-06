<?php

namespace App\Http\Requests\Validators;

use App\Users\UserManager;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class UniqueUser
 *
 * @package App\Http\Requests\Validators
 */
class UniqueUser implements Rule
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * UniqueUser constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManager
     */
    private function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool|void
     */
    public function passes($attribute, $value)
    {
        return !$this->getUserManager()->isEmailUsed($value);
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'validation.user_not_unique';
    }
}
