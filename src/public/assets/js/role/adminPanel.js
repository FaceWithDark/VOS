"use strict"; // Don't ask me why I need to do this, JS sucks in all shapes

const appendLocation = document.querySelector(".admin-setting-page");
const appendParam = new URLSearchParams(window.location.search);

if (!(appendParam.has("option"))) {
    appendLocation.innerHTML = `
        <div id="admin-setting-admin-option">
            <form action="/setting/admin" target="_self" autocomplete="off" method="get">
                <label for="option">What do you want to do?</label>
                <select name="option" id="option" size="1" required>
                    <option value="addRole">Add role to specified user</option>
                    <option value="addBeatmap">Add deleted beatmap data</option>
                </select>

                <br /><br />

                <input type="submit" value="Confirm Selected Options">
            </form>
        </div>
    `;
} else {
    // Clear the current HTML block
    appendLocation.innerHTML = "";

    const selectedOption = appendParam.get("option");
    const currentAppendPath = window.location.pathname;
    const currentAppendParam = window.location.search;
    const currentAppendUrl = new URL(window.location.href);

    switch (true) {
        case (/^(?:addRole|addrole)$/i).test(selectedOption):
            appendLocation.innerHTML = `
                <div id="admin-add-role-option">
                    <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                        <label for="roleLevel">Which role level do you want to work with?</label>
                        <select name="roleLevel" id="roleLevel" size="1" required>
                            <optgroup label="Role Level">
                                <option value="generalRole">General</option>
                                <option value="tournamentRole">Tournament</option>
                            </optgroup>
                        </select>
                    </form>
                </div>
            `;

            const appendForm = document.getElementById("roleLevel");

            appendForm.addEventListener(
                "change",
                () => {
                    const selectedRoleOption = appendForm.value;
                    let selectedRoleForm = "";

                    switch (true) {
                        case (/^(?:generalRole|generalrole)$/i).test(selectedRoleOption):
                            if ((
                                (document.getElementById("admin-add-role-panel") !== null) &&
                                (document.getElementById("admin-view-role-panel") !== null)
                            )) {
                                // Delete existen drop down selection that isn't belong to this case
                                document.getElementById("admin-add-role-panel").remove();
                                document.getElementById("admin-view-role-panel").remove();

                                // Then append the correct one in
                                selectedRoleForm = `
                                    <div id="admin-add-role-panel">
                                        <h2>Add User for General Role Category</h2>

                                        <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                            <label for="userId">ID</label>
                                            <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                                            <br />

                                            <label for="userRole">Role</label>
                                            <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                                            <br />

                                            <input type="submit" value="Add General Role">
                                        </form>
                                    </div>

                                    <div id="admin-view-role-panel">
                                        <h2>View General Role Data</h2>
                                        <p>COMING SOON!!</p>
                                    </div>
                                `;
                                appendForm.insertAdjacentHTML("afterend", selectedRoleForm);
                                break;
                            } else {
                                // Append brand new drop down selection to specificed location
                                selectedRoleForm = `
                                    <div id="admin-add-role-panel">
                                        <h2>Add User for General Role Category</h2>

                                        <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                            <label for="userId">ID</label>
                                            <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                                            <br />

                                            <label for="userRole">Role</label>
                                            <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                                            <br />

                                            <input type="submit" value="Add General Role">
                                        </form>
                                    </div>

                                    <div id="admin-view-role-panel">
                                        <h2>View General Role Data</h2>
                                        <p>COMING SOON!!</p>
                                    </div>
                                `;
                                appendForm.insertAdjacentHTML("afterend", selectedRoleForm);
                                break;
                            }

                        case (/^(?:tournamentRole|tournamentrole)$/i).test(selectedRoleOption):
                            if ((
                                (document.getElementById("admin-add-role-panel") !== null) &&
                                (document.getElementById("admin-view-role-panel") !== null)
                            )) {
                                // Delete existen drop down selection that isn't belong to this case
                                document.getElementById("admin-add-role-panel").remove();
                                document.getElementById("admin-view-role-panel").remove();

                                // Then append the correct one in
                                selectedRoleForm = `
                                    <div id="admin-add-role-panel">
                                        <h2>Add User for Tournament Role Category</h2>

                                        <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                            <label for="userId">ID</label>
                                            <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                                            <br />

                                            <label for="userRole">Role</label>
                                            <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                                            <br />

                                            <label for="userTournament">Tournament</label>
                                            <input type="text" name="userTournament" placeholder="E.G: VOT5" minlength="4" maxlength="5" autofocus required />
                                            <br />

                                            <input type="submit" value="Add Tournament Role">
                                        </form>
                                    </div>

                                    <div id="admin-view-role-panel">
                                        <h2>View Tournament Role Data</h2>
                                        <p>COMING SOON!!</p>
                                    </div>
                                `;
                                appendForm.insertAdjacentHTML("afterend", selectedRoleForm);
                                break;
                            } else {
                                // Append brand new drop down selection to specificed location
                                selectedRoleForm = `
                                    <div id="admin-add-role-panel">
                                        <h2>Add User for Tournament Role Category</h2>

                                        <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                            <label for="userId">ID</label>
                                            <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                                            <br />

                                            <label for="userRole">Role</label>
                                            <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                                            <br />

                                            <label for="userTournament">Tournament</label>
                                            <input type="text" name="userTournament" placeholder="E.G: VOT5" minlength="4" maxlength="5" autofocus required />
                                            <br />

                                            <input type="submit" value="Add Tournament Role">
                                        </form>
                                    </div>

                                    <div id="admin-view-role-panel">
                                        <h2>View Tournament Role Data</h2>
                                        <p>COMING SOON!!</p>
                                    </div>
                                `;
                                appendForm.insertAdjacentHTML("afterend", selectedRoleForm);
                                break;
                            }

                        default:
                            if (!(currentAppendUrl.search)) {
                                // Quick log for further debugs
                                console.log("Correct URL found, no redirection needed.");
                                break;
                            } else {
                                // Clear out current query params
                                currentAppendUrl.search = "";

                                // Then redirect user to original path
                                window.location.href = currentAppendUrl.toString();
                                break;
                            }
                    }
                }
            )
            break;

        case (/^(?:addBeatmap|addbeatmap)$/i).test(selectedOption):
            appendLocation.innerHTML = `
                <div id="admin-add-beatmap-panel">
                    <h2>Manually add beatmap data for certain tournament</h2>

                    <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="multipart/form-data" method="post">
                        <label for="beatmapId">Beatmap ID:</label>
                        <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                        <br /><br />

                        <label for="roundId">Round ID:</label>
                        <select name="roundId" id="roundId" size="1" required>
                            <option value="qualifiers">Qualifier</option>
                            <option value="groupstagesweek1">Group Stages (Week 1)</option>
                            <option value="groupstagesweek2">Group Stages (Week 2)</option>
                            <option value="roundsof16">Round Of 16</option>
                            <option value="quarterfinals">Quarter Final</option>
                            <option value="semifinals">Semi Final</option>
                            <option value="finals">Final</option>
                            <option value="grandfinals">Grand Final</option>
                            <option value="allstars">All Star</option>
                        </select>

                        <br /><br />

                        <label for="tournamentId">Tournament ID:</label>
                        <select name="tournamentId" id="tournamentId" size="1" required>
                            <optgroup label="Vietnamese osu!Taiko Tournament">
                                <option value="vot1">VOT1</option>
                                <option value="vot2">VOT2</option>
                                <option value="vot3">VOT3</option>
                                <option value="vot4">VOT4</option>
                                <option value="vot5">VOT5</option>
                            </optgroup>

                            <optgroup label="Vietnamese osu!Taiko Colosseum">
                                <option value="vtc1">VTC1</option>
                                <option value="vtc2">VTC2</option>
                                <option value="vtc3">VTC3</option>
                            </optgroup>
                        </select>

                        <br /><br />

                        <label for="beatmapType">Beatmap Type:</label>
                        <select name="beatmapType" id="beatmapType" size="1" required>
                            <optgroup label="No Mod">
                                <option value="nm1">NM1</option>
                                <option value="nm2">NM2</option>
                                <option value="nm3">NM3</option>
                                <option value="nm4">NM4</option>
                                <option value="nm5">NM5</option>
                                <option value="nm6">NM6</option>
                                <option value="nm7">NM7</option>
                            </optgroup>

                            <optgroup label="Hidden">
                                <option value="hd1">HD1</option>
                                <option value="hd2">HD2</option>
                            </optgroup>

                            <optgroup label="Hard Rock">
                                <option value="hr1">HR1</option>
                                <option value="hr2">HR2</option>
                            </optgroup>

                            <optgroup label="Double Time">
                                <option value="dt1">DT1</option>
                                <option value="dt2">DT2</option>
                            </optgroup>

                            <optgroup label="Night Core">
                                <option value="nc1">NC1</option>
                                <option value="nc2">NC2</option>
                            </optgroup>

                            <optgroup label="Free/Force Mod">
                                <option value="fm1">FM1</option>
                                <option value="fm2">FM2</option>
                                <option value="fm3">FM3</option>
                            </optgroup>

                            <optgroup label="Easy">
                                <option value="ez">EZ</option>
                            </optgroup>

                            <optgroup label="Hidden + Hard Rock">
                                <option value="hdhr">HDHR</option>
                            </optgroup>

                            <optgroup label="Flash Light">
                                <option value="fl">FL</option>
                            </optgroup>

                            <optgroup label="Tie Breaker">
                                <option value="TB">TB</option>
                            </optgroup>
                        </select>

                        <br /><br />

                        <label for="beatmapImage">Beatmap Image:</label>
                        <input type="file" name="beatmapImage" id="beatmapImage" accept=".jpg,.jpeg,.gif,.png,.webp" autofocus required />

                        <br /><br />

                        <label for="beatmapUrl">Beatmap Url:</label>
                        <input type="text" name="beatmapUrl" id="beatmapUrl" placeholder="E.G: https://osu.ppy.sh/beatmapsets/1082737#taiko/2264801" autofocus required />

                        <br /><br />

                        <label for="beatmapName">Beatmap Name:</label>
                        <input type="text" name="beatmapName" id="beatmapName" placeholder="E.G: 少女レイ" autofocus required />

                        <br /><br />

                        <label for="beatmapDifficultyName">Beatmap Diff Name:</label>
                        <input type="text" name="beatmapDifficultyName" id="beatmapDifficultyName" placeholder="E.G: Baidori Oni" autofocus required />

                        <br /><br />

                        <label for="beatmapFeatureArtist">Beatmap FA:</label>
                        <input type="text" name="beatmapFeatureArtist" id="beatmapFeatureArtist" placeholder="E.G: みきとP" autofocus required />

                        <br /><br />

                        <label for="beatmapMapper">Beatmap Mapper:</label>
                        <input type="text" name="beatmapMapper" id="beatmapMapper" placeholder="E.G: きたふま" autofocus required />

                        <br /><br />

                        <label for="beatmapMapperUrl">Beatmap Mapper URL:</label>
                        <input type="text" name="beatmapMapperUrl" id="beatmapMapperUrl" placeholder="E.G: https://osu.ppy.sh/users/8987606" autofocus required />

                        <br /><br />

                        <label for="beatmapDifficulty">Beatmap SR:</label>
                        <input type="text" name="beatmapDifficulty" id="beatmapDifficulty" placeholder="E.G: 7.95" autofocus required />

                        <br /><br />

                        <label for="beatmapLength">Beatmap Length:</label>
                        <input type="text" name="beatmapLength" id="beatmapLength" placeholder="E.G: 4.34" autofocus required />

                        <br /><br />

                        <label for="beatmapOverallSpeed">Beatmap BPM:</label>
                        <input type="text" name="beatmapOverallSpeed" id="beatmapOverallSpeed" placeholder="E.G: 150" autofocus required />

                        <br /><br />

                        <label for="beatmapOverallDifficulty">Beatmap OD:</label>
                        <input type="text" name="beatmapOverallDifficulty" id="beatmapOverallDifficulty" placeholder="E.G: 7.00" autofocus required />

                        <br /><br />

                        <label for="beatmapOverallHealth">Beatmap HP:</label>
                        <input type="text" name="beatmapOverallHealth" id="beatmapOverallHealth" placeholder="E.G: 5.00" autofocus required />

                        <br /><br />

                        <strong>Is it a custom-made beatmap?</strong>
                        <input type="radio" name="beatmapCategory" id="customBeatmap" value="Yes">
                        <label for="customBeatmap">Yes</label>
                        <input type="radio" name="beatmapCategory" id="normalBeatmap" value="No">
                        <label for="normalBeatmap">No</label>

                        <br /><br />

                        <input type="submit" value="Add Beatmap Data">
                    </form>
                </div>

                <div class="admin-view-beatmap-panel">
                    <h2>View Beatmap Data</h2>
                    <p>COMING SOON!!</p>
                </div>
            `;
            break;

        default:
            if (!(currentAppendUrl.search)) {
                // Quick log for further debugs
                console.log("Correct URL found, no redirection needed.");
                break;
            } else {
                // Clear out current query params
                currentAppendUrl.search = "";

                // Then redirect user to original path
                window.location.href = currentAppendUrl.toString();
                break;
            }
    }
}
