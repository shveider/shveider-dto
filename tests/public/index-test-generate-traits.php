<?php declare(strict_types=1);

use ShveiderDto\Command\GenerateDtoTraitsCommand;
use ShveiderDto\GenerateDTOConfig;
use ShveiderDto\Helpers\DtoFilesReader;
use ShveiderDto\ShveiderDtoFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

$sharedConfig = new GenerateDTOConfig(
    __DIR__ . '/../Transfers/AbstractConfigurableTransfer',
    minified: false,
);

echo PHP_EOL . 'Generate Transfer\'s.' . PHP_EOL;

(new GenerateDtoTraitsCommand(new ShveiderDtoFactory(), $sharedConfig))->execute();

foreach ((new DtoFilesReader())->getFilesGenerator($sharedConfig) as $item) {
    if (preg_match('/#\[TransferSkip]/i', $item->content)) {
        continue;
    }

    assert(file_exists($item->filesDir . '/Generated/' . $item->traitName . '.php'));
    $namespace = $item->getFullNamespace();
    class_exists($namespace . '\\' . $item->traitName);
}

$modulesConfig = new GenerateDTOConfig(
    __DIR__ . '/../Transfers/AbstractConfigurableTransfer',
    __DIR__ . '/../Transfers/Generated',
    'ShveiderDtoTest\Generated',
    minified: true,
);

(new GenerateDtoTraitsCommand(new ShveiderDtoFactory(), $modulesConfig))->execute();

foreach ((new DtoFilesReader())->getFilesGenerator($sharedConfig) as $item) {
    if (preg_match('/#\[TransferSkip]/i', $item->content)) {
        continue;
    }

    assert(file_exists(__DIR__ . '/../Transfers/Generated/' . $item->traitName . '.php'));
    $namespace = $item->getFullNamespace();
    class_exists($namespace . '\\' . $item->traitName);
}
