<?php declare(strict_types=1);

namespace ShveiderDto\Model\Code;

class DtoTrait extends AbstractDtoClass
{
    public function __toString(): string
    {
        $traitDoc = '/** Auto generated class. Do not change anything here. */';

        $methodsString = $this->generateMethodsString();
        $registeredVarsString = $this->generateRegisteredVarsString();
        $registeredTransfersString = $this->generateRegisteredTransfers();
        $registeredArrayTransfersString = $this->generateRegisteredArrayTransfers();
        $registeredValuesWithConstructString = $this->generateRegisteredValueWithConstructString();
        $registeredAliasString = $this->generateRegisteredAlias();

        $php = "<?php\n\n";
        $namespace = "namespace $this->namespace;\n\n";
        $traitBody = "$registeredVarsString\n\n$registeredTransfersString\n\n$registeredArrayTransfersString\n\n$registeredValuesWithConstructString\n\n$registeredAliasString\n\n$methodsString";

        return "$php$namespace$traitDoc\ntrait $this->name\n{\n$traitBody\n}\n";
    }

    protected function generateRegisteredVarsString(): string
    {
        $list = $this->mapArrayToString($this->registeredVars, fn ($key, $var) => "'$var'");

        return "\tprotected array \$__registered_vars = [$list];";
    }

    protected function generateRegisteredValueWithConstructString(): string
    {
        $list = $this->mapArrayToString(
            $this->registeredValuesWithConstruct,
            fn ($name, $values) => "'$name'=>['" . implode("','", $values) . "']",
        );

        return "\tprotected array \$__registered_values_with_construct = [$list];";
    }

    protected function generateRegisteredAlias(): string
    {
        $list = $this->mapArrayToString(
            $this->registeredAlias,
            fn ($name, $value) => "'$name'=>'" . $value . "'",
        );

        return "\tprotected array \$__registered_alias = [$list];";
    }

    protected function generateRegisteredTransfers(): string
    {
        $registeredTransfersList = $this
            ->mapArrayToString($this->registeredTransfers, fn ($name, $namespace) => "'$name' => '$namespace'");

        return "\tprotected array \$__registered_transfers = [$registeredTransfersList];";
    }

    protected function generateRegisteredArrayTransfers(): string
    {
        $registeredArrayTransfersList = $this
            ->mapArrayToString($this->registeredArrayTransfers, fn ($name, $namespace) => "'$name' => '$namespace'");

        return "\tprotected array \$__registered_array_transfers = [$registeredArrayTransfersList];";
    }

    protected function generateMethodsString(): string
    {
        return implode($this->minified ? PHP_EOL : PHP_EOL . PHP_EOL, $this->methods);
    }
}
