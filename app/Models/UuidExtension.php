<?php

namespace App\Models;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use LaravelDoctrine\ORM\Extensions\Extension;

/**
 * Class UuidExtension
 *
 * @package App\Models
 */
final class UuidExtension implements Extension
{
    public function __construct(private UuidGenerator $uuidGenerator)
    {
    }

    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader|null            $reader
     *
     * @return void
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null)
    {
        $manager->addEventListener([Events::prePersist], $this);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        if ($args->getEntity() instanceof UuidModel && empty($args->getEntity()->getUuid())) {
            $args->getEntity()->setUuid($this->uuidGenerator->generate());
        }
    }

    /**
     * @return null
     */
    public function getFilters()
    {
        return null;
    }
}
