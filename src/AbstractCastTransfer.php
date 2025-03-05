<?php declare(strict_types=1);

namespace ShveiderDto;

use ShveiderDto\Traits\ModifiedOverrideTrait;

class AbstractCastTransfer extends AbstractTransfer
{
    use ModifiedOverrideTrait;
    protected const SHARED_SKIPPED_PROPERTIES = [
        '__modified' => 0,
        '__private_registered_vars' => 1,
        '__casts' => 2,
    ];

    /**
     * @var array{
     *     collections: array<string, class-string>,
     *     constructs: array<string, array<string>>,
     *     transfers: array<string, class-string>,
     *     vars: array<string>
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
}
