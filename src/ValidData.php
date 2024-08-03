<?php

namespace Inilim\Validator;

use Inilim\Validator\ValidAbstract;

class ValidData
{
    /**
     * @var array<string,BOOL>
     */
    public array $checked = [];
    public array $err     = [];

    function __construct(
        readonly public array $data,
        readonly public array $check_keys,
        protected readonly ValidAbstract $context,
    ) {
    }

    function getStatusByKey(string $key): ?bool
    {
        return $this->checked[$key] ?? null;
    }

    function isChecked(string $key): bool
    {
        return isset($this->checked[$key]);
    }

    function setError(string $key, string $msg): void
    {
        $this->err[$key] ??= [];
        $this->err[$key][] = $msg;
    }

    function checked(string $key, bool $status): void
    {
        $this->checked[$key] = $status;
    }
}
