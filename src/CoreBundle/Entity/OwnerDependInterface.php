<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Entity;

interface OwnerDependInterface
{
    public function getOwnerId(): int;

    public function setOwner(UserInterface $owner): void;
}
