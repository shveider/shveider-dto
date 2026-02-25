<?php declare(strict_types=1);

namespace ShveiderDto\Plugins;

use BackedEnum;
use ReflectionClass;
use ShveiderDto\AbstractConfigurableTransfer;
use ShveiderDto\AbstractTransfer;
use ShveiderDto\Constants;
use ShveiderDto\GenerateDTOConfig;
use ShveiderDto\Model\Code\AbstractDtoClass;
use ShveiderDto\ShveiderDtoExpanderPluginsInterface;

class RegisteredVarsShveiderDtoExpanderPlugin implements ShveiderDtoExpanderPluginsInterface
{
    public function expand(ReflectionClass $reflectionClass, GenerateDTOConfig $config, AbstractDtoClass $abstractDtoObject): AbstractDtoClass
    {
        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->isPrivate()) {
                continue;
            }

            if (in_array($property->getName(), $this->getSkippedProperties())) {
                continue;
            }

            $abstractDtoObject->addRegisteredVar($property->getName());

            if (!class_exists('\\' . ltrim($property->getType()->getName(), '\\'))) {
                continue;
            }

            if (is_a('\\' . ltrim($property->getType()->getName(), '\\'), BackedEnum::class, true)) {
                $abstractDtoObject->addRegisteredEnum($property->getName(), $property->getType()->getName());
            }

            $parentClass = get_parent_class('\\' . $property->getType()->getName());

            if (!$parentClass) {
                continue;
            }

            if (is_a($parentClass, AbstractTransfer::class, true) || in_array($parentClass, [AbstractTransfer::class, AbstractConfigurableTransfer::class])) {
                $abstractDtoObject
                    ->addRegisteredTransfer($property->getName(), $property->getType()->getName());
            }

            // TODO: Not fully implemented. Not decided how to implement it better.
            if ($parentClass === \ArrayObject::class || $property->getType()->getName() === \ArrayObject::class) {
                $abstractDtoObject
                    ->addRegisteredArrayObject($property->getName(), $property->getType()->getName());
            }
        }

        return $abstractDtoObject;
    }

    protected function getSkippedProperties(): array
    {
        return Constants::SHARED_SKIPPED_PROPERTIES;
    }
}