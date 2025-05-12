<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

$getEnvironmentFile = file_get_contents(filename: __DIR__ . '/vot.dev.env');

$getAllEnvironmentValue = explode(
    separator: "\n",
    string: $getEnvironmentFile
);

foreach ($getAllEnvironmentValue as $environmentValue) {
    preg_match(
        pattern: "/([^#]+)=(.*)/",
        subject: $environmentValue,
        matches: $matches
    );

    if (isset($matches[2])) {
        putenv(trim($environmentValue));
    }
}
