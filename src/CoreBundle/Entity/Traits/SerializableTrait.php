<?php

declare(strict_types=1);
/**
 * And remember this above all: Our Mechanical gods are watching. Make sure They are not ashamed!
 */

namespace Intelligibility\CoreBundle\Entity\Traits;

trait SerializableTrait
{
    private static array $serializeAttributeNamesCache = [];

    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function &toArray(): array
    {
        $data = [];
        foreach ($this->getSerializableAttributeNamesFromCache() as $name) {
            $data[$name] = $this->{$name};
        }

        return $data;
    }

    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);

        $this->fromArray($data);
    }

    protected function fromArray(array &$data): void
    {
        foreach ($this->getSerializableAttributeNamesFromCache() as $name) {
            if (\array_key_exists($name, $data)) {
                $this->$name = &$data[$name];
            }
        }
    }

    protected function getSerializableAttributeNames(): array
    {
        return array_keys(get_object_vars($this));
    }

    private function &getSerializableAttributeNamesFromCache()
    {
        $key = static::class;
        if (!isset(self::$serializeAttributeNamesCache[$key])) {
            self::$serializeAttributeNamesCache[$key] = $this->getSerializableAttributeNames();
        }

        return self::$serializeAttributeNamesCache[$key];
    }
}
