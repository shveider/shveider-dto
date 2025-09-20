<?php

namespace ShveiderDto\Plugins;

use ReflectionClass;
use ShveiderDto\Attributes\Alias;
use ShveiderDto\GenerateDTOConfig;
use ShveiderDto\Model\Code\AbstractDtoClass;
use ShveiderDto\ShveiderDtoExpanderPluginsInterface;
use ShveiderDto\Traits\GetTypeTrait;

class AliasShveiderDtoExpanderPlugin implements ShveiderDtoExpanderPluginsInterface
{
    use GetTypeTrait;

    public function expand(
        ReflectionClass   $reflectionClass,
        GenerateDTOConfig $config,
        AbstractDtoClass  $abstractDtoObject
    ): AbstractDtoClass {
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Alias::class);

            if (!empty($attributes)) {
                $abstractDtoObject->addRegisteredAlias($property->getName(), $attributes[0]->newInstance()->name);
            }
        }

        return $abstractDtoObject;
    }

}
