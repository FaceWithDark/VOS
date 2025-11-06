"use strict"; // Don't ask me why I need to do this, JS sucks in all shapes

const appendLocation = document.querySelector(".tournament-setting-page");
const appendParam = new URLSearchParams(window.location.search);

if (!(appendParam.has("tournament") && appendParam.has("round"))) {
    appendLocation.innerHTML = `
        <div id="tournament-setting-tournament-option">
            <form action="/setting/tournament" target="_self" autocomplete="off" method="get">
                <label for="tournament">Which tournament?</label>
                <select name="tournament" id="tournament" size="1" required>
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
            </form>
        </div>
    `;

    const appendDropDown = document.getElementById("tournament");

    appendDropDown.addEventListener(
        "change",
        () => {
            const tournamentOption = appendDropDown.value;
            let roundOption = "";

            switch (true) {
                // *** VOT5 TOURNAMENT ROUND OPTIONS ***
                case (/^(?:vot5|vietnameseosutaikotournament5)$/i).test(tournamentOption):
                    if (document.getElementById("tournament-setting-round-option") !== null) {
                        // Delete existen drop down selection that isn't belong to this case
                        document.getElementById("tournament-setting-round-option").remove();

                        // Then append the correct one in
                        roundOption = `
                            <div id="tournament-setting-round-option">
                                <label for="round">Which round?</label>
                                <select name="round" id="round" size="1" required>
                                    <option value="qualifiers">Qualifiers</option>
                                    <option value="groupstagesweek1">Group Stages (Week 1)</option>
                                    <option value="groupstagesweek2">Group Stages (Week 2)</option>
                                    <option value="semifinals">Semi Finals</option>
                                    <option value="finals">Finals</option>
                                    <option value="grandfinals">Grand Finals</option>
                                    <option value="allstars">All Stars</option>
                                </select>

                                <br><br>

                                <input type="submit" value="Confirm Selected Options">
                            </div>
                        `;
                        appendDropDown.insertAdjacentHTML("afterend", roundOption);
                        break;
                    } else {
                        // Append brand new drop down selection to specificed location
                        roundOption = `
                            <div id="tournament-setting-round-option">
                                <label for="round">Which round?</label>
                                <select name="round" id="round" size="1" required>
                                    <option value="qualifiers">Qualifiers</option>
                                    <option value="groupstagesweek1">Group Stages (Week 1)</option>
                                    <option value="groupstagesweek2">Group Stages (Week 2)</option>
                                    <option value="semifinals">Semi Finals</option>
                                    <option value="finals">Finals</option>
                                    <option value="grandfinals">Grand Finals</option>
                                    <option value="allstars">All Stars</option>
                                </select>

                                <br><br>

                                <input type="submit" value="Confirm Selected Options">
                            </div>
                        `;
                        appendDropDown.insertAdjacentHTML("afterend", roundOption);
                        break;
                    }

                // *** VOT4 TOURNAMENT ROUND OPTIONS ***
                case (/^(?:vot4|vietnameseosutaikotournament4)$/i).test(tournamentOption):
                    if (document.getElementById("tournament-setting-round-option") !== null) {
                        // Delete existen drop down selection that isn't belong to this case
                        document.getElementById("tournament-setting-round-option").remove();

                        // Then append the correct one in
                        roundOption = `
                            <div id="tournament-setting-round-option">
                                <label for="round">Which round?</label>
                                <select name="round" id="round" size="1" required>
                                    <option value="qualifiers">Qualifier</option>
                                    <option value="roundsof16">Round Of 16</option>
                                    <option value="quarterfinals">Quarter Final</option>
                                    <option value="semifinals">Semi Final</option>
                                    <option value="finals">Final</option>
                                    <option value="grandfinals">Grand Final</option>
                                    <option value="allstars">All Star</option>
                                </select>

                                <br><br>

                                <input type="submit" value="Confirm Selected Options">
                            </div>
                        `;
                        appendDropDown.insertAdjacentHTML("afterend", roundOption);
                        break;
                    } else {
                        // Append brand new drop down selection to specificed location
                        roundOption = `
                            <div id="tournament-setting-round-option">
                                <label for="round">Which round?</label>
                                <select name="round" id="round" size="1" required>
                                    <option value="qualifiers">Qualifier</option>
                                    <option value="roundsof16">Round Of 16</option>
                                    <option value="quarterfinals">Quarter Final</option>
                                    <option value="semifinals">Semi Final</option>
                                    <option value="finals">Final</option>
                                    <option value="grandfinals">Grand Final</option>
                                    <option value="allstars">All Star</option>
                                </select>

                                <br><br>

                                <input type="submit" value="Confirm Selected Options">
                            </div>
                        `;
                        appendDropDown.insertAdjacentHTML("afterend", roundOption);
                        break;
                    }

                default:
                    if (document.getElementById("tournament-setting-round-option") !== null) {
                        // Delete existen drop down selection that isn't belong to this case
                        document.getElementById("tournament-setting-round-option").remove();
                        alert(`You'll be able to append data for ${tournamentOption.toLocaleUpperCase()} soon. Please try again next time!!`);
                        break;
                    } else {
                        alert(`You'll be able to append data for ${tournamentOption.toLocaleUpperCase()} soon. Please try again next time!!`);
                        break;
                    }
            }
        }
    )
} else {
    // Clear the current HTML block
    appendLocation.innerHTML = "";

    const tournamentValue = appendParam.get("tournament");
    const roundValue = appendParam.get("round");
    const currentAppendPath = window.location.pathname;
    const currentAppendParam = window.location.search;

    // Tournament option regex matching handling
    switch (true) {
        // *** VOT5 TOURNAMENT ROUND OPTIONS ***
        case (/^(?:vot5|vietnameseosutaikotournament5)$/i).test(tournamentValue):
            // Round option regex matching handling
            switch (true) {
                // *** TOURNAMENT QUALIFIER MAPPOOL FORM ***
                case (/^(?:qualifier|qualifiers|qlf|qlfs)$/i).test(roundValue):
                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toLocaleUpperCase()} round in ${tournamentValue.toLocaleUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

                                <label for="beatmapType">Beatmap Type:</label>
                                <select name="beatmapType" id="beatmapType" size="1" required>
                                    <optgroup label="No Mod">
                                        <option value="nm1">NM1</option>
                                        <option value="nm2">NM2</option>
                                        <option value="nm3">NM3</option>
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
                                    </optgroup>

                                    <optgroup label="Free/Force Mod">
                                        <option value="fm1">FM1</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                // *** TOURNAMENT GROUP STAGE (WEEK 1) MAPPOOL FORM ***
                case (/^(?:groupstageweek1|groupstagesweek1|gsw1|gssw1)$/i).test(roundValue):
                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toLocaleUpperCase()} round in ${tournamentValue.toLocaleUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

                                <label for="beatmapType">Beatmap Type:</label>
                                <select name="beatmapType" id="beatmapType" size="1" required>
                                    <optgroup label="No Mod">
                                        <option value="nm1">NM1</option>
                                        <option value="nm2">NM2</option>
                                        <option value="nm3">NM3</option>
                                        <option value="nm4">NM4</option>
                                    </optgroup>

                                    <optgroup label="Hidden">
                                        <option value="hd1">HD1</option>
                                        <option value="hd2">HD2</option>
                                    </optgroup>

                                    <optgroup label="Hard Rock">
                                        <option value="hr1">HR1</option>
                                        <option value="hr2">HR2</option>
                                    </optgroup>

                                    <optgroup label="Night Core">
                                        <option value="nc1">NC1</option>
                                        <option value="nc2">NC2</option>
                                    </optgroup>

                                    <optgroup label="Free/Force Mod">
                                        <option value="fm1">FM1</option>
                                        <option value="fm2">FM2</option>
                                    </optgroup>

                                    <optgroup label="Tie Breaker">
                                        <option value="TB">TB</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                // *** TOURNAMENT GROUP STAGE (WEEK 2) MAPPOOL FORM ***
                case (/^(?:groupstageweek2|groupstagesweek2|gsw2|gssw2)$/i).test(roundValue):
                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toLocaleUpperCase()} round in ${tournamentValue.toLocaleUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

                                <label for="beatmapType">Beatmap Type:</label>
                                <select name="beatmapType" id="beatmapType" size="1" required>
                                    <optgroup label="No Mod">
                                        <option value="nm1">NM1</option>
                                        <option value="nm2">NM2</option>
                                        <option value="nm3">NM3</option>
                                        <option value="nm4">NM4</option>
                                    </optgroup>

                                    <optgroup label="Hidden">
                                        <option value="hd1">HD1</option>
                                        <option value="hd2">HD2</option>
                                    </optgroup>

                                    <optgroup label="Hard Rock">
                                        <option value="hr1">HR1</option>
                                        <option value="hr2">HR2</option>
                                    </optgroup>

                                    <optgroup label="Night Core">
                                        <option value="nc1">NC1</option>
                                        <option value="nc2">NC2</option>
                                    </optgroup>

                                    <optgroup label="Free/Force Mod">
                                        <option value="fm1">FM1</option>
                                        <option value="fm2">FM2</option>
                                    </optgroup>

                                    <optgroup label="Tie Breaker">
                                        <option value="TB">TB</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                // *** TOURNAMENT SEMI FINAL MAPPOOL FORM ***
                case (/^(?:semifinal|semifinals|sf|sfs)$/i).test(roundValue):
                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toLocaleUpperCase()} round in ${tournamentValue.toLocaleUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

                                <label for="beatmapType">Beatmap Type:</label>
                                <select name="beatmapType" id="beatmapType" size="1" required>
                                    <optgroup label="No Mod">
                                        <option value="nm1">NM1</option>
                                        <option value="nm2">NM2</option>
                                        <option value="nm3">NM3</option>
                                        <option value="nm4">NM4</option>
                                        <option value="nm5">NM5</option>
                                    </optgroup>

                                    <optgroup label="Hidden">
                                        <option value="hd1">HD1</option>
                                        <option value="hd2">HD2</option>
                                    </optgroup>

                                    <optgroup label="Hard Rock">
                                        <option value="hr1">HR1</option>
                                        <option value="hr2">HR2</option>
                                    </optgroup>

                                    <optgroup label="Night Core">
                                        <option value="nc1">NC1</option>
                                        <option value="nc2">NC2</option>
                                    </optgroup>

                                    <optgroup label="Free/Force Mod">
                                        <option value="fm1">FM1</option>
                                        <option value="fm2">FM2</option>
                                    </optgroup>

                                    <optgroup label="Flash Light">
                                        <option value="fl">FL</option>
                                    </optgroup>

                                    <optgroup label="Tie Breaker">
                                        <option value="TB">TB</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                // *** TOURNAMENT FINAL MAPPOOL FORM ***
                case (/^(?:final|finals|fnl|fnls)$/i).test(roundValue):
                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toLocaleUpperCase()} round in ${tournamentValue.toLocaleUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

                                <label for="beatmapType">Beatmap Type:</label>
                                <select name="beatmapType" id="beatmapType" size="1" required>
                                    <optgroup label="No Mod">
                                        <option value="nm1">NM1</option>
                                        <option value="nm2">NM2</option>
                                        <option value="nm3">NM3</option>
                                        <option value="nm4">NM4</option>
                                        <option value="nm5">NM5</option>
                                    </optgroup>

                                    <optgroup label="Hidden">
                                        <option value="hd1">HD1</option>
                                        <option value="hd2">HD2</option>
                                    </optgroup>

                                    <optgroup label="Hard Rock">
                                        <option value="hr1">HR1</option>
                                        <option value="hr2">HR2</option>
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

                                    <optgroup label="Tie Breaker">
                                        <option value="TB">TB</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                // *** TOURNAMENT GRAND FINAL & ALL STAR MAPPOOL DATA ***
                case (/^(?:grandfinal|grandfinals|gf|gfs)$/i).test(roundValue):
                case (/^(?:allstar|allstars|astr|astrs)$/i).test(roundValue):

                    /*
                     *==========================================================
                     * Because [All STAR] mappool is basically the same as
                     * [GRAND FINAL] mappool, so I'll just being a bit lazy here
                     * by using the [GRAND FINAL] mappool data directly. This'll
                     * prevent me from adding the same beatmap ID into the JSON
                     * data.
                     *==========================================================
                     */

                    // Then append a new HTML block
                    appendLocation.innerHTML = `
                        <div class="tournament-add-mappool-panel">
                            <h2>Add mappool data for ${roundValue.toUpperCase()} round in ${tournamentValue.toUpperCase()} tournament</h2>

                            <form action="${currentAppendPath}${currentAppendParam}" target="_self" autocomplete="off" enctype="application/x-www-form-urlencoded" method="post">
                                <!-- Hidden inputs to keep existing GET params -->
                                <input type="hidden" name="tournament" value="${tournamentValue}">
                                <input type="hidden" name="round" value="${roundValue}">

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

                                    <optgroup label="Tie Breaker">
                                        <option value="TB">TB</option>
                                    </optgroup>
                                </select>

                                <br /><br />

                                <label for="beatmapId">Beatmap ID:</label>
                                <input type="text" name="beatmapId" id="beatmapId" placeholder="E.G: 2264801" autofocus required />

                                <br /><br />

                                <input type="submit" value="Add Mappool Data">
                            </form>
                        </div>

                        <div class="tournament-view-data-panel">
                            <h2>View Mappool Data</h2>
                            <p>COMING SOON!!</p>
                        </div>
                    `;
                    break;

                default:
                    break;
            }
            break;

        // *** VOT4 TOURNAMENT ROUND OPTIONS ***
        case (/^(?:vot4|vietnameseosutaikotournament4)$/i).test(tournamentValue):
            break;

        default:
            break;
    }
}
