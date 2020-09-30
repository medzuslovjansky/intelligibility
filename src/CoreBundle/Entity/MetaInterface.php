<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Entity;

interface MetaInterface
{
    public function getId(): ?int;

    public function getCreated(): ?string;

    public function getModified(): string;

    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getCreatedDate(): \DateTime;

    public function getModifiedDate(): \DateTime;

    public function preCreate(): void;

    public function getEntityId(): string;

    public function isActive(): bool;
}
