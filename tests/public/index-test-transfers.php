<?php

require_once __DIR__ . '/../../vendor/autoload.php';

function testCase(string $transferNamespace, bool $useMethods, bool $testMethodsFeatures = false): void {
    echo 'Test '. $transferNamespace . PHP_EOL;
    $mainTransferClass = "\\ShveiderDtoTest\\Transfers\\$transferNamespace\\MainTransfer";
    $addressTransferClass = "\\ShveiderDtoTest\\Transfers\\$transferNamespace\\AddressTransfer";
    $cityTransferClass = "\\ShveiderDtoTest\\Transfers\\$transferNamespace\\CityTransfer";
    $customerTransferClass = "\\ShveiderDtoTest\\Transfers\\$transferNamespace\\CustomerTransfer";
    $testCase = new \ShveiderDtoTest\TestCase($useMethods, new $mainTransferClass(), $addressTransferClass, $cityTransferClass, $customerTransferClass);

    $testCase->testInterface();
    $testMethodsFeatures && $testCase->testMethodsFeatures();
}

testCase('AbstractCastTransfer', false);
testCase('AbstractCastDynamicTransfer', true, testMethodsFeatures: true);
testCase('AbstractConfigurableTransfer', true, testMethodsFeatures: true);
