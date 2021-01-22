<?php

namespace App\Http\Binders;

/**
 * Interface Binder
 *
 * @package App\Http\Binders
 */
interface Binder
{
    /**
     * @param string $identifier
     *
     * @return mixed
     */
    public function bind(string $identifier);
}
