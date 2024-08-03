<?php

namespace Inilim\Validator;

use Inilim\Validator\ValidData;
use Inilim\Validator\ValidResult;

abstract class ValidAbstract
{
    /**
     * @var array<string,string>
     */
    const ALIAS  = [];
    /**
     * @var string[] исключаем методы
     */
    const EXCEPT = [];

    private ValidData $vdata;



    /**
     * @param string[] $check_keys какие ключи проверять
     */
    function exec(array $data, array $check_keys = [])
    {
        $ex = !!$check_keys;

        $this->vdata = new ValidData(
            data: $data,
            check_keys: $check_keys,
            context: $this,
        );

        foreach ($this->getMethods() as $method) {
            $key = $this->getKey($method);
            if ($ex) {
                if (!\in_array($key, $check_keys, true)) {
                    continue;
                }
            }

            $this->check($key);
        }

        // de($this->vdata);

        return new ValidResult(
            errors: $this->vdata->err,
            checked_keys: \array_keys($this->vdata->checked),
            data: $this->vdata->data,
        );
    }

    protected function isCheckedByMethod(string $method): bool
    {
        return $this->vdata->isChecked($this->getKey($method));
    }
    protected function isCheckedByKey(string $key): bool
    {
        return $this->vdata->isChecked($key);
    }

    protected function getStatusByMethod(string $method): ?bool
    {
        return $this->vdata->getStatusByKey($this->getKey($method));
    }
    protected function getStatusByKey(string $key): ?bool
    {
        return $this->vdata->getStatusByKey($key);
    }

    protected function checkedByMethod(string $method): bool
    {
        return $this->check($this->getKey($method));
    }
    protected function checkedByKey(string $key): bool
    {
        return $this->check($key);
    }

    private function check(string $key): bool
    {
        if ($this->vdata->isChecked($key)) {
            return $this->vdata->getStatusByKey($key);
        }
        $method = $this->getMethod($key);
        $result = $this->$method($key, $this->vdata->data);

        if (\is_string($result)) {
            $status = false;
            $this->vdata->setError($key, $result);
        } elseif ($result === null) {
            $status = true;
        } elseif ($result === true) {
            $status = true;
        } elseif ($result === false) {
            $status = false;
            $this->vdata->setError($key, 'not valid');
        } else {
            throw new \Exception();
        }

        $this->vdata->checked($key, $status);
        return $status;
    }

    private function getMethod(string $key): string
    {
        return static::ALIAS[$key] ?? $key;
    }

    private function getKey(string $method): string
    {
        // TODO потом сделать нормально
        foreach (static::ALIAS as $key => $m) {
            if ($m === $method) return $key;
        }
        return $method;
    }

    // private function getCachedMethods(): array
    // {
    //     return \fileCache()
    //         ->getOrSaveFromClaster(
    //             static::class,
    //             self::class,
    //             $this->getMethods(...),
    //         ) ?? $this->getMethods();
    // }

    /**
     * @return string[]
     */
    private function getMethods(): array
    {
        $static_class = static::class;
        $ref          = new \ReflectionClass($static_class);
        $methods      = [];
        foreach ($ref->getMethods() as $method) {
            /** @var \ReflectionMethod $method */

            // INFO убираем наследуемые методы
            if ($static_class !== $method->class) continue;

            $name = $method->getName();
            if (!\in_array($name, static::EXCEPT)) {
                $methods[] = $name;
            }
        }
        return $methods;
    }
}
