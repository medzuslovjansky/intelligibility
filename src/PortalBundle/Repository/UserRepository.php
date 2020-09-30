<?php

declare(strict_types=1);

namespace Intelligibility\PortalBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Intelligibility\CoreBundle\Repository\AbstractRepository;
use Intelligibility\PortalBundle\Entity\User;

class UserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
