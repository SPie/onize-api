<?php

namespace App\Models;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ORM\Extensions\Extension;

/**
 * Class UuidExtension
 *
 * @package App\Models
 */
final class UuidExtension implements Extension
{
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null)
    {
        // TODO: Implement addSubscribers() method.
    }

    public function getFilters()
    {
        // TODO: Implement getFilters() method.
    }
}
