<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use Illuminate\Contracts\Validation\Rule;

class UniqueUser implements Rule
{
    public function __construct(private UserManager $userManager, private ?int $existingUserId = null)
    {
    }

    public function setExistingUserId(?int $existingUserId): self
    {
        $this->existingUserId = $existingUserId;

        return $this;
    }

    private function getExistingUserId(): ?int
    {
        return $this->existingUserId;
    }

    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true;
        }

        try {
            $user = $this->userManager->getUserByEmail($value);

            return !empty($this->getExistingUserId()) && $this->getExistingUserId() == $user->getId();
        } catch (ModelNotFoundException $e) {
            return true;
        }
    }

    public function message(): string
    {
        return 'validation.user-not-unique';
    }
}
