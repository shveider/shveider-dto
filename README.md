# DTO Library Documentation

This library provides various implementations of Data Transfer Objects (DTOs) that can be used to facilitate the transfer of data between layers in your application. It offers multiple strategies to handle DTOs with flexibility and performance in mind.

**DTO Can't work with private properties.**
Because parent class or trait don't have access to child props.

**But AbstractReflectionTransfer have access to all type of props.**

## DTO Types

### 1. `AbstractConfigurableTransfer`
- Uses traits to configure behavior (setters, getters, adders).
- Provides a more structured approach with configurable fields.
- **Performance**: Faster than reflection-based DTOs but with more boilerplate code.

[read more](docs/AbstractConfigurableTransfer.md)

### 2. `AbstractCastTransfer`
- Relies on casting to handle DTO properties.
- Simplified, does not provide setters/getters.
- **Performance**: Very fast, but lacks the convenience of configurable options.

[read more](docs/AbstractCastTransfer.md)

### 3. `AbstractCastDynamicTransfer`
- Relies on casting to handle DTO properties.
- Simplified, does not provide setters/getters.
- **Performance**: Very fast, but lacks the convenience of configurable options.
- Has getters, setters and adders.

## Commands

### Generating Traits Command
Use the following command to generate DTO traits:

```php

$modulesConfig = new GenerateDTOConfig(
    __DIR__ . '/../tests/DTO/*/Transfers',
    __DIR__ . '/../tests/Generated',
    'ShveiderDtoTest\Generated',
    minified: false,
);

(new GenerateDtoTraitsCommand(new ShveiderDtoFactory(), $modulesConfig))->execute();

```

## Methods that all transfers have.
The transfer class inherits methods defined in the DataTransferObjectInterface. These methods help to easily manipulate data within transfer objects by converting between arrays and objects, tracking modified properties, and serializing data. Below are the key methods you can use:

1. `fromArray(array $data): static` <br/>
   This method allows you to populate the properties of a transfer object using an associative array. It maps each array key to the corresponding property in the transfer object.
```php
$userTransfer = new UserTransfer();
$userTransfer->fromArray([
    'name' => 'John Doe',
    'age' => 30,
    'address' => [
        'city' => 'New York',
        'country' => 'USA'
    ],
]);
```
**Behavior**:
- The method sets values in the transfer object based on the keys in the $data array.
- If the transfer object contains nested DTOs, it will automatically populate those nested objects as well.

2. `toArray(bool $recursive = false): array` <br/>
   The toArray method converts the transfer object back into an associative array, with properties as keys and their respective values. If the object contains nested DTOs, and the $recursive flag is set to true, it will also convert those nested objects into arrays.
```php
$array = $userTransfer->toArray(true);
```
**Behavior**:
- When $recursive is true, any nested DTOs are also converted to arrays.
- When $recursive is false, nested DTOs remain as objects in the array.

Example of Output: 
```php
[
    'name' => 'John Doe',
    'age' => 30,
    'address' => [
        'city' => 'New York',
        'country' => 'USA'
    ]
]
```

3. `modifiedToArray(): array` <br/>
   This method returns only the properties that have been modified. A property is considered modified if it was set or changed via the fromArray() method or a setter method.

`Usage Example`:
```php
$userTransfer->fromArray([
    'name' => 'Jane Doe'
]);
$modified = $userTransfer->modifiedToArray();
```
**Behavior**: <br/>
 - It returns an associative array of only the properties that were changed since the creation of the object or since the last reset of modified state.

**Example Output**:
```php
[
    'name' => 'Jane Doe'
]
```

4. `toJson(bool $pretty = false): string` <br/>
   The toJson method serializes the transfer object into a JSON string. If $pretty is set to true, the resulting JSON string will be formatted with indentation for readability.

**Usage Example**:
```php
$json = $userTransfer->toJson(true);
```
Output:
```json
{
    "name": "John Doe",
    "age": 30,
    "address": {
        "city": "New York",
        "country": "USA"
    }
}
```
These methods provide a flexible way to work with data transfer objects, allowing seamless conversions between arrays, objects, and JSON while also tracking modified properties for efficient updates.

| Method                  | Description                                                                 |
|-------------------------|-----------------------------------------------------------------------------|
| `fromArray(array $data)` | Sets the transfer object’s properties from an associative array.             |
| `toArray(bool $recursive)` | Converts the transfer object back to an array, optionally recursively.     |
| `modifiedToArray()`      | Returns an array of only the modified properties.                           |
| `toJson(bool $pretty)`   | Serializes the transfer object into a JSON string.                          |


More documentation:
- [docs](docs)
- [AbstractCastTransfer.md](docs%2FAbstractCastTransfer.md)
- [AbstractConfigurableTransfer.md](docs%2FAbstractConfigurableTransfer.md)
- [AbstractReflectionTransfer.md](docs%2FAbstractReflectionTransfer.md)
- [MANUALLY_CONFIGURED_TRANSFER.md](docs%2FMANUALLY_CONFIGURED_TRANSFER.md)
- [USING_REFLECTION.md](docs%2FUSING_REFLECTION.md)
- [USING_TRAITS.md](docs%2FUSING_TRAITS.md)
