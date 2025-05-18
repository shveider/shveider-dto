<?php declare(strict_types=1);

namespace ShveiderDto;

interface DataTransferObjectInterface
{
    /**
     * Takes values from array and set it to defined properties in your data transfer object.
     *
     * @param array $data
     *
     * @return $this
     */
    public function fromArray(array $data): static;

    /**
     * Takes properties in your data transfer object and returns it ass array key => value.
     *
     * @param bool $recursive - if your data transfer object have another dto inside call this method for this dto as well.
     *
     * @return array
     */
    public function toArray(bool $recursive = false): array;

    /**
     * Returns the modified properties of the data transfer object as an associative array (key => value).
     * Modified properties are those changed via the fromArray() method or any set*() method.
     *
     * @param bool $recursive Whether to recursively include modified nested objects.
     *
     * @return array The array of modified properties.
     */
    public function modifiedToArray(bool $recursive = false): array;

    /**
     * Calls toArray method inside and convert it to json string.
     *
     * @param bool $pretty
     *
     * @return string
     */
    public function toJson(bool $pretty = false): string;

    public function isModified(string $name): bool;
}
