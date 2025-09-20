<?php declare(strict_types=1);

namespace ShveiderDto;

/**
 * @property array<string> $__registered_vars
 * - Uses for mapping fields in helping methods. If not set — get_class_vars used.
 *
 * @property array<string, string> $__registered_transfers
 * - Uses to determine, which field is transfer. To map it correctly.
 *
 * @property array<string, string> $__registered_array_transfers
 * - Uses to determine, which field is array of transfers. To map it correctly.
 *
 * @property array<string, array<string>> $__registered_values_with_construct
 * - Uses to determine, which field is transfer with fields in a construct. To map it correctly.
 *
 * @property array<string, string> $__registered_alias
 * - Uses to determine, which field is alias. To map it correctly.
 *
 */
abstract class AbstractConfigurableTransfer extends AbstractTransfer
{
    protected const SHARED_SKIPPED_PROPERTIES = [
        '__modified' => 1,
        '__registered_vars' => 1,
        '__registered_transfers' => 1,
        '__registered_array_transfers' => 1,
        '__registered_ao' => 1,
        '__private_registered_vars' => 1,
        '__registered_values_with_construct' => 1,
        '__registered_alias' => 1,
    ];

    protected function hasRegisteredArrayTransfers(string $name): bool
    {
        return isset($this->__registered_array_transfers[$name]);
    }

    protected function getRegisteredArrayTransfer(string $name): string
    {
        return $this->__registered_array_transfers[$name];
    }

    protected function hasRegisteredValueWithConstruct(string $name): bool
    {
        return isset($this->__registered_values_with_construct[$name]);
    }

    protected function getRegisteredValueWithConstruct(string $name): array
    {
        return $this->__registered_values_with_construct[$name];
    }

    protected function findRegisteredTransfer(string $name): ?string
    {
        return $this->__registered_transfers[$name] ?? null;
    }

    protected function findRegisteredVars(): array
    {
        return $this->__registered_vars ?? [];
    }

    protected function findAlias(string $name): ?string
    {
        return $this->__registered_alias[$name] ?? null;
    }
}
