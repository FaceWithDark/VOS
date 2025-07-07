<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getTournamentStaff(
    string $tournament_name
): array {

    $staffJsonData              = __DIR__ . '/../Datas/Staff/VotStaffData.json';

    $allHostRoleStaffData       = [];
    $mappoolerRoleStaffData     = [];
    $gfxVfxRoleStaffData        = [];
    $mapperRoleStaffData        = [];
    $playTesterRoleStaffData    = [];
    $refereeRoleStaffData       = [];
    $streamerRoleStaffData      = [];
    $commentatorRoleStaffData   = [];
    $statisticianRoleStaffData  = [];

    $osuUserAccessToken         = $_COOKIE['vot_access_token'];

    switch ($tournament_name) {
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
                $allHostRoleJsonData = $staffRoleJsonData['host_role'];
                foreach ($allHostRoleJsonData as $hostRoleJsonData) {
                    $hostRoleStaffData = getTournamentStaffData(
                        staff_id: $hostRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $hostRoleStaffId        = $hostRoleStaffData['id'];
                    $hostRoleStaffName      = $hostRoleStaffData['username'];
                    $hostRoleStaffRole      = 'Host'; // TODO: I'll do it properly. Leave TODO here for now
                    $hostRoleStaffFlag      = $hostRoleStaffData['country_code'];
                    $hostRoleStaffImage     = $hostRoleStaffData['avatar_url'];
                    $hostRoleStaffUrl       = "https://osu.ppy.sh/users/{$hostRoleStaffData['id']}";
                    $hostRoleStaffRank      = $hostRoleStaffData['statistics']['global_rank'];
                    $hostRoleStaffTimeZone  = getUserTimeZone()['baseOffset'];

                    $allHostRoleStaffData[] = [
                        'staff_id'          => $hostRoleStaffId,
                        'staff_name'        => $hostRoleStaffName,
                        'staff_role'        => $hostRoleStaffRole,
                        'staff_flag'        => $hostRoleStaffFlag,
                        'staff_image'       => $hostRoleStaffImage,
                        'staff_url'         => $hostRoleStaffUrl,
                        'staff_rank'        => $hostRoleStaffRank,
                        'staff_time_zone'   => $hostRoleStaffTimeZone
                    ];
                }

                $allMappoolerRoleJsonData = $staffRoleJsonData['mappooler_role'];
                foreach ($allMappoolerRoleJsonData as $mappoolerRoleJsonData) {
                    $mappoolerRoleAppendableStaffData = getTournamentStaffData(
                        staff_id: $mappoolerRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $mappoolerRoleStaffData[] = $mappoolerRoleAppendableStaffData;
                }

                $allGfxVfxRoleJsonData = $staffRoleJsonData['gfx_vfx_role'];
                foreach ($allGfxVfxRoleJsonData as $gfxVfxRoleJsonData) {
                    $gfxVfxRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $gfxVfxRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $gfxVfxRoleStaffData[] = $gfxVfxRoleAppendableJsonData;
                }

                $allMapperRoleJsonData = $staffRoleJsonData['mapper_role'];
                foreach ($allMapperRoleJsonData as $mapperRoleJsonData) {
                    $mapperRoleAppendbleJsonData = getTournamentStaffData(
                        staff_id: $mapperRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $mapperRoleStaffData[] = $mapperRoleAppendbleJsonData;
                }

                $allPlayTesterRoleJsonData = $staffRoleJsonData['play_tester_role'];
                foreach ($allPlayTesterRoleJsonData as $playTesterRoleJsonData) {
                    $playTesterRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $playTesterRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $playTesterRoleStaffData[] = $playTesterRoleAppendableJsonData;
                }

                $allRefereeRoleJsonData = $staffRoleJsonData['referee_role'];
                foreach ($allRefereeRoleJsonData as $refereeRoleJsonData) {
                    $refereeRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $refereeRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $refereeRoleStaffData[] = $refereeRoleAppendableJsonData;
                }

                $allStreamerRoleJsonData = $staffRoleJsonData['streamer_role'];
                foreach ($allStreamerRoleJsonData as $streamerRoleJsonData) {
                    $streamerRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $streamerRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $streamerRoleStaffData[] = $streamerRoleAppendableJsonData;
                }

                $allCommentatorRoleJsonData = $staffRoleJsonData['commentator_role'];
                foreach ($allCommentatorRoleJsonData as $commentatorRoleJsonData) {
                    $commentatorRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $commentatorRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $commentatorRoleStaffData[] = $commentatorRoleAppendableJsonData;
                }

                $allStatisticianRoleJsonData  = $staffRoleJsonData['statistician_role'];
                foreach ($allStatisticianRoleJsonData as $statisticianRoleJsonData) {
                    $statisticianRoleAppendableJsonData = getTournamentStaffData(
                        staff_id: $statisticianRoleJsonData,
                        access_token: $osuUserAccessToken
                    );
                    $statisticianRoleStaffData[] = $statisticianRoleAppendableJsonData;
                }
            }
            break;
    }

    getStaffData(data: $allHostRoleStaffData);

    return [
        $hostRoleStaffData,
        $mappoolerRoleStaffData,
        $gfxVfxRoleStaffData,
        $mapperRoleStaffData,
        $playTesterRoleStaffData,
        $refereeRoleStaffData,
        $streamerRoleStaffData,
        $commentatorRoleStaffData,
        $statisticianRoleStaffData
    ];
}

function getTournamentStaffData(
    int $staff_id,
    string $access_token
): array | bool {
    $httpAuthorisationType  = $access_token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];

        $staffDataUrl = "https://osu.ppy.sh/api/v2/users/{$staff_id}/taiko";

        # CURL session will be handled manually through curl_setopt()
        $staffDataCurlHandle = curl_init(url: null);

        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_URL, value: $staffDataUrl);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $staffDataCurlResponse = curl_exec(handle: $staffDataCurlHandle);

        if (curl_errno(handle: $staffDataCurlHandle)) {
            error_log(curl_error(handle: $staffDataCurlHandle));
            curl_close(handle: $staffDataCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $staffReadableData = json_decode(
                json: $staffDataCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $staffDataCurlHandle);
            return $staffReadableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        );

        $staffDataUrl = "https://osu.ppy.sh/api/v2/users/{$staff_id}/taiko";

        # CURL session will be handled manually through curl_setopt()
        $staffDataCurlHandle = curl_init(url: null);

        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_URL, value: $staffDataUrl);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $staffDataCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $staffDataCurlResponse = curl_exec(handle: $staffDataCurlHandle);

        if (curl_errno(handle: $staffDataCurlHandle)) {
            error_log(curl_error(handle: $staffDataCurlHandle));
            curl_close(handle: $staffDataCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $staffReadableData = json_decode(
                json: $staffDataCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $staffDataCurlHandle);
            return $staffReadableData;
        }
    }
}
