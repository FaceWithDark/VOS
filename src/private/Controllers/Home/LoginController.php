<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Login.php';
require __DIR__ . '/../../Configurations/TimeZone.php';


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    echo '<a href="/home">Bro, do not try to do that...</a></p>';
} else {
    // Run the session first before any output buffers
    session_start(
        options: [
            'name'              => 'vot_access_id',
            'cookie_lifetime'   => 86400,
            'cookie_httponly'   => 1
        ]
    );
    session_regenerate_id(delete_old_session: true);

    $userAccessToken    = $_COOKIE['vot_access_token'];
    $userData           = getUserData(token: $userAccessToken);

    $userId             = $userData['id'];
    $userName           = $userData['username'];
    $userFlag           = $userData['country_code'];
    $userImage          = $userData['avatar_url'];
    $userUrl            = "https://osu.ppy.sh/users/{$userData['id']}";
    $userRank           = $userData['statistics']['global_rank'];
    $userTimeZone       = getUserTimeZone()['baseOffset'];
    $userRoleId         = 'USR';    // All user have user-level access by default
    $userTournamemtId   = 'NONE';   // All user belong to none tournament by default

    if (!checkUserData(id: $userId)) {
        createUserData(
            user_id: $userId,
            name: $userName,
            flag: $userFlag,
            image: $userImage,
            url: $userUrl,
            rank: $userRank,
            time_zone: $userTimeZone,
            role_id: $userRoleId,
            tournament_id: $userTournamentId
        );
    } else {
        // TODO: UPDATE query here (change the 'view' table only, not the actual
        // table if all data stay the same).
        error_log(
            message: 'User data exist, simply ignoring it...',
            message_type: 0
        );
    };

    // NOTE: there might be a better way than this 'cause my current role logic
    // approaches is not yet fully implemented
    $_SESSION['id'] = $userId; // Every user have their own unique token

    // Show the page again after actions have been done
    require __DIR__ . '/../../Views/Home/LoginView.php';
}
