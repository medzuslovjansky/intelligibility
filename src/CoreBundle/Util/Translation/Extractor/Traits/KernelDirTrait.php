<?php

declare(strict_types=1);
/**
 * And remember this above all: Our Mechanical gods are watching. Make sure They are not ashamed!
 */

namespace Intelligibility\CoreBundle\Util\Translation\Extractor\Traits;

trait KernelDirTrait
{
    private string $kernelDir;
    private string $srcDir = '/src/*';

    protected function getFullDirectory(): string
    {
        return $this->kernelDir.$this->getDirectory();
    }

    protected function getSrcDirList(): array
    {
        $dirs = array_filter(glob($this->kernelDir.$this->getSrcDir()), 'is_dir');
        $result = [];
        foreach ($dirs as $dir) {
            $fullDir = $dir.$this->getDirectory();
            if (file_exists($fullDir)) {
                $result[] = $fullDir;
            }
        }

        return $result;
    }

    abstract protected function getDirectory(): string;

    public function getKernelDir(): string
    {
        return $this->kernelDir;
    }

    public function setKernelDir(string $kernelDir): void
    {
        $this->kernelDir = $kernelDir;
    }

    private function getSrcDir(): string
    {
        return $this->srcDir;
    }
}
