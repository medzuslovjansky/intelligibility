<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util\Translation\Extractor;

use Intelligibility\CoreBundle\Enum\AbstractEnum;
use Intelligibility\CoreBundle\Util\Translation\Extractor\Traits\IsRunTrait;
use Intelligibility\CoreBundle\Util\Translation\Extractor\Traits\KernelDirTrait;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Extractor\PhpExtractor;
use Symfony\Component\Translation\MessageCatalogue;

class ConstEnumExtractor extends PhpExtractor
{
    use IsRunTrait;
    use KernelDirTrait;

    private string $prefix = '__';

    public function __construct(string $kernelDir)
    {
        $this->setKernelDir($kernelDir);
    }

    protected function getDirectory(): string
    {
        return '/Enum';
    }

    public function extract($directory, MessageCatalogue $catalog): void
    {
        if ($this->isRunAlready($directory)) {
            return;
        }

        foreach ($this->getSrcDirList() as $dir) {
            $files = $this->extractFiles($dir);
            $this->extractByFileList($files, $catalog);
        }
    }

    protected function extractByFileList(iterable $files, MessageCatalogue $catalog): void
    {
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $fullClassName = $this->getClassNameFromFile($file);
            $values = AbstractEnum::getKeyAndTranslation($fullClassName);
            foreach ($values as $transElement) {
                $catalog->set($transElement['trans'], $this->prefix.$transElement['trans']);
            }
        }
    }

    protected function getClassNameFromFile(SplFileInfo $file)
    {
        $tokens = token_get_all($file->getContents());
        $classSegments = [];

        foreach ($tokens as $key => $token) {
            if (isset($token[0]) && T_NAMESPACE === $token[0]) {
                $i = 0;
                while (';' !== ($subToken = $tokens[++$i + $key])) {
                    if (!\in_array($subToken[0], [T_NS_SEPARATOR, T_WHITESPACE], true)) {
                        $classSegments[] = $subToken[1];
                    }
                }
            }

            if (isset($token[0]) && \in_array($token[0], [T_CLASS, T_INTERFACE, T_TRAIT], true)) {
                $i = 0;
                do {
                    $subToken = $tokens[++$i + $key];
                } while (isset($subToken[0]) && T_STRING !== $subToken[0]);

                $classSegments[] = $subToken[1];
                break;
            }
        }

        return implode('\\', $classSegments);
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }
}
