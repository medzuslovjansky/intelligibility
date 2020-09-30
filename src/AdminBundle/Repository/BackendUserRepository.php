<?php

declare(strict_types=1);

namespace Intelligibility\AdminBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Intelligibility\AdminBundle\Entity\BackendUser;
use Intelligibility\CoreBundle\Repository\AbstractRepository;

class BackendUserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackendUser::class);
    }
}
