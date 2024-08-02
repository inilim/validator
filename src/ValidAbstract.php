<?php

namespace Inilim\Validator;

use Inilim\Validator\ValidData;

abstract class ValidAbstract
{
    /**
     * @var array<string,string>
     */
    public const ALIAS  = [];
    /**
     * @var string[] исключаем методы
     */
    public const EXCEPT = [];

    private ValidData $vdata;

    protected function getVData(): ValidData
    {
        return $this->vdata;
    }

    /**
     * @param string[] $check_keys какие ключи проверять
     */
    protected function exec(array &$data, array $check_keys = []): void
    {
        $ex = !!$check_keys;

        $this->vdata = new ValidData(
            data: $data,
            check_keys: $check_keys,
            context: $this,
        );

        foreach ($this->getCachedMethods() as $method) {

            if ($ex) {
                de($method);
                if (!\in_array($method, $check_keys, true)) {
                    continue;
                }
            }

            // INFO проверить
            $this->check($method);
        }
    }

    protected function check(string $key): void
    {
        if ($this->vdata->isChecked($key)) return;
        $m = $this->getMethod($key);
        $this->$m($key);
        $this->checked($key);
    }

    // ------------------------------------------------------------------
    // INFO ERRORS
    // ------------------------------------------------------------------

    protected function setError(string $msg, string $key): void
    {
        (function (string $msg, string $key): void {
            /** @var ValidData $this */
            $this->_setError($msg, $key);
        })
            ->bindTo($this->vdata)->__invoke($msg, $key);
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    private function getMethod(string $method): string
    {
        return static::ALIAS[$method] ?? $method;
    }

    private function checked(string $key): void
    {
        (function (string $key): void {
            /** @var ValidData $this */
            $this->_checked($key);
        })
            ->bindTo($this->vdata)->__invoke($key);
    }

    private function getCachedMethods(): array
    {
        return \fileCache()
            ->getOrSaveFromClaster(
                static::class,
                self::class,
                $this->getMethods(...),
            ) ?? $this->getMethods();
    }

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
