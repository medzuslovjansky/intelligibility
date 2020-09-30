<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Intelligibility\CoreBundle\Doctrine\Order\OrderBy;

interface AbstractRepositoryInterface extends ObjectRepository
{
    public function countAll(): int;

    public function countAllByCriteria(array $criteria): int;

    public function findAllByCriteria(array $criteria, int $limit, int $offset, ?OrderBy $orderBy = null): array;

    public function findAllIdsByCriteria(array $criteria, int $limit, int $offset, ?OrderBy $orderBy = null): array;
}
