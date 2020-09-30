<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Events;

use Intelligibility\CoreBundle\Manager\AbstractEntityManager;
use Symfony\Contracts\EventDispatcher\Event;

class PersistEntityEvents extends Event
{
    protected object $entity;

    protected AbstractEntityManager $manager;

    protected bool $autoFlash;

    public function __construct(object $entity, AbstractEntityManager $manager, bool $autoFlash)
    {
        $this->entity = $entity;
        $this->manager = $manager;
        $this->autoFlash = $autoFlash;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getManager(): AbstractEntityManager
    {
        return $this->manager;
    }

    public function isAutoFlush(): bool
    {
        return $this->autoFlash;
    }
}
