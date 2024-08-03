<?php

namespace Inilim\Validator;

readonly class ValidResult
{
    /**
     * @param array $errors
     * @param array $checked_keys ключи что были проверены
     */
    function __construct(
        protected array $errors,
        protected array $checked_keys,
        protected array $data,
    ) {
    }

    function hasErrors(): bool
    {
        return !!$this->errors;
    }

    /**
     */
    function getErrors(): array
    {
        return $this->errors;
    }
}
