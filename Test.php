<?php

require_once __DIR__ . '/vendor/autoload.php';

use Inilim\Dump\Dump;

Dump::init();

class Test extends \Inilim\Validator\ValidAbstract
{
    const ALIAS  = [
        'three.*' => '_333',
    ];

    protected function _333($key, $data)
    {
        de($key);
    }

    protected function one($key, $data)
    {
    }
}


$a = new Test;


$data = [
    'one' => '',
    'two' => 123,
    'three' => [123, 123, 123],
];

$a->exec($data, [
    'one',
    'two',
    'three.*',
]);
