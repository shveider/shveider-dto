<?php declare(strict_types=1);

namespace ShveiderDto;

// TODO: 1. If we have values in construct with def values it's not modified
// TODO: 2. We have transfer1 and transfer2 and both have transfer city inside. second transfer have changed city name.
// TODO:    First transfer have city.name and city.key filled. so if we map modified to array second to first key will be lost.

abstract class AbstractTransfer implements DataTransferObjectInterface
{
    protected const SHARED_SKIPPED_PROPERTIES = [
        '__modified' => 0,
        '__private_registered_vars' => 1,
    ];

    /** @var array<string, bool> */
    protected array $__modified = [];

    /** @var list<string> */
    private array $__private_registered_vars;

    public function fromArray(array $data): static
    {
        if (empty($data)) {
            return $this;
        }

        foreach ($this->getClassVars() as $name) {
            if (array_key_exists($name, $data)) {
                $this->setFromArray($data[$name], $name);

                continue;
            }

            if ($alias = $this->findAlias($name)) {
                if (array_key_exists($alias, $data)) {
                    $this->setFromArray($data[$alias], $name);
                }
            }
        }

        return $this;
    }

    private function setFromArray(mixed $dataItem, string $name): void
    {
        $this->__modified[$name] ??= false;
        $this->$name = is_array($dataItem) ? $this->getValueFromArray($dataItem, $name) : $this->fromValue($name, $dataItem);
    }

    public function toArray(bool $recursive = false, bool $aliased = false): array
    {
        if ($recursive && $aliased) {
            return $this->toArrayWithAliasRecursive();
        }

        if ($recursive) {
            return $this->toArrayRecursive();
        }

        if ($aliased) {
            return $this->toArrayWithAlias();
        }

        $result = [];

        foreach ($this->getClassVars() as $name) {
            $result[$name] = $this->$name ?? null;
        }

        return $result;
    }

    private function toArrayWithAlias(): array
    {
        $result = [];

        foreach ($this->getClassVars() as $name) {
            $result[$this->findAlias($name) ?? $name] = $this->$name ?? null;
        }

        return $result;
    }

    private function toArrayRecursive(): array
    {
        $result = [];

        foreach ($this->getClassVars() as $name) {
            $result[$name] = $this->recursiveToArray($name, $this->$name ?? null, false);
        }

        return $result;
    }

    private function toArrayWithAliasRecursive(): array
    {
        $result = [];

        foreach ($this->getClassVars() as $name) {
            $result[$this->findAlias($name) ?? $name] = $this->recursiveToArray($name, $this->$name ?? null, true);
        }

        return $result;
    }

    public function modifiedToArray(bool $recursive = false): array
    {
        $result = [];

        foreach ($this->__modified as $name => $_) {
            $result[$name] = $recursive ? $this->recursiveModifiedToArray($name, $this->$name) : $this->$name;
        }

        return $result;
    }

    public function toJson(bool $pretty = false, bool $aliased = false): string
    {
        return json_encode($this->toArray(true, $aliased), $pretty ? JSON_PRETTY_PRINT : 0);
    }

    protected function fromValue(string $name, mixed $value): mixed
    {
        if ($value && is_string($value)) {
            if ($registeredEnum = $this->findRegisteredEnum($name)) {
                /** @var $registeredEnum class-string<\BackedEnum> */
                return $registeredEnum::tryFrom($value);
            }
        }

        return $value instanceof Transferable ? $value->transfer() : $value;
    }

    protected function getValueFromArray(array $dataItem, string $name): mixed
    {
        if ($transfer = $this->findRegisteredTransfer($name)) {
            return $this->hasRegisteredValueWithConstruct($name)
                ? (new $transfer(...$this->shiftMulti($dataItem, $this->getRegisteredValueWithConstruct($name))))
                    ->fromArray($dataItem)
                : (new $transfer())->fromArray($dataItem);
        }

        if ($this->hasRegisteredArrayTransfers($name)) {
            return $this->arrayTransfersFromArray($name, $dataItem);
        }

        if ($this->hasRegisteredValueWithConstruct($name)) {
            $set = $this->getRegisteredValueWithConstruct($name);
            $obj = array_shift($set);

            return count($set) > 0 ? new $obj(...$this->shiftMulti($dataItem, $set)) : new $obj();
        }

        return $dataItem;
    }

    /** @return array<string> */
    protected function getClassVars(): array
    {
        if (isset($this->__private_registered_vars)) {
            return $this->__private_registered_vars;
        }

        if ($vars = $this->findRegisteredVars()) {
            return $this->__private_registered_vars = $vars;
        }

        $vars = [];

        foreach (get_class_vars(static::class) as $name => $_) {
            if (!isset(static::SHARED_SKIPPED_PROPERTIES[$name])) {
                $vars[] = $name;
            }
        }

        return $this->__private_registered_vars = $vars;
    }

    protected function recursiveModifiedToArray(string $name, mixed $value): mixed
    {
        if (is_array($value) && $this->hasRegisteredArrayTransfers($name)) {
            $arrayOfTransfers = [];

            foreach ($value as $key => $item) {
                $arrayOfTransfers[$key] = $item && is_a($item, DataTransferObjectInterface::class)
                    ? $item->modifiedToArray(true) : $item;
            }

            return $arrayOfTransfers;
        }

        return $value && is_a($value, DataTransferObjectInterface::class)
            ? $value->modifiedToArray(true) : $value;
    }

    protected function arrayOfTransfersToArray(array $arrayValue, bool $recursive, bool $aliased): array {
        $result = [];

        foreach ($arrayValue as $key => $item) {
            $result[$key] = $item && is_a($item, DataTransferObjectInterface::class) ? $item->toArray($recursive, $aliased) : $item;
        }

        return $result;
    }

    protected function recursiveToArray(string $name, mixed $value, bool $aliased): mixed
    {
        if (is_array($value) && $this->hasRegisteredArrayTransfers($name)) {
            return $this->arrayOfTransfersToArray($value, true, $aliased);
        }

        return $value && is_a($value, DataTransferObjectInterface::class) ? $value->toArray(true, $aliased) : $value;
    }

    protected function arrayTransfersFromArray(string $name, array $arrayValues): array
    {
        $transfer = $this->getRegisteredArrayTransfer($name);
        $set = $this->hasRegisteredValueWithConstruct($name) ? $this->getRegisteredValueWithConstruct($name) : null;

        $result = [];

        foreach ($arrayValues as $key => $arrayValue) {
            if (!is_array($arrayValue)) {
                $result[$key] = $arrayValue;
                continue;
            }

            $instance = $set !== null
                ? new $transfer(...$this->shiftMulti($arrayValue, $set))
                : new $transfer();

            $result[$key] = $instance->fromArray($arrayValue);
        }

        return $result;
    }

    protected function modify(string $name): static
    {
        $this->__modified[$name] = true;

        return $this;
    }

    private function shiftMulti(array &$array, array $values): array
    {
        if (count($values) === 0) {
            return [$array];
        }

        $res = [];
        foreach ($values as $value) {
            if (array_key_exists($value, $array)) {
                $res[$value] = $array[$value];
                unset($array[$value]);
            }
        }

        return $res;
    }

    public function isModified(string $name): bool
    {
        return $this->__modified[$name] ?? false;
    }

    public function fromJson(string $json): static
    {
        return $this->fromArray(json_decode($json, true));
    }

    abstract protected function hasRegisteredArrayTransfers(string $name): bool;

    /** @return class-string<static> */
    abstract protected function getRegisteredArrayTransfer(string $name): string;

    abstract protected function hasRegisteredValueWithConstruct(string $name): bool;

    abstract protected function getRegisteredValueWithConstruct(string $name): array;

    /** @return class-string<\ShveiderDto\AbstractTransfer>|null */
    abstract protected function findRegisteredTransfer(string $name): ?string;

    abstract protected function findRegisteredVars(): ?array;

    abstract protected function findAlias(string $name): ?string;

    abstract protected function findRegisteredEnum(string $name): ?string;
}
