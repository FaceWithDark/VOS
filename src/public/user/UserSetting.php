<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
require_once '../../private/model/crud/UserHandling.php';

/* TODO:
 * When the project's complexity scaled up enough to use this authorisation level system, then I
 * will starting working on the proper implementation on it. For now, logic below will be keep as
 * a template for future workflow.
 *
 * DOIN (Date of Interest): 10/01/25
 * DOIM (Date of Implementation):
 *  + 16/02/25: Basic access-level filtering system
 *  + TBA
*/

$adminAccessLevel = "Admin";
$hostAccessLevel = "Host";
$userAccessLevel = "User";

$adminRoleQuery = "SELECT userId, userRole
                   FROM $userTable
                   WHERE userRole = '$adminAccessLevel';";

$hostRoleQuery = "SELECT userId, userRole
                  FROM $userTable
                  WHERE userRole = '$hostAccessLevel';";

$userRoleQuery = "SELECT userId, userRole
                  FROM $userTable
                  WHERE userRole = '$userAccessLevel';";

$adminRoleStatement = $phpDataObject->prepare($adminRoleQuery);
$hostRoleStatement = $phpDataObject->prepare($hostRoleQuery);
$userRoleStatement = $phpDataObject->prepare($userRoleQuery);

$adminRoleStatement->execute();
$hostRoleStatement->execute();
$userRoleStatement->execute();

$fetchAdminRole = $adminRoleStatement->fetchAll(PDO::FETCH_DEFAULT);
$fetchHostRole = $hostRoleStatement->fetchAll(PDO::FETCH_DEFAULT);
$fetchUserRole = $userRoleStatement->fetchAll(PDO::FETCH_DEFAULT);

/*
echo ('<pre>' . print_r($fetchAdminRole, true) . '</pre>');
echo ('<pre>' . print_r($fetchHostRole, true) . '</pre>');
echo ('<pre>' . print_r($fetchUserRole, true) . '</pre>');
*/

foreach ($fetchAdminRole as $adminRoleData) {
    $adminFetchedData = $adminRoleData['userRole'];
}

foreach ($fetchHostRole as $hostRoleData) {
    $hostFetchedData = $hostRoleData['userRole'];
}

foreach ($fetchUserRole as $userRoleData) {
    $userFetchedData = $userRoleData['userRole'];
}
?>

<?php if ($adminFetchedData === 'Admin'): ?>
    <a href="/index.php">
        <button type="button" style="margin-left: 15rem;">Admin Page (not quite)</button>
    </a>
<?php elseif ($hostFetchedData === 'Host'): ?>
    <a href="/user/Home.php">
        <button type="button" style="margin-left: 15rem;">Back To Home Page</button>
    </a>
<?php elseif ($userFetchedData === 'User'): ?>
    <a href="/user/Home.php">
        <button type="button" style="margin-left: 15rem;">Back To Home Page</button>
    </a>
<?php else: ?>
    <a href="/user/Home.php">
        <button type="button" style="margin-left: 15rem;">Back To Home Page</button>
    </a>
<?php endif; ?>
