<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function appendJsonData(
    array &$json_data,
    array $json_keys,
    mixed $json_value,
    string $json_file
): void {
    // Lock in the initial address of passed JSON data structure before append
    // or remove its layer
    $json_data_address = &$json_data;

    foreach ($json_keys as $json_key) {
        // Make sure the layer exist and it's an array (empty or non-empty are valid)
        if (!isset($json_data_address[$json_key]) || !is_array(value: $json_data_address[$json_key])) {
            // Create each layer with no data (empty array)
            $json_data_address[$json_key] = [];
            error_log(
                message: "Created missing [{$json_key}] JSON layer\n.",
                message_type: 0
            );
        } else {
            error_log(
                message: "JSON layer already exists for [{$json_key}]\n.",
                message_type: 0
            );
        }
        // Continue the checking and referencing process until it hit the final layer/key
        $json_data_address = &$json_data_address[$json_key];
    }
    // Final JSON data structure
    $json_data_address = $json_value;

    // Export it to a dedicated JSON file for future usages
    file_put_contents(
        filename: $json_file,
        data: json_encode(
            value: $json_data,
            flags: 0,
            depth: 512
        ),
        flags: 0,
        context: null
    );
}
