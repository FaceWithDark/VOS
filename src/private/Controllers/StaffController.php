<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function getTournamentStaff(
    string $name,
    string $role
): array {

    $staffJsonData              = __DIR__ . '/../Datas/Staff/VotStaffData.json';

    $allStaffHostData           = [];
    $allStaffMappoolerData      = [];
    $allStaffGfxVfxData         = [];
    $allStaffMapperData         = [];
    $allStaffPlayTesterData     = [];
    $allStaffRefereeData        = [];
    $allStaffStreamerData       = [];
    $allStaffCommentatorData    = [];
    $allStaffStatisticianData   = [];

    $staffAccessToken           = $_COOKIE['vot_access_token'];

    switch ($name) {
        case 'vot4':
        case 'vot3':
        case 'vot2':
        case 'vot1':
            $staffViewableJsonData = file_get_contents(
                filename: $staffJsonData,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );

            $staffReadableJsonData = json_decode(
                json: $staffViewableJsonData,
                associative: true
            );

            foreach ($staffReadableJsonData as $staffRoleJsonData) {
                switch ($role) {
                    case 'DEFAULT':
                        /*** STAFF DATA FOR HOST ROLE ***/
                        $staffHostRoleJsonData = $staffRoleJsonData['HOST'];
                        /*
                        Because filter staff role by default is basically
                        fetching all staff roles within the database of a
                        specific tournament, so I'll just being a bit hacky here
                        by reading each individual staff role data straight away.
                        This'll prevent me having to create a dedicated 'default'
                        role in the database table that just basically the sum
                        of all other roles, which unesscesary increase the
                        database size.
                        */
                        foreach ($staffHostRoleJsonData as $staffHostIdJsonData) {
                            $staffHostData = getTournamentStaffData(
                                id: $staffHostIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffHostId        = $staffHostData['id'];
                            $staffHostName      = $staffHostData['username'];
                            $staffHostRole      = 'HOST';
                            $staffHostFlag      = $staffHostData['country_code'];
                            $staffHostImage     = $staffHostData['avatar_url'];
                            $staffHostUrl       = "https://osu.ppy.sh/users/{$staffHostData['id']}";
                            $staffHostRank      = $staffHostData['statistics']['global_rank'];
                            $staffHostTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffHostData[] = [
                                'staff_id'          => $staffHostId,
                                'staff_name'        => $staffHostName,
                                'staff_role'        => $staffHostRole,
                                'staff_flag'        => $staffHostFlag,
                                'staff_image'       => $staffHostImage,
                                'staff_url'         => $staffHostUrl,
                                'staff_rank'        => $staffHostRank,
                                'staff_time_zone'   => $staffHostTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR MAPPOOLER ROLE ***/
                        $staffMappoolerRoleJsonData = $staffRoleJsonData['MAPPOOLER'];
                        foreach ($staffMappoolerRoleJsonData as $staffMappoolerIdJsonData) {
                            $staffMappoolerData = getTournamentStaffData(
                                id: $staffMappoolerIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffMappoolerId        = $staffMappoolerData['id'];
                            $staffMappoolerName      = $staffMappoolerData['username'];
                            $staffMappoolerRole      = 'MAPPOOLER';
                            $staffMappoolerFlag      = $staffMappoolerData['country_code'];
                            $staffMappoolerImage     = $staffMappoolerData['avatar_url'];
                            $staffMappoolerUrl       = "https://osu.ppy.sh/users/{$staffMappoolerData['id']}";
                            $staffMappoolerRank      = $staffMappoolerData['statistics']['global_rank'];
                            $staffMappoolerTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffMappoolerData[] = [
                                'staff_id'          => $staffMappoolerId,
                                'staff_name'        => $staffMappoolerName,
                                'staff_role'        => $staffMappoolerRole,
                                'staff_flag'        => $staffMappoolerFlag,
                                'staff_image'       => $staffMappoolerImage,
                                'staff_url'         => $staffMappoolerUrl,
                                'staff_rank'        => $staffMappoolerRank,
                                'staff_time_zone'   => $staffMappoolerTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR GFX/VFX ROLE ***/
                        $staffGfxVfxRoleJsonData = $staffRoleJsonData['GFX_VFX'];
                        foreach ($staffGfxVfxRoleJsonData as $staffGfxVfxIdJsonData) {
                            $staffGfxVfxData = getTournamentStaffData(
                                id: $staffGfxVfxIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffGfxVfxId        = $staffGfxVfxData['id'];
                            $staffGfxVfxName      = $staffGfxVfxData['username'];
                            $staffGfxVfxRole      = 'GFX / VFX';
                            $staffGfxVfxFlag      = $staffGfxVfxData['country_code'];
                            $staffGfxVfxImage     = $staffGfxVfxData['avatar_url'];
                            $staffGfxVfxUrl       = "https://osu.ppy.sh/users/{$staffGfxVfxData['id']}";
                            $staffGfxVfxRank      = $staffGfxVfxData['statistics']['global_rank'];
                            $staffGfxVfxTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffGfxVfxData[] = [
                                'staff_id'          => $staffGfxVfxId,
                                'staff_name'        => $staffGfxVfxName,
                                'staff_role'        => $staffGfxVfxRole,
                                'staff_flag'        => $staffGfxVfxFlag,
                                'staff_image'       => $staffGfxVfxImage,
                                'staff_url'         => $staffGfxVfxUrl,
                                'staff_rank'        => $staffGfxVfxRank,
                                'staff_time_zone'   => $staffGfxVfxTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR MAPPER ROLE ***/
                        $staffMapperRoleJsonData = $staffRoleJsonData['MAPPER'];
                        foreach ($staffMapperRoleJsonData as $staffMapperIdJsonData) {
                            $staffMapperData = getTournamentStaffData(
                                id: $staffMapperIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffMapperId        = $staffMapperData['id'];
                            $staffMapperName      = $staffMapperData['username'];
                            $staffMapperRole      = 'MAPPER';
                            $staffMapperFlag      = $staffMapperData['country_code'];
                            $staffMapperImage     = $staffMapperData['avatar_url'];
                            $staffMapperUrl       = "https://osu.ppy.sh/users/{$staffMapperData['id']}";
                            $staffMapperRank      = $staffMapperData['statistics']['global_rank'];
                            $staffMapperTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffMapperData[] = [
                                'staff_id'          => $staffMapperId,
                                'staff_name'        => $staffMapperName,
                                'staff_role'        => $staffMapperRole,
                                'staff_flag'        => $staffMapperFlag,
                                'staff_image'       => $staffMapperImage,
                                'staff_url'         => $staffMapperUrl,
                                'staff_rank'        => $staffMapperRank,
                                'staff_time_zone'   => $staffMapperTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR PLAY TESTER ROLE ***/
                        $staffPlayTesterRoleJsonData = $staffRoleJsonData['PLAY_TESTER'];
                        foreach ($staffPlayTesterRoleJsonData as $staffPlayTesterIdJsonData) {
                            $staffPlayTesterData = getTournamentStaffData(
                                id: $staffPlayTesterIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffPlayTesterId        = $staffPlayTesterData['id'];
                            $staffPlayTesterName      = $staffPlayTesterData['username'];
                            $staffPlayTesterRole      = 'PLAYTESTER';
                            $staffPlayTesterFlag      = $staffPlayTesterData['country_code'];
                            $staffPlayTesterImage     = $staffPlayTesterData['avatar_url'];
                            $staffPlayTesterUrl       = "https://osu.ppy.sh/users/{$staffPlayTesterData['id']}";
                            $staffPlayTesterRank      = $staffPlayTesterData['statistics']['global_rank'];
                            $staffPlayTesterTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffPlayTesterData[] = [
                                'staff_id'          => $staffPlayTesterId,
                                'staff_name'        => $staffPlayTesterName,
                                'staff_role'        => $staffPlayTesterRole,
                                'staff_flag'        => $staffPlayTesterFlag,
                                'staff_image'       => $staffPlayTesterImage,
                                'staff_url'         => $staffPlayTesterUrl,
                                'staff_rank'        => $staffPlayTesterRank,
                                'staff_time_zone'   => $staffPlayTesterTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR REFEREE ROLE ***/
                        $staffRefereeRoleJsonData = $staffRoleJsonData['REFEREE'];
                        foreach ($staffRefereeRoleJsonData as $staffRefereeIdJsonData) {
                            $staffRefereeData = getTournamentStaffData(
                                id: $staffRefereeIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffRefereeId        = $staffRefereeData['id'];
                            $staffRefereeName      = $staffRefereeData['username'];
                            $staffRefereeRole      = 'REFEREE';
                            $staffRefereeFlag      = $staffRefereeData['country_code'];
                            $staffRefereeImage     = $staffRefereeData['avatar_url'];
                            $staffRefereeUrl       = "https://osu.ppy.sh/users/{$staffRefereeData['id']}";
                            $staffRefereeRank      = $staffRefereeData['statistics']['global_rank'];
                            $staffRefereeTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffRefereeData[] = [
                                'staff_id'          => $staffRefereeId,
                                'staff_name'        => $staffRefereeName,
                                'staff_role'        => $staffRefereeRole,
                                'staff_flag'        => $staffRefereeFlag,
                                'staff_image'       => $staffRefereeImage,
                                'staff_url'         => $staffRefereeUrl,
                                'staff_rank'        => $staffRefereeRank,
                                'staff_time_zone'   => $staffRefereeTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR STREAMER ROLE ***/
                        $staffStreamerRoleJsonData = $staffRoleJsonData['STREAMER'];
                        foreach ($staffStreamerRoleJsonData as $staffStreamerIdJsonData) {
                            $staffStreamerData = getTournamentStaffData(
                                id: $staffStreamerIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffStreamerId        = $staffStreamerData['id'];
                            $staffStreamerName      = $staffStreamerData['username'];
                            $staffStreamerRole      = 'STREAMER';
                            $staffStreamerFlag      = $staffStreamerData['country_code'];
                            $staffStreamerImage     = $staffStreamerData['avatar_url'];
                            $staffStreamerUrl       = "https://osu.ppy.sh/users/{$staffStreamerData['id']}";
                            $staffStreamerRank      = $staffStreamerData['statistics']['global_rank'];
                            $staffStreamerTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffStreamerData[] = [
                                'staff_id'          => $staffStreamerId,
                                'staff_name'        => $staffStreamerName,
                                'staff_role'        => $staffStreamerRole,
                                'staff_flag'        => $staffStreamerFlag,
                                'staff_image'       => $staffStreamerImage,
                                'staff_url'         => $staffStreamerUrl,
                                'staff_rank'        => $staffStreamerRank,
                                'staff_time_zone'   => $staffStreamerTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR COMMENTATOR ROLE ***/
                        $staffCommentatorRoleJsonData = $staffRoleJsonData['COMMENTATOR'];
                        foreach ($staffCommentatorRoleJsonData as $staffCommentatorIdJsonData) {
                            $staffCommentatorData = getTournamentStaffData(
                                id: $staffCommentatorIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffCommentatorId        = $staffCommentatorData['id'];
                            $staffCommentatorName      = $staffCommentatorData['username'];
                            $staffCommentatorRole      = 'COMMENTATOR';
                            $staffCommentatorFlag      = $staffCommentatorData['country_code'];
                            $staffCommentatorImage     = $staffCommentatorData['avatar_url'];
                            $staffCommentatorUrl       = "https://osu.ppy.sh/users/{$staffCommentatorData['id']}";
                            $staffCommentatorRank      = $staffCommentatorData['statistics']['global_rank'];
                            $staffCommentatorTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffCommentatorData[] = [
                                'staff_id'          => $staffCommentatorId,
                                'staff_name'        => $staffCommentatorName,
                                'staff_role'        => $staffCommentatorRole,
                                'staff_flag'        => $staffCommentatorFlag,
                                'staff_image'       => $staffCommentatorImage,
                                'staff_url'         => $staffCommentatorUrl,
                                'staff_rank'        => $staffCommentatorRank,
                                'staff_time_zone'   => $staffCommentatorTimeZone
                            ];
                        }


                        /*** STAFF DATA FOR STATISTICIAN ROLE ***/
                        $staffStatisticianRoleJsonData = $staffRoleJsonData['STATISTICIAN'];
                        foreach ($staffStatisticianRoleJsonData as $staffStatisticianIdJsonData) {
                            $staffStatisticianData = getTournamentStaffData(
                                id: $staffStatisticianIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffStatisticianId        = $staffStatisticianData['id'];
                            $staffStatisticianName      = $staffStatisticianData['username'];
                            $staffStatisticianRole      = 'STATISTICIAN';
                            $staffStatisticianFlag      = $staffStatisticianData['country_code'];
                            $staffStatisticianImage     = $staffStatisticianData['avatar_url'];
                            $staffStatisticianUrl       = "https://osu.ppy.sh/users/{$staffStatisticianData['id']}";
                            $staffStatisticianRank      = $staffStatisticianData['statistics']['global_rank'];
                            $staffStatisticianTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffStatisticianData[] = [
                                'staff_id'          => $staffStatisticianId,
                                'staff_name'        => $staffStatisticianName,
                                'staff_role'        => $staffStatisticianRole,
                                'staff_flag'        => $staffStatisticianFlag,
                                'staff_image'       => $staffStatisticianImage,
                                'staff_url'         => $staffStatisticianUrl,
                                'staff_rank'        => $staffStatisticianRank,
                                'staff_time_zone'   => $staffStatisticianTimeZone
                            ];
                        }
                        break;

                    case 'HOST':
                        /*** STAFF DATA FOR HOST ROLE ***/
                        $staffHostRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffHostRoleJsonData as $staffHostIdJsonData) {
                            $staffHostData = getTournamentStaffData(
                                id: $staffHostIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffHostId        = $staffHostData['id'];
                            $staffHostName      = $staffHostData['username'];
                            $staffHostRole      = $role;
                            $staffHostFlag      = $staffHostData['country_code'];
                            $staffHostImage     = $staffHostData['avatar_url'];
                            $staffHostUrl       = "https://osu.ppy.sh/users/{$staffHostData['id']}";
                            $staffHostRank      = $staffHostData['statistics']['global_rank'];
                            $staffHostTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffHostData[] = [
                                'staff_id'          => $staffHostId,
                                'staff_name'        => $staffHostName,
                                'staff_role'        => $staffHostRole,
                                'staff_flag'        => $staffHostFlag,
                                'staff_image'       => $staffHostImage,
                                'staff_url'         => $staffHostUrl,
                                'staff_rank'        => $staffHostRank,
                                'staff_time_zone'   => $staffHostTimeZone
                            ];
                        }

                    case 'MAPPOOLER':
                        /*** STAFF DATA FOR MAPPOOLER ROLE ***/
                        $staffMappoolerRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffMappoolerRoleJsonData as $staffMappoolerIdJsonData) {
                            $staffMappoolerData = getTournamentStaffData(
                                id: $staffMappoolerIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffMappoolerId        = $staffMappoolerData['id'];
                            $staffMappoolerName      = $staffMappoolerData['username'];
                            $staffMappoolerRole      = $role;
                            $staffMappoolerFlag      = $staffMappoolerData['country_code'];
                            $staffMappoolerImage     = $staffMappoolerData['avatar_url'];
                            $staffMappoolerUrl       = "https://osu.ppy.sh/users/{$staffMappoolerData['id']}";
                            $staffMappoolerRank      = $staffMappoolerData['statistics']['global_rank'];
                            $staffMappoolerTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffMappoolerData[] = [
                                'staff_id'          => $staffMappoolerId,
                                'staff_name'        => $staffMappoolerName,
                                'staff_role'        => $staffMappoolerRole,
                                'staff_flag'        => $staffMappoolerFlag,
                                'staff_image'       => $staffMappoolerImage,
                                'staff_url'         => $staffMappoolerUrl,
                                'staff_rank'        => $staffMappoolerRank,
                                'staff_time_zone'   => $staffMappoolerTimeZone
                            ];
                        }
                        break;

                    case 'GFX_VFX':
                        /*** STAFF DATA FOR GFX/VFX ROLE ***/
                        $staffGfxVfxRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffGfxVfxRoleJsonData as $staffGfxVfxIdJsonData) {
                            $staffGfxVfxData = getTournamentStaffData(
                                id: $staffGfxVfxIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffGfxVfxId        = $staffGfxVfxData['id'];
                            $staffGfxVfxName      = $staffGfxVfxData['username'];
                            $staffGfxVfxRole      = $role;
                            $staffGfxVfxFlag      = $staffGfxVfxData['country_code'];
                            $staffGfxVfxImage     = $staffGfxVfxData['avatar_url'];
                            $staffGfxVfxUrl       = "https://osu.ppy.sh/users/{$staffGfxVfxData['id']}";
                            $staffGfxVfxRank      = $staffGfxVfxData['statistics']['global_rank'];
                            $staffGfxVfxTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffGfxVfxData[] = [
                                'staff_id'          => $staffGfxVfxId,
                                'staff_name'        => $staffGfxVfxName,
                                'staff_role'        => $staffGfxVfxRole,
                                'staff_flag'        => $staffGfxVfxFlag,
                                'staff_image'       => $staffGfxVfxImage,
                                'staff_url'         => $staffGfxVfxUrl,
                                'staff_rank'        => $staffGfxVfxRank,
                                'staff_time_zone'   => $staffGfxVfxTimeZone
                            ];
                        }
                        break;

                    case 'MAPPER':
                        /*** STAFF DATA FOR MAPPER ROLE ***/
                        $staffMapperRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffMapperRoleJsonData as $staffMapperIdJsonData) {
                            $staffMapperData = getTournamentStaffData(
                                id: $staffMapperIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffMapperId        = $staffMapperData['id'];
                            $staffMapperName      = $staffMapperData['username'];
                            $staffMapperRole      = $role;
                            $staffMapperFlag      = $staffMapperData['country_code'];
                            $staffMapperImage     = $staffMapperData['avatar_url'];
                            $staffMapperUrl       = "https://osu.ppy.sh/users/{$staffMapperData['id']}";
                            $staffMapperRank      = $staffMapperData['statistics']['global_rank'];
                            $staffMapperTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffMapperData[] = [
                                'staff_id'          => $staffMapperId,
                                'staff_name'        => $staffMapperName,
                                'staff_role'        => $staffMapperRole,
                                'staff_flag'        => $staffMapperFlag,
                                'staff_image'       => $staffMapperImage,
                                'staff_url'         => $staffMapperUrl,
                                'staff_rank'        => $staffMapperRank,
                                'staff_time_zone'   => $staffMapperTimeZone
                            ];
                        }
                        break;

                    case 'PLAY_TESTER':
                        /*** STAFF DATA FOR PLAY TESTER ROLE ***/
                        $staffPlayTesterRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffPlayTesterRoleJsonData as $staffPlayTesterIdJsonData) {
                            $staffPlayTesterData = getTournamentStaffData(
                                id: $staffPlayTesterIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffPlayTesterId        = $staffPlayTesterData['id'];
                            $staffPlayTesterName      = $staffPlayTesterData['username'];
                            $staffPlayTesterRole      = $role;
                            $staffPlayTesterFlag      = $staffPlayTesterData['country_code'];
                            $staffPlayTesterImage     = $staffPlayTesterData['avatar_url'];
                            $staffPlayTesterUrl       = "https://osu.ppy.sh/users/{$staffPlayTesterData['id']}";
                            $staffPlayTesterRank      = $staffPlayTesterData['statistics']['global_rank'];
                            $staffPlayTesterTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffPlayTesterData[] = [
                                'staff_id'          => $staffPlayTesterId,
                                'staff_name'        => $staffPlayTesterName,
                                'staff_role'        => $staffPlayTesterRole,
                                'staff_flag'        => $staffPlayTesterFlag,
                                'staff_image'       => $staffPlayTesterImage,
                                'staff_url'         => $staffPlayTesterUrl,
                                'staff_rank'        => $staffPlayTesterRank,
                                'staff_time_zone'   => $staffPlayTesterTimeZone
                            ];
                        }
                        break;

                    case 'REFEREE':
                        /*** STAFF DATA FOR REFEREE ROLE ***/
                        $staffRefereeRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffRefereeRoleJsonData as $staffRefereeIdJsonData) {
                            $staffRefereeData = getTournamentStaffData(
                                id: $staffRefereeIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffRefereeId        = $staffRefereeData['id'];
                            $staffRefereeName      = $staffRefereeData['username'];
                            $staffRefereeRole      = $role;
                            $staffRefereeFlag      = $staffRefereeData['country_code'];
                            $staffRefereeImage     = $staffRefereeData['avatar_url'];
                            $staffRefereeUrl       = "https://osu.ppy.sh/users/{$staffRefereeData['id']}";
                            $staffRefereeRank      = $staffRefereeData['statistics']['global_rank'];
                            $staffRefereeTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffRefereeData[] = [
                                'staff_id'          => $staffRefereeId,
                                'staff_name'        => $staffRefereeName,
                                'staff_role'        => $staffRefereeRole,
                                'staff_flag'        => $staffRefereeFlag,
                                'staff_image'       => $staffRefereeImage,
                                'staff_url'         => $staffRefereeUrl,
                                'staff_rank'        => $staffRefereeRank,
                                'staff_time_zone'   => $staffRefereeTimeZone
                            ];
                        }
                        break;

                    case 'STREAMER':
                        /*** STAFF DATA FOR STREAMER ROLE ***/
                        $staffStreamerRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffStreamerRoleJsonData as $staffStreamerIdJsonData) {
                            $staffStreamerData = getTournamentStaffData(
                                id: $staffStreamerIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffStreamerId        = $staffStreamerData['id'];
                            $staffStreamerName      = $staffStreamerData['username'];
                            $staffStreamerRole      = $role;
                            $staffStreamerFlag      = $staffStreamerData['country_code'];
                            $staffStreamerImage     = $staffStreamerData['avatar_url'];
                            $staffStreamerUrl       = "https://osu.ppy.sh/users/{$staffStreamerData['id']}";
                            $staffStreamerRank      = $staffStreamerData['statistics']['global_rank'];
                            $staffStreamerTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffStreamerData[] = [
                                'staff_id'          => $staffStreamerId,
                                'staff_name'        => $staffStreamerName,
                                'staff_role'        => $staffStreamerRole,
                                'staff_flag'        => $staffStreamerFlag,
                                'staff_image'       => $staffStreamerImage,
                                'staff_url'         => $staffStreamerUrl,
                                'staff_rank'        => $staffStreamerRank,
                                'staff_time_zone'   => $staffStreamerTimeZone
                            ];
                        }
                        break;

                    case 'COMMENTATOR':
                        /*** STAFF DATA FOR COMMENTATOR ROLE ***/
                        $staffCommentatorRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffCommentatorRoleJsonData as $staffCommentatorIdJsonData) {
                            $staffCommentatorData = getTournamentStaffData(
                                id: $staffCommentatorIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffCommentatorId        = $staffCommentatorData['id'];
                            $staffCommentatorName      = $staffCommentatorData['username'];
                            $staffCommentatorRole      = $role;
                            $staffCommentatorFlag      = $staffCommentatorData['country_code'];
                            $staffCommentatorImage     = $staffCommentatorData['avatar_url'];
                            $staffCommentatorUrl       = "https://osu.ppy.sh/users/{$staffCommentatorData['id']}";
                            $staffCommentatorRank      = $staffCommentatorData['statistics']['global_rank'];
                            $staffCommentatorTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffCommentatorData[] = [
                                'staff_id'          => $staffCommentatorId,
                                'staff_name'        => $staffCommentatorName,
                                'staff_role'        => $staffCommentatorRole,
                                'staff_flag'        => $staffCommentatorFlag,
                                'staff_image'       => $staffCommentatorImage,
                                'staff_url'         => $staffCommentatorUrl,
                                'staff_rank'        => $staffCommentatorRank,
                                'staff_time_zone'   => $staffCommentatorTimeZone
                            ];
                        }
                        break;

                    case 'STATISTICIAN':
                        /*** STAFF DATA FOR STATISTICIAN ROLE ***/
                        $staffStatisticianRoleJsonData = $staffRoleJsonData[$role];
                        foreach ($staffStatisticianRoleJsonData as $staffStatisticianIdJsonData) {
                            $staffStatisticianData = getTournamentStaffData(
                                id: $staffStatisticianIdJsonData,
                                token: $staffAccessToken
                            );

                            $staffStatisticianId        = $staffStatisticianData['id'];
                            $staffStatisticianName      = $staffStatisticianData['username'];
                            $staffStatisticianRole      = $role;
                            $staffStatisticianFlag      = $staffStatisticianData['country_code'];
                            $staffStatisticianImage     = $staffStatisticianData['avatar_url'];
                            $staffStatisticianUrl       = "https://osu.ppy.sh/users/{$staffStatisticianData['id']}";
                            $staffStatisticianRank      = $staffStatisticianData['statistics']['global_rank'];
                            $staffStatisticianTimeZone  = getUserTimeZone()['baseOffset'];

                            $allStaffStatisticianData[] = [
                                'staff_id'          => $staffStatisticianId,
                                'staff_name'        => $staffStatisticianName,
                                'staff_role'        => $staffStatisticianRole,
                                'staff_flag'        => $staffStatisticianFlag,
                                'staff_image'       => $staffStatisticianImage,
                                'staff_url'         => $staffStatisticianUrl,
                                'staff_rank'        => $staffStatisticianRank,
                                'staff_time_zone'   => $staffStatisticianTimeZone
                            ];
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
            break;

        default:
            # code...
            break;
    }

    getStaffData(data: $allStaffHostData);
    getStaffData(data: $allStaffMappoolerData);
    getStaffData(data: $allStaffGfxVfxData);
    getStaffData(data: $allStaffMapperData);
    getStaffData(data: $allStaffPlayTesterData);
    getStaffData(data: $allStaffRefereeData);
    getStaffData(data: $allStaffStreamerData);
    getStaffData(data: $allStaffCommentatorData);
    getStaffData(data: $allStaffStatisticianData);

    return [
        $allStaffHostData,
        $allStaffMappoolerData,
        $allStaffGfxVfxData,
        $allStaffMapperData,
        $allStaffPlayTesterData,
        $allStaffRefereeData,
        $allStaffStreamerData,
        $allStaffCommentatorData,
        $allStaffStatisticianData
    ];
}


function getTournamentStaffData(
    int $id,
    string $token
): array | bool {
    $httpAuthorisationType  = $token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';
    $staffUrl               = "https://osu.ppy.sh/api/v2/users/{$id}/taiko";

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];

        # CURL session will be handled manually through curl_setopt()
        $staffCurlHandle = curl_init(url: null);

        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_URL, value: $staffUrl);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $staffCurlResponse = curl_exec(handle: $staffCurlHandle);

        if (curl_errno(handle: $staffCurlHandle)) {
            error_log(curl_error(handle: $staffCurlHandle));
            curl_close(handle: $staffCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $staffReadableData = json_decode(
                json: $staffCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $staffCurlHandle);
            return $staffReadableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        );

        # CURL session will be handled manually through curl_setopt()
        $staffCurlHandle = curl_init(url: null);

        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_URL, value: $staffUrl);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $staffCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $staffCurlResponse = curl_exec(handle: $staffCurlHandle);

        if (curl_errno(handle: $staffCurlHandle)) {
            error_log(curl_error(handle: $staffCurlHandle));
            curl_close(handle: $staffCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $staffReadableData = json_decode(
                json: $staffCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $staffCurlHandle);
            return $staffReadableData;
        }
    }
}
