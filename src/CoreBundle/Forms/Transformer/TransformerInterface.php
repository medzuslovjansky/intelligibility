<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Transformer;

interface TransformerInterface
{
    public function transform(array $data, object $entity): array;

    public function reverseTransform(array $data, object $entity): void;
}
