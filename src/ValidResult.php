<?php

namespace Inilim\Validator;

readonly class ValidResult
{
    function __construct(
        public array $errors
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
