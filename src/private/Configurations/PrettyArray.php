<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function array_dump(array $array): string
{
    $prettyArrayOutput = '<pre>' . print_r(value: $array, return: true) . '</pre>';
    return $prettyArrayOutput;
}
