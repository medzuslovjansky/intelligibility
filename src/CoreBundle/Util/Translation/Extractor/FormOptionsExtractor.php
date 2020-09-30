<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util\Translation\Extractor;

class FormOptionsExtractor extends FormExtractor
{
    protected $sequences = [
        [
            "'label'",
            '=>',
            self::MESSAGE_TOKEN,
        ],
        [
            "'help'",
            '=>',
            self::MESSAGE_TOKEN,
        ],
    ];
}
