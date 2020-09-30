<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Repository;

class Comparation
{
    public const EQ = '=';

    public const NOT_EQ = '!=';

    public const BG_OR_EQ = '>=';

    public const SM_OR_EQ = '<=';

    public const LIKE = 'LIKE';

    public const NOT_LIKE = 'NOT LIKE';

    public const IN = 'IN';

    public const NOT_IN = 'NOT IN';

    public const IS_NULL = 'IS NULL';

    public const NOT_NULL = 'IS NOT NULL';
}
