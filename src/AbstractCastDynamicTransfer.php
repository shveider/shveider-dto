<?php declare(strict_types=1);

namespace ShveiderDto;

use BadMethodCallException;

abstract class AbstractCastDynamicTransfer extends AbstractCastTransfer
{
    private array $__methods = [];

    public function __call(string $name, array $arguments)
    {
        if (empty($this->__methods)) {
            foreach ($this->getClassVars() as $classVar) {
                $this->__methods['get']['get' . ucfirst($classVar)] = $classVar;
                $this->__methods['set']['set' . ucfirst($classVar)] = $classVar;

                if (isset($this->__casts['singular'][$classVar])) {
                    $addMethodName = $this->__casts['singular'][$classVar];

                    $this->__methods['add']['add' . ucfirst($addMethodName)] = $classVar;
                }

                if (isset($this->__casts['collections'][$classVar]) && !isset($this->__casts['singular'][$classVar])) {
                    $addMethodName = substr($classVar, 0, str_ends_with($classVar, 'ses') ? -2 : -1);
                    $this->__methods['add']['add' . ucfirst($addMethodName)] = $classVar;
                }
            }
        }

        if (isset($this->__methods['get'][$name])) {
            $propertyName = $this->__methods['get'][$name];

            return $this->$propertyName;
        }

        if (isset($this->__methods['set'][$name])) {
            $propertyName = $this->__methods['set'][$name];
            $value = $arguments[0] ?? null;

            $this->__modified[$propertyName] = true;
            $this->$propertyName = is_array($value) ? $this->getValueFromArray($value, $name) : $value;

            return $this;
        }

        if (isset($this->__methods['add'][$name])) {
            $propertyName = $this->__methods['add'][$name];
            $this->__modified[$propertyName] = true;

            if (count($arguments) > 1) {
                $this->$propertyName[$arguments[0]] = $arguments[1];

                return $this;
            }

            $this->$propertyName[] = $arguments[0];

            return $this;
        }

        throw new BadMethodCallException($name);
    }
}
