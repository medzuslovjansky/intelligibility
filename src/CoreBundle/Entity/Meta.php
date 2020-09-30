<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Intelligibility\CoreBundle\Enum\StatusEnum;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass()
 */
abstract class Meta
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=false )
     *
     * @var \DateTime
     */
    protected $created_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $modified;

    /**
     * @ORM\Column(type="datetime", nullable=false )
     *
     * @var \DateTime
     */
    protected $modified_date;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    protected $status;

    private ?string $className = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(string $created): void
    {
        $this->created = $created;
    }

    public function getModified(): string
    {
        return $this->modified;
    }

    public function setModified(string $modified): void
    {
        $this->modified = $modified;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedDate(): \DateTime
    {
        return $this->created_date;
    }

    public function getModifiedDate(): \DateTime
    {
        return $this->modified_date;
    }

    public function preCreate(): void
    {
        if (null === $this->created_date) {
            $timezone = new \DateTimeZone('UTC');
            $this->created_date = new \DateTime('now', $timezone);
            $this->modified_date = new \DateTime('now', $timezone);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function doPreUpdate(): void
    {
        $timezone = new \DateTimeZone('UTC');
        $this->modified_date = new \DateTime('now', $timezone);
    }

    public function isActive(): bool
    {
        return StatusEnum::ACTIVE === $this->status;
    }

    public function isNew(): bool
    {
        return null === $this->id;
    }

    final public function getEntityId(): string
    {
        if (null === $this->className) {
            $tmp = explode('\\', static::class);
            $this->className = end($tmp);
        }

        return $this->className.':'.$this->id;
    }
}
