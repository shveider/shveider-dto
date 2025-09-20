<?php declare(strict_types=1);

namespace ShveiderDto;

use ShveiderDto\Helpers\DtoFilesReader;
use ShveiderDto\Model\Code\DtoPhpDoc;
use ShveiderDto\Model\Code\DtoTrait;
use ShveiderDto\Model\DtoTraitGenerator;
use ShveiderDto\Plugins\AliasShveiderDtoExpanderPlugin;
use ShveiderDto\Plugins\ArrayOfShveiderDtoExpanderPlugin;
use ShveiderDto\Plugins\GetSetMethodShveiderDtoExpanderPlugin;
use ShveiderDto\Plugins\RegisteredVarsShveiderDtoExpanderPlugin;
use ShveiderDto\Plugins\TransferWithConstructDtoExpanderPlugin;

class ShveiderDtoFactory
{
    /** @return \ShveiderDto\Model\DtoTraitGenerator */
    public function createDtoTraitGenerator(): DtoTraitGenerator
    {
        return new DtoTraitGenerator();
    }

    public function createDtoFilesReader(): DtoFilesReader
    {
        return new DtoFilesReader();
    }

    public function createDtoTrait(string $name): DtoTrait
    {
        return new DtoTrait($name);
    }

    public function createDtoPhpDoc(string $name): DtoPhpDoc
    {
        return new DtoPhpDoc($name);
    }

    /** @return array<int|string, \ShveiderDto\ShveiderDtoExpanderPluginsInterface> */
    public function getExpanderPlugins(): array
    {
        return [
            GetSetMethodShveiderDtoExpanderPlugin::class => new GetSetMethodShveiderDtoExpanderPlugin(),
            ArrayOfShveiderDtoExpanderPlugin::class => new ArrayOfShveiderDtoExpanderPlugin(),
            RegisteredVarsShveiderDtoExpanderPlugin::class => new RegisteredVarsShveiderDtoExpanderPlugin(),
            TransferWithConstructDtoExpanderPlugin::class => new TransferWithConstructDtoExpanderPlugin(),
            AliasShveiderDtoExpanderPlugin::class => new AliasShveiderDtoExpanderPlugin(),
        ];
    }
}
