<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function appendJsonData(
    array &$json_data,
    array $json_keys,
    mixed $json_value,
    string $json_file
): void {
    if (!file_exists(filename: $json_file)) {
        // JSON data sent in is emtpy array by default so no need to re-declare
        $json_data = [];
    } else {
        // Use exisitng JSON file to append/remove data
        $json_data = json_decode(
            json: file_get_contents(
                filename: $json_file,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            ),
            associative: true,
            depth: 512,
            flags: 0
        );
    }

    // Directly use the address of chosen JSON data (use case above) for nested
    // layer handling
    $json_data_address = &$json_data;

    foreach ($json_keys as $json_key) {
        // Make sure the layer for each key exist
        if (!array_key_exists(
            key: $json_key,
            array: $json_data_address
        )) {
            // Create each layer with no data (empty array)
            $json_data_address[$json_key] = [];
        } else {
            // Skip through that layer until the last data passed in/viewable
            // depends on the cases
        }
        // Overwrite current address value continuously until it reached final
        // nested layer
        $json_data_address = &$json_data_address[$json_key];
    }
    // Then append the value to the final key correspondingly
    $json_data_address = $json_value;

    // Export it to a dedicated JSON file for future usages
    file_put_contents(
        filename: $json_file,
        data: json_encode(
            value: $json_data,
            flags: JSON_PRETTY_PRINT,
            depth: 512
        ),
        flags: 0,
        context: null
    );

    error_log(
        message: sprintf(
            "[%s] JSON file is successfully created/updated!!",
            basename(
                path: $json_file,
                suffix: ''
            )
        ),
        message_type: 0
    );
}
