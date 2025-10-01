<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    $adminSettingPath = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        )
    )[1];

    error_log(
        message: sprintf(
            "Suspicious attempt to access [%s] setting page detected!!",
            strtoupper(string: $adminSettingPath)
        ),
        message_type: 0
    );

    exit(header(
        header: 'Location: /home',
        replace: true,
        response_code: 302
    ));
} else {
    require __DIR__ . '/../../Controllers/NavigationBarController.php';
    require __DIR__ . '/../../Views/Setting/UserSettingView.php';
}
