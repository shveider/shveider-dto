<?php declare(strict_types=1);

namespace ShveiderDto\Model;

use ShveiderDto\GenerateDTOConfig;
use ShveiderDto\Model\Code\DtoTrait;

readonly class DtoTraitGenerator
{
    public function generate(
        GenerateDTOConfig $config,
        DtoTrait $trait,
        string $directory,
        string $transferNamespace,
    ): void {
        $file = $this->resolveStorageAndReturnFilePath($config, $directory, $trait->getName());

        $trait->setMinified($config->isMinified());
        $trait->setNamespace($this->getNamespaceWithTransferClassNamespace($transferNamespace, $config));

        file_exists($file) && unlink($file);
        file_put_contents($file, $trait);
    }

    public function deleteTrait(DtoTrait $trait, GenerateDTOConfig $config, string $directory): void
    {
        $file = $this->resolveStorageAndReturnFilePath($config, $directory, $trait->getName());
        file_exists($file) && unlink($file);
    }

    public function generateEmptyTrait(DtoTrait $trait, GenerateDTOConfig $config, string $transferDir, string $transferNamespace): void
    {
        $file = $this->resolveStorageAndReturnFilePath($config, $transferDir, $trait->getName());

        $trait->setMinified($config->isMinified());
        $trait->setNamespace($this->getNamespaceWithTransferClassNamespace($transferNamespace, $config));

        file_exists($file) && unlink($file);
        file_put_contents($file, $trait);
    }

    protected function resolveStorageAndReturnFilePath(GenerateDTOConfig $config, string $directory, string $traitName): string
    {
        if ($config->getWriteTo()) {
            !file_exists($config->getWriteTo()) && mkdir($config->getWriteTo());

            return rtrim($config->getWriteTo(), '/') . '/' . $traitName . '.php';
        }

        $writePath = rtrim($directory, '/') . '/' . $config->getDirNameForGeneratedFiles();
        !file_exists($writePath) && mkdir($writePath);

        return rtrim($directory, '/') . '/' . $config->getDirNameForGeneratedFiles()  . '/' . $traitName . '.php';
    }

    protected function getNamespaceWithTransferClassNamespace(string $transferNamespace, GenerateDTOConfig $config): string
    {
        return $config->getWriteToNamespace()
            ?: trim($transferNamespace, '\\') . '\\' . $config->getDirNameForGeneratedFiles();
    }
}
