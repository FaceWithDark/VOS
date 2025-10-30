<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function mappool_json_encode(
    string $file,
    string $tournament,
    string $round,
    string $type,
    int $id
): string {
    $data = [];
    $data[$tournament][$round][$type] = $id;

    file_put_contents(
        filename: $file,
        data: $data,
        flags: 0,
        context: null
    );

    return "New file created!!";
}


function mappool_json_decode() {}
