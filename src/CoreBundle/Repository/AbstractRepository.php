<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Intelligibility\CoreBundle\Doctrine\Order\OrderBy;

abstract class AbstractRepository extends ServiceEntityRepository implements AbstractRepositoryInterface
{
    public const DEFAULT_ENTITY_ALIAS = 'en';
    protected const ID_FIELD = 'id';

    public function countAll(): int
    {
        $result = $this->getDefaultQueryBuilder()
            ->select('count('.self::DEFAULT_ENTITY_ALIAS.'.'.static::ID_FIELD.')')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result;
    }

    public function countAllByCriteria(array $criteria): int
    {
        $builder = $this->getDefaultQueryBuilder()
            ->select('count('.self::DEFAULT_ENTITY_ALIAS.'.'.static::ID_FIELD.')');
        $this->applyCriteria($builder, $criteria);

        return (int) $builder->getQuery()->getSingleScalarResult();
    }

    public function findAllByCriteria(array $criteria, int $limit, int $offset, ?OrderBy $orderBy = null): array
    {
        $builder = $this->getDefaultQueryBuilder()
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $this->applyCriteria($builder, $criteria);

        if (null !== $orderBy) {
            $builder->orderBy(self::DEFAULT_ENTITY_ALIAS.'.'.$orderBy->getField(), $orderBy->getOrder());
        }

        return $builder->getQuery()->getResult();
    }

    public function findAllIdsByCriteria(array $criteria, int $limit, int $offset, ?OrderBy $orderBy = null): array
    {
        $builder = $this->getDefaultQueryBuilder()
            ->select(self::DEFAULT_ENTITY_ALIAS.'.'.static::ID_FIELD)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $this->applyCriteria($builder, $criteria);
        if (null !== $orderBy) {
            $builder->orderBy(self::DEFAULT_ENTITY_ALIAS.'.'.$orderBy->getField(), $orderBy->getOrder());
        }

        return $builder->getQuery()->getResult();
    }

    public function getDefaultQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder(self::DEFAULT_ENTITY_ALIAS);
    }

    private function applyCriteria(QueryBuilder $builder, array $criteria): void
    {
        $parameters = [];
        foreach ($criteria as $key => $criterionData) {
            if (\is_object($criterionData[0])) {
                $builder->andWhere($criterionData[0]);
                if (isset($criterionData[1])) {
                    $parameters = array_merge($parameters, $criterionData[1]);
                }
                continue;
            }

            $comparation = $criterionData[2] ?? Comparation::EQ;
            $setVale = $criterionData[0].$key;
            if (Comparation::IN === $comparation || Comparation::NOT_IN === $comparation) {
                $builder->andWhere(self::DEFAULT_ENTITY_ALIAS.'.'.$criterionData[0].' '.$comparation.' (:'.$setVale.')');
            } elseif (Comparation::IS_NULL === $comparation || Comparation::NOT_NULL === $comparation) {
                $builder->andWhere(self::DEFAULT_ENTITY_ALIAS.'.'.$criterionData[0].' '.$comparation);
                continue;
            } else {
                $builder->andWhere(self::DEFAULT_ENTITY_ALIAS.'.'.$criterionData[0].' '.$comparation.' :'.$setVale);
            }
            $parameters[$setVale] = $criterionData[1];
        }
        $builder->setParameters($parameters);
    }
}
