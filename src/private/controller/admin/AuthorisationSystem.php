<?php

require_once __DIR__ . '/../../model/crud/UserHandling.php';

/* TODO:
 * When the project's complexity scaled up enough to use this authorisation level system, then I
 * will starting working on the proper implementation on it. For now, logic below will be keep as
 * a template for future workflow.
 *
 * DOIN (Date of Interest): 10/01/25
 * DOIM (Date of Implementation): TBA
*/

switch ($userRole) {
    case 'User':
        echo "おはよございます！";
        exit();
    case 'Host':
        echo "Здравствуйте";
        exit();
    case 'Admin':
        echo "Xin chào!";
        exit();
    default:
        exit("Error 403: Forbidden");
}
