<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Enum;

use ReflectionClass;

abstract class AbstractEnum
{
    protected static array $cache = [];

    public static function getKeyAndTranslation(?string $className = null): array
    {
        if (null === $className) {
            $className = static::class;
        }
        if (!isset(self::$cache[$className])) {
            $refClass = new ReflectionClass($className);
            if ($refClass->isSubclassOf(self::class)) {
                foreach ($refClass->getConstants() as $name => $value) {
                    $translation = self::getTranslation($refClass->getShortName(), $name);
                    self::$cache[$className][] = ['name' => $name, 'value' => $value, 'trans' => $translation];
                }
            } elseif ($refClass->isAbstract()) {
                return [];
            } else {
                throw new \InvalidArgumentException('Invalid argument for AbstractEnum');
            }
        }

        return self::$cache[$className];
    }

    public static function getTranslationByValues(array $values, ?string $className = null): array
    {
        $translations = self::getKeyAndTranslation($className);
        $result = [];
        foreach ($values as $value) {
            foreach ($translations as $transItem) {
                if ($transItem['value'] === $value) {
                    $result[$value] = $transItem['trans'];
                    break;
                }
            }
        }

        return $result;
    }

    public static function getTranslation(string $className, string $keyName): string
    {
        return 'translation@enum.'.$className.'.'.$keyName;
    }
}
