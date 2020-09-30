<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Entity\Traits;

use Intelligibility\CoreBundle\Entity\UserInterface;

trait OwnerDependTrait
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $owner_id;

    public function getOwnerId(): int
    {
        return $this->owner_id;
    }

    public function setOwner(UserInterface $owner): void
    {
        $this->owner_id = $owner->getId();
    }
}
