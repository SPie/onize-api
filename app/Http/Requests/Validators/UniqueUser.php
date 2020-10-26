<?php

namespace App\Http\Requests\Validators;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use App\Users\UserModel;
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

    private ?int $existingUserId;

    /**
     * UniqueUser constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        $this->existingUserId = null;
    }

    /**
     * @return UserManager
     */
    private function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @param int|null $existingUserId
     *
     * @return $this
     */
    public function setExistingUserId(?int $existingUserId)
    {
        $this->existingUserId = $existingUserId;

        return $this;
    }

    /**
     * @return UserModel|null
     */
    private function getExistingUserId(): ?int
    {
        return $this->existingUserId;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool|void
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        try {
            $user = $this->getUserManager()->getUserByEmail($value);

            return !empty($this->getExistingUserId()) && $this->getExistingUserId() == $user->getId();
        } catch (ModelNotFoundException $e) {
            return true;
        }
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'validation.user_not_unique';
    }
}
