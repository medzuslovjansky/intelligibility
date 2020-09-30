<?php

declare(strict_types=1);
/**
 * And remember this above all: Our Mechanical gods are watching. Make sure They are not ashamed!
 */

namespace Intelligibility\AdminBundle\Manager;

use Intelligibility\AdminBundle\Entity\BackendUser;
use Intelligibility\AdminBundle\Repository\BackendUserRepository;
use Intelligibility\CoreBundle\Manager\AbstractEntityManager;

/**
 * @method BackendUserRepository getRepository
 */
class BackendUserManager extends AbstractEntityManager
{
    public function getEntityClass(): string
    {
        return BackendUser::class;
    }

    public function findOneByEmail(string $email): ?BackendUser
    {
        return $this->getRepository()->findOneBy(['email' => $email]);
    }
}
