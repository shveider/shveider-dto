<?php declare(strict_types=1);

namespace ShveiderDto\Model\Code;

abstract class AbstractDtoClass implements DtoClassInterface
{
    /** @var array<\ShveiderDto\Model\Code\Method> */
    protected array $methods = [];

    protected bool $minified = false;

    protected string $namespace;

    protected array $registeredVars = [];

    protected array $registeredTransfers = [];

    protected array $registeredArrayTransfers = [];

    protected array $registeredArrayObjects = [];

    protected array $registeredValuesWithConstruct = [];

    public function __construct(protected readonly string $name)
    {
    }

    public function addMethod(string $name, Method $method): void
    {
        $this->methods[$name] = $method;
    }

    public function getMethod(string $name): ?Method
    {
        return $this->methods[$name] ?? null;
    }

    public function setMinified(bool $minified): static
    {
        $this->minified = $minified;

        foreach ($this->methods as $method) {
            $method->setMinified($minified);
        }

        return $this;
    }

    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function addRegisteredVar(string $registeredVar): static
    {
        $this->registeredVars[] = $registeredVar;

        return $this;
    }

    public function addRegisteredTransfer(string $varName, string $transferFullNamespace): static
    {
        $this->registeredTransfers[$varName] = $transferFullNamespace;

        return $this;
    }

    public function addRegisteredArrayTransfer(string $varName, string $transferFullNamespace): static
    {
        $this->registeredArrayTransfers[$varName] = $transferFullNamespace;

        return $this;
    }

    public function addRegisteredValueWithConstruct(string $varName, array $values): static
    {
        $this->registeredValuesWithConstruct[$varName] = $values;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addRegisteredArrayObject(string $varName, string $registeredArrayObject): void
    {
        $this->registeredArrayObjects[$varName] = $registeredArrayObject;
    }

    protected function mapArrayToString(array $array, callable $mapCallback): string
    {
        $list = [];

        foreach ($array as $propertyName => $v) {
            $list[] = $mapCallback($propertyName, $v);
        }

        return implode(', ', $list);
    }
}