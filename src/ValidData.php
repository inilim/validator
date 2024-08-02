<?php

namespace Inilim\Validator;

use Inilim\Validator\ValidAbstract;

class ValidData
{
    /**
     * @var array<string,TRUE>
     */
    protected array $checked = [];
    protected array $err     = [];

    function __construct(
        readonly public array $data,
        readonly public array $check_keys,
        protected readonly ValidAbstract $context,
    ) {
    }

    function isChecked(string $key): bool
    {
        return $this->checked[$this->_getMethod($key)] ?? false;
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    private function _setError(string $msg, string $key): void
    {
        $this->err[$key] ??= [];
        $this->err[$key][] = $msg;
    }

    private function _checked(string $key): void
    {
        $this->checked[$this->_getMethod($key)] = true;
    }

    private function _getMethod(string $key): string
    {
        return (function (string $key): string {
            /**
             * @var ValidAbstract $this
             */
            return $this->getMethod($key);
        })
            ->bindTo($this->context)->__invoke($key);
    }
}
