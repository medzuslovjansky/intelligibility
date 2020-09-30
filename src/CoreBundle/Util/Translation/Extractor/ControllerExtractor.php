<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util\Translation\Extractor;

use Intelligibility\CoreBundle\Util\Translation\Extractor\Traits\IsRunTrait;
use Intelligibility\CoreBundle\Util\Translation\Extractor\Traits\KernelDirTrait;
use Symfony\Component\Translation\Extractor\PhpExtractor;
use Symfony\Component\Translation\MessageCatalogue;

class ControllerExtractor extends PhpExtractor
{
    use IsRunTrait;
    use KernelDirTrait;

    public function __construct(string $kernelDir)
    {
        $this->setKernelDir($kernelDir);
    }

    protected function getDirectory(): string
    {
        return '/Controller';
    }

    public function extract($directory, MessageCatalogue $catalog): void
    {
        if ($this->isRunAlready($directory)) {
            return;
        }

        foreach ($this->getSrcDirList() as $dir) {
            parent::extract($dir, $catalog);
        }
    }
}
