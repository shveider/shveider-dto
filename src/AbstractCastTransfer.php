<?php declare(strict_types=1);

namespace ShveiderDto;

abstract class AbstractCastTransfer extends AbstractTransfer
{
    protected const SHARED_SKIPPED_PROPERTIES = [
        '__modified' => 0,
        '__private_registered_vars' => 1,
        '__casts' => 2,
    ];

    /**
     * @var array{
     *     collections: array<string, class-string>,
     *     constructs: array<string, array<string>>,
     *     transfers: array<string, class-string<\ShveiderDto\DataTransferObjectInterface>>,
     *     vars: array<string>,
     *     alias: array<string, string>,
     *     enums: array<string, class-string<\BackedEnum>>
     * }
     */
    protected array $__casts = [];

    protected function hasRegisteredArrayTransfers(string $name): bool
    {
        return isset($this->__casts['collections'][$name]);
    }

    protected function getRegisteredArrayTransfer(string $name): string
    {
        return $this->__casts['collections'][$name];
    }

    protected function hasRegisteredValueWithConstruct(string $name): bool
    {
        return isset($this->__casts['constructs'][$name]);
    }

    protected function getRegisteredValueWithConstruct(string $name): array
    {
        return $this->__casts['constructs'][$name];
    }

    protected function findRegisteredTransfer(string $name): ?string
    {
        return $this->__casts['transfers'][$name] ?? null;
    }

    protected function findRegisteredVars(): ?array
    {
        return $this->__casts['vars'] ?? null;
    }

    protected function findAlias(string $name): ?string
    {
        return $this->__casts['alias'][$name] ?? null;
    }

    protected function findRegisteredEnum(string $name): ?string
    {
        return $this->__casts['enums'][$name] ?? null;
    }

    public function modifiedToArray(bool $recursive = false): array
    {
        foreach (get_class_vars(static::class) as $name => $defaultValue) {
            if (isset(static::SHARED_SKIPPED_PROPERTIES[$name]) || isset($this->__modified[$name])) {
                continue;
            }

            if (isset($this->$name) && $this->$name !== $defaultValue) {
                $this->__modified[$name] = true;
            }
        }

        return parent::modifiedToArray($recursive);
    }
}
