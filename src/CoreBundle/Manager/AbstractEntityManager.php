<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\UnitOfWork;
use Intelligibility\CoreBundle\Entity\Meta;
use Intelligibility\CoreBundle\Entity\MetaInterface;
use Intelligibility\CoreBundle\Entity\OwnerDependInterface;
use Intelligibility\CoreBundle\Entity\UserInterface;
use Intelligibility\CoreBundle\Enum\StatusEnum;
use Intelligibility\CoreBundle\Events\PersistEntityEvents;
use Intelligibility\CoreBundle\Repository\AbstractRepository;
use Intelligibility\CoreBundle\Repository\AbstractRepositoryInterface;
use Intelligibility\CoreBundle\Repository\Comparation;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEntityManager
{
    protected EntityManagerInterface $em;

    protected TokenStorageInterface $tokenStorage;

    protected EventDispatcherInterface $dispatcher;

    /** @var AbstractRepository */
    protected $repository;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->dispatcher = $dispatcher;
    }

    abstract public function getEntityClass(): string;

    final public function createNew(): object
    {
        $class = $this->getEntityClass();
        $entity = new $class();
        if ($entity instanceof MetaInterface) {
            $this->doConfigureNew($entity);
            $entity->preCreate();
            if ($entity instanceof OwnerDependInterface) {
                $user = $this->getUser();
                if ($user instanceof UserInterface) {
                    $entity->setOwner($user);
                }
            }
        }

        return $entity;
    }

    final public function save(object $entity, ?bool $autoFlash = false): void
    {
        if ($entity instanceof Meta) {
            $user = $this->getUser();
            if ($user instanceof UserInterface) {
                $entity->setModified($user->getEntityId());
                if ($entity->isNew()) {
                    $entity->setCreated($user->getEntityId());
                }
            }
        }

        $this->doSave($entity, $autoFlash);
    }

    final public function delete(object $entity, ?bool $autoFlash = false): void
    {
        $this->doDelete($entity, $autoFlash);
    }

    final public function flush(): void
    {
        $this->em->flush();
    }

    final public function clear(?string $objectName = null): void
    {
        $this->em->clear($objectName);
    }

    protected function doSave(object $object, ?bool $autoFlash = false): void
    {
        $this->em->persist($object);
        if ($autoFlash) {
            $this->em->flush();
        }
    }

    protected function doDelete(object $entity, ?bool $autoFlash = false): void
    {
        $this->em->remove($entity);
        if ($autoFlash) {
            $this->em->flush();
        }
    }

    protected function getRepository(): AbstractRepositoryInterface
    {
        if (!$this->repository) {
            $this->repository = $this->em->getRepository($this->getEntityClass());
        }

        return $this->repository;
    }

    protected function doConfigureNew(Meta $entity): void
    {
        $entity->setStatus(StatusEnum::ACTIVE);
    }

    final protected function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            return $token->getUser() instanceof UserInterface ? $token->getUser() : null;
        }

        return null;
    }

    final public function isNewEntity(object $entity): bool
    {
        return UnitOfWork::STATE_NEW === $this->em->getUnitOfWork()->getEntityState($entity);
    }

    final public function getExpr(): Expr
    {
        return $this->em->getExpressionBuilder();
    }

    public function find($id): ?object
    {
        return $this->getRepository()->find($id);
    }

    public function countAll(): int
    {
        return $this->getRepository()->countAll();
    }

    public function findByUser(UserInterface $user): ?object
    {
        return $this->getRepository()->findOneBy(['owner_id' => $user->getId()]);
    }

    public function findByOwnerId(int $ownerId): ?object
    {
        return $this->getRepository()->findOneBy(['owner_id' => $ownerId]);
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $criteria[] = ['id', $ids, Comparation::IN];

        return $this->getRepository()->findAllByCriteria($criteria, 100, 0);
    }

    final protected function dispatchEvent(object $entity, string $eventName, bool $autoFlash): void
    {
        $dispatcher = $this->dispatcher;
        $event = $this->createNewEvent($entity, $autoFlash);

        if ($dispatcher->hasListeners($eventName)) {
            $dispatcher->dispatch($event, $eventName);
        }
    }

    final protected function findEntityPropertyOldValue(object $entity, string $propertyName)
    {
        $oldData = $this->em->getUnitOfWork()->getOriginalEntityData($entity);
        if (!empty($oldData)) {
            if (\array_key_exists($propertyName, $oldData)) {
                return $oldData[$propertyName];
            }
            throw new \Exception(sprintf('The property (%s) not exist on entity(%s)', $propertyName, sprintf('class: %s', \get_class($entity))));
        }
    }

    protected function createNewEvent(object $entity, bool $autoFlash): Event
    {
        return new PersistEntityEvents($entity, $this, $autoFlash);
    }
}
