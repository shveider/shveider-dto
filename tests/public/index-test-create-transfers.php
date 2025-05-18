<?php

$DTO = [
    'MainTransfer' => [
        'property' => [
            'customer' => 'CustomerTransfer',
            'testVo' => [
                'type' => 'TestVo',
                'attr' => ['ValueWithConstruct'],
            ],
            'testAssociative' => 'TestAssociativeTransfer',
        ],
        '__casts' => [
            'transfers' => [
                'testAssociative' => 'TestAssociativeTransfer::class',
                'customer' => 'CustomerTransfer::class',
            ],
            'constructs' => [
                'testVo' => ['TestVo::class', 'vString', 'vInt', 'vArray'],
            ],
        ],
    ],
    'CityTransfer' => [
        'construct' => ['key' => 'string', 'name' => 'string'],
    ],
    'CustomerTransfer' => [
        'property' => [
            'name' => 'string',
            'nickName1' => [
                'type' => '?string',
                'default' => "''",
            ],
            'nickName2' => [
                'type' => '?string',
                'default' => null,
            ],
            'nickName3' => '?string',
            'email' => 'string',
            'phone' => 'string',
            'addresses' => [
                'type' => 'array',
                'attr' => ["ArrayOf(AddressTransfer::class, 'address')"]
            ],
        ],
        '__casts' => [
            'collections' => [
                'addresses' => 'AddressTransfer::class',
            ],
        ],
    ],
    'TestAssociativeTransfer' => [
        'property' => [
            'attributes' => [
                'type' => 'array',
                'attr' => ["ArrayOf('string', 'attribute', true)"],
                'default' => '[]',
            ],
        ],
        '__casts' => ['singular' => ['attributes' => 'attribute']],
    ],
    'AddressTransfer' => [
        'property' => [
            'street' => 'string',
            'city' => [
                'type' => 'CityTransfer',
                'attr' => ['ValueWithConstruct'],
            ],
        ],
        '__casts' => [
            'transfers' => ['city' => 'CityTransfer::class'],
            'constructs' => ['city' => ['key', 'name']],
        ],
    ],
];

$WRITE_CASTS_FOR = ['AbstractCastDynamicTransfer', 'AbstractCastTransfer'];
$WRITE_REG_FOR = ['AbstractSetTransfer'];

!file_exists(__DIR__ . '/../Transfers') && mkdir(__DIR__ . '/../Transfers');

$DTO_TYPES = [
    'AbstractCastDynamicTransfer',
    'AbstractCastTransfer',
    'AbstractConfigurableTransfer',
];

foreach ($DTO_TYPES as $item) {
    build($item, $DTO, $WRITE_CASTS_FOR, $WRITE_REG_FOR);
}

function build(string $dtoType, array $DTO, array $WRITE_CASTS_FOR, array $WRITE_REG_FOR): void {
    $dir = __DIR__ . '/../Transfers/' . $dtoType;
    !file_exists($dir) && mkdir($dir);

    $defaultClass = "<?php declare(strict_types=1);\n\nnamespace ShveiderDtoTest\Transfers\\$dtoType;\n\n";
    $defaultClass .= "use ShveiderDto\Attributes as A;\nuse ShveiderDtoTest\VO\TestVo;\n";

    $defaultClass .= "use ShveiderDto\\$dtoType;\n";

    $security = match ($dtoType) {
        'AbstractConfigurableTransfer', 'AbstractCastDynamicTransfer' => 'protected',
        default => 'public ',
    };

    foreach ($DTO as $dtoName => $dto) {
        $file = $dir . '/' . $dtoName . '.php';
        $class = $defaultClass;
        $class .= "class $dtoName extends $dtoType\n{\n";

        if ($dtoType === 'AbstractConfigurableTransfer') {
            $class .= "\tuse \\ShveiderDtoTest\Transfers\\$dtoType\\Generated\\{$dtoName}Trait;\n";
        }

        if (isset($dto['property'])) {
            $class .= getPropertiesDefinition($dto['property'], $security);
        }

        if (in_array($dtoType, $WRITE_CASTS_FOR) && isset($dto['__casts'])) {
            $class .= "\nprotected array \$__casts = " . customExportVar($dto['__casts']) . ";\n";
        }

        if (in_array($dtoType, $WRITE_REG_FOR)) {
            if (isset($dto['__registered_transfers'])) {
                $class .= "\nprotected array \$__registered_transfers = " . customExportVar($dto['__registered_transfers']) . ";\n";
            }

            if (isset($dto['__registered_values_with_construct'])) {
                $class .= "\nprotected array \$__registered_values_with_construct = " . customExportVar($dto['__registered_values_with_construct']) . ";\n";
            }

            if (isset($dto['__registered_array_transfers'])) {
                $class .= "\nprotected array \$__registered_array_transfers = " . customExportVar($dto['__registered_array_transfers']) . ";\n";
            }
        }

        if (isset($dto['construct'])) {
            $class .= "\tpublic function __construct(" . getPropertiesDefinition($dto['construct'], $security, true) . ') {}' . PHP_EOL;
        }

        $class .= '}' . PHP_EOL;

        file_put_contents($file, $class);
    }
}

function getPropertiesDefinition(array $properties, string $security, $construct = false): string {
    $class = '';

    foreach ($properties as $propertyName => $propertySet) {
        $type = is_string($propertySet) ? $propertySet : $propertySet['type'];

        if (isset($propertySet['attr'])) {
            foreach ($propertySet['attr'] as $attr) {
                $class .= "\t#[A\\$attr]" . ($construct ? '' : PHP_EOL);
            }
        }

        $propertyDefinition = "\t$security $type \$$propertyName";
        if (isset($propertySet['default'])) {
            $propertyDefinition .= " = " . $propertySet['default'];
        }

        $class .= $propertyDefinition . ($construct ? ',' : ';'. PHP_EOL);
    }

    return $class;
}

function customExportVar(mixed $input): string
{
    if (is_string($input)) {
        return str_contains($input, '::class') ? $input : "'$input'";
    }

    $vars = [];

    foreach ($input as $key => $item) {
        $key = is_numeric($key) ? $key : "'$key'";
        $vars[] = "$key => " . customExportVar($item);
    }

    return '[' . implode(',', $vars) . ']';
}
