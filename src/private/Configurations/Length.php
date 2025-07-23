<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function timeStampFormat(int $number = 0): string
{
    $hourConversion     = floor($number / 60);
    $minuteConversion   = $number % 60;
    $timeStampOutput    = sprintf("%02d:%02d", $hourConversion, $minuteConversion);
    return $timeStampOutput;
}
