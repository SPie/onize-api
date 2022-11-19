<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelsNotFoundException;
use App\Projects\RoleManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Contracts\Validation\Rule;

class PermissionsExist implements Rule
{
    private Collection $permissions;

    private array $notFoundIdentifiers = [];

    public function __construct(readonly private RoleManager $roleManager)
    {
        $this->permissions = new ArrayCollection([]);
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function passes($attribute, $value): bool
    {
        if (!\is_array($value)) {
            // will be handled by another rule
            return true;
        }

        try {
            $this->permissions = $this->roleManager->getPermissions($value);
        } catch (ModelsNotFoundException $e) {
            $this->notFoundIdentifiers = $e->getIdentifiers();

            return false;
        }

        return true;
    }

    public function message(): string
    {
        return \sprintf('validation.permissions-not-found:%s', \implode(',', $this->notFoundIdentifiers));
    }
}
