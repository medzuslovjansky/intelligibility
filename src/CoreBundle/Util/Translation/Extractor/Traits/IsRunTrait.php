<?php

declare(strict_types=1);
/**
 * And remember this above all: Our Mechanical gods are watching. Make sure They are not ashamed!
 */

namespace Intelligibility\CoreBundle\Util\Translation\Extractor\Traits;

trait IsRunTrait
{
    private bool $isRun = false;
    private ?string $directory;

    protected function isRunAlready(string $directory): bool
    {
        if (!$this->isRun) {
            $this->isRun = true;
            $this->directory = $directory;

            return false;
        }
        if ($directory === $this->directory) {
            return false;
        }

        return true;
    }
}
