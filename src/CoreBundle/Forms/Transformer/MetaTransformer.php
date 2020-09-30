<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Transformer;

use Intelligibility\CoreBundle\Entity\MetaInterface;

trait MetaTransformer
{
    public function metaTransform(array $data, object $object): array
    {
        if ($object instanceof MetaInterface) {
            $data['created'] = $object->getCreated();
            $data['created_date'] = $object->getCreatedDate();
            $data['modified'] = $object->getModified();
            $data['modified_date'] = $object->getModifiedDate();
        }

        return $data;
    }
}
