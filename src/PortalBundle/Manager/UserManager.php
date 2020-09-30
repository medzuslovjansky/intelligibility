<?php

declare(strict_types=1);

namespace Intelligibility\PortalBundle\Manager;

use Intelligibility\CoreBundle\Manager\AbstractEntityManager;
use Intelligibility\PortalBundle\Entity\User;
use Intelligibility\PortalBundle\Repository\UserRepository;

/**
 * @method UserRepository getRepository
 */
class UserManager extends AbstractEntityManager
{
    public function getEntityClass(): string
    {
        return User::class;
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->getRepository()->findOneBy(['email' => $email]);
    }
}
