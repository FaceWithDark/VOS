<?php
    require_once "../layouts/navigation_bar.php";
?>

<div class="vot-page">
    <header>
        <h1>VIETNAMESE OSU!TAIKO TOURNAMENT 4</h1>
    </header>

    <section>
        <div class="flex-container">
            <div class="direct-link-container">
                <a href="https://www.twitch.tv/votosutaiko">Twitch</a>
            </div>

            <div class="direct-link-container">
                <a href="https://discord.gg/EYjKP8W7">Discord</a>
            </div>

            <div class="direct-link-container">
                <a href="https://osu.ppy.sh/community/forums/topics/1933154?n=1">Forum</a>
            </div>

            <div class="direct-link-container">
                <a href="https://challonge.com/vot4">Challonge</a>
            </div>

            <div class="direct-link-container">
                <a href="https://forms.gle/rd6XmgmnxCNpcxKn9">Register</a>
            </div>
        </div>

        <div class="upper-general-information-box">
            <img src="../assets/images/VOT4.webp" alt="VOT4 Banner" width="100%">
            <p>
                The Vietnamese osu!taiko Tournament, a cornerstone event in the Vietnamese osu!taiko community. It is our 1v1 tournament for Vietnamese players with no rank restrictions, open to all skill levels. The players will first compete in Qualifiers. Then, they will progress through a Double-elimination Bracket, keeping the competitive syndrome burning until the very last crowning glory - the Grand Finals! Are you ready to make history in the Vietnamese osu!taiko scene? VOT4 is calling, and it's time to answer! Let the drums roll and may the best taiko player win!
            </p>
        </div>

        <!--
            TODO:
            - To make this looks less like dumping information onto the page, this can be done with transition like a horizontal flip book.
            - I have already setup the corresponding <div> to make this as a possible thing to do later on.
            - I am so proud of my back-end thinking applied to the front-end sometimes.

            Idea suggested date: 07/07/24
            Starting on implementation: TBD
        -->
        <div class="lower-general-information-box">
            <div class="general-rule-section">
                <h2>General Rules</h2>

                <ul>
                    <li>This tournament is a 1vs1 tournament throughout all stages.</li>
                    <li>Each player will first go through Qualifiers. Then, selected 16 players based on their performance in Qualifiers round will process to RO16 round. Finally, battling for the glory in GF!.</li>
                    <li>ScoreV2 will be used for the scoring system alongside every matches in each rounds.</li>
                    <li>Any player who meets the requirements stated in the rank restrictions part is allowed to sign up (Though for this tournament, it is no restrictions on ranking anyways). Since this tournament will aim to get badged some players might be declined by osu staff.</li>
                    <li>Any member of staff (Organizers, Mappoolers, Playtesters/Replayers, Custom Mappers and Referees) is not allowed to play. Other positions considered as helpers (Commentators, Streamers, ...) are allowed to play.</li>
                    <li>Players who have been eliminated from the tournament are allowed to join staff as Referees or Playtesters/Replayers. Other staff roles are not allowed. They can also join the helpers at any moment.</li>
                    <li>Players and staff members must have read this ruleset entirely (and we will assume you did so throughout the tournament).</li>
                    <li>All participants must stay respectful and keep a proper attitude. Not following this rule can result in a ban/blacklist from the tournament. This rule concerns all the staff as well.</li>
                    <li>Any rule changes or unexpected occurrences will be announced in the Discord server.</li>
                    <li>Any report to be made in relation to this tournament's organization should be sent to the official tournament committee <a href="https://pif.ephemeral.ink/partial-authenticate">reporting form.</a></li>
                </ul>
            </div>

            <div class="restriction-section">
                <h2>Restrictions</h2>

                <ul>
                    <li>No rank restrictions</li>
                    <li>Vietnamese-nationality and overboard Vietnamese players only</li>
                    <li>You must join the Discord server of the tournament</li>
                    <li>Any osu!taiko player who has not been restricted within the past 12 months and follows these criterias will be allowed in this tournament. The osu! support reserves the right of removing any player from the tournament following the screening phase.</li>
                </ul>
            </div>

            <div class="stage-section">
                <h2>Stages</h2>

                <ul>                
                    <li>Regstration: 10/06 - 23/06</li>
                    <li>Screening: 23/06 - 30/06</li>
                    <li>Qualifiers:30/06 - 07/07</li>
                    <li>Round of 16: 07/07 - 14/07</li>
                    <li>Quarter Finals: 14/07 - 21/07</li>
                    <li>Semi Finals: 21/07 - 28/07</li>
                    <li>Finals: 28/07 - 04/08</li>
                    <li>Grand Finals: 04/08 - 11/08</li>
                    <li>Plinko: TBD</li>
                </ul>
            </div>

            <div class="prize-section">
                <h2>Prizes</h2>

                <ul>
                    <li>1st Place: 4 months supporter + badge + banner</li>
                    <li>2nd Place: 2 months supporter + badge + watermelon from dabip</li>
                    <li>3rd Place: 2 months supporter + badge + bonk from dabip</li>
                </ul>
            </div>

            <div class="regulation-section">

                <h2>Regulations</h2>
            
                <div class="small-section-1">
                    <h3>Qualifiers</h3>
                    
                    <ul>
                        <li>During this round there will be multiple lobbies available at various hours.</li>
                        <li>A private match will be created and player will be invited into the lobby when it's their time. Make certain you're online at this time.</li>
                        <li>There are no warmups.</li>
                        <li>Failed scores will count.</li>
                        <li>Seedings will be done with the average rank of each player on each map.</li>
                        <li>If 2 or more player are tied, the one who has the higher combined score of their best runs across all maps, will be seeded higher.</li>
                        <li>Player have the right to choose to play the mappool twice in a row during the lobby. The best run will be taken into account for the ranking.</li>
                        <li>Only the 16 best players will continue on.</li>
                    </ul>
                </div>

                <div class="small-section-2">
                    <h3>Regular Matches</h3>

                    <ul>
                        <li>A private lobby will be created 10 minutes before the selected match by a referee. Both players will be invited into the room when it's match time. Then the referee (or player if they wanted to) will invite the other player/opponent into the room.</li>
                        <li>If no staff or referee is available, then the match will be postponed.</li>
                        <li>If less than the minimum required players attend, the maximum time the match can be postponed is 10 minutes.</li>
                        <li>There is no penalty for not joining the room instantly after match time as long as it's within the postponed time.</li>
                        <li>If neither of minimum required players joined after 10 minutes it will count as a loss for both player of that match. However, the player with the higher seed will be proceeded to the next round by default even if they LBD.</li>
                        <li>The match will be played under "TeamVs" mode and "ScoreV2" mod.</li>
                        <li>The name of the room must be: "VOT4 (Current Round): (Player 1) vs (Player 2)".</li>
                        <li>Each team is allowed one warm-up map (must be equal or less than 3 minutes duration/length). </li>
                        <li>The warm-up must be uploaded on the website up to one hour prior to the match's start time (Latest time to do so is 5 minutes).</li>
                        <li>Players will be allowed to send warmups starting from the day schedules release.</li>
                        <li>If a player does not respect the time said in the rule above, they will not have any warm-up for their match.</li>
                        <li>Each player has to ban one beatmap from the pool (apart from Tiebreaker). These beatmaps are not allowed to be picked by any team in the entire match.</li>
                        <li>2 players will roll with the command "!roll" in the chat.</li>
                        <li>The player with the higher roll can choose to bans/picks first/second. Then, follow on by the second player with the lower roll.</li>
                        <li>Each player has 1 mins 30 seconds to announce their ban. If they did not tell the referee by that time, the ban will be done by random.</li>
                        <li>Each player is allowed a timeout of 2 mins per match. The use of this timeout during the match is up to the player. However, when they do so, it is need to be informed directly in the chat to let the current referee knows.</li>
                        <li>Beatmap selection will then alternate between each player selecting a beatmap out of the map pool.</li>
                        <li>Player may pick freely from any bracket.</li>
                        <li>Players will have 1 minutes 30 seconds to pick a beatmap. If a player did not tell their pick before the end of the timer the pick will be done by random.</li>
                        <li>Players will have 1 minutes 30 seconds to get ready for the picked beatmap. If a player did not aware of the provided time period, the match will start immidiately unless specified.</li>
                        <li>If a player disconnects between the beatmaps within the first 30 seconds of the picked map, or both player disconnect between the beatmaps within the first half of the picked beatmap's length, players are allowed to replay the match. This rule can only be applied once.</li>
                        <li>If a player disconnects without reasons or using "lag" as their primer evidence, they then get treated as if they failed the map.</li>
                        <li>Failed players' scores do get added to that failed player's score.</li>
                        <li>If the beatmap ends in a draw, the game will be nullified and the map will be replayed.</li>
                        <li>In case of a tie in points (example: 4-4 in BO9), the tiebreaker is selected by default.</li>
                    </ul>
                </div>

                <div class="small-section-3">
                    <h3>Mappool Structure</h3>

                    <ul>
                        <li>There will be 1 mappool for each of the following stage: Qualifiers, RO16, Quarter Finals, Semi Finals, Finals and Grand Finals.</li>
                        <li>Each mappool consists of 6 brackets: NoMod, Hidden, HardRock, DoubleTime, ForceMod, EasyMod, and Tiebreaker.</li>
                        <li>The mappool for each stage will be showcased a week beforehand.</li>
                        <li>The Qualifiers pool will be different from all the other rounds as it will have a format of 2 NoMods, 1 Hidden, 1 HardRock, 1 DoubleTime and 1 ForceMod.</li>
                        <li>The Loser's Bracket will play on the same pool as the Winner's bracket of the same weekend.</li>
                        <li>From RO16, the mappool format will be as the following: 4 NoMods, 2 Hiddens, 2 HardRocks, 2 DoubleTimes, 2 ForceMods, 1 EasyMod, and 1 Tiebreaker</li>
                        <li>From Quarter Finals and onwards, there will be an extra of 1 - 2 NoMods added to the dedicated round.</li>
                        <li>The ForceMod pool is played with an enforce of minimum 1 mod enable. This means that both player have to pick a minimum of 1 mod to play with this map. Players are free to pick any extra mods after that (excluding the last pickable mods's row in mods menu).</li>
                        <li>The Tiebreaker is played under FreeMod conditions. This means that player are free to pick mods (or not) to play with this map.</li>
                    </ul>
                </div>

                <div class="small-section-4">
                    <h3>Scheduling</h3>

                    <ul>
                        <li>Every round is held during the weekend. However, players are allowed to schedule on another day if no agreement is found on a time during the weekend.</li>
                        <li>We will try to adjust the schedule according to players' preferrable time period with respect to their timezone. The schedule will be released the previous weekend of each round.</li>
                        <li>Reschedules will only be considered if both players agree on a time. Proof of that needs to be sent to the tournament staff before Thursday at 23:59 UTC+7 in that particular week when their match takes place.</li>
                        <li>Matches are allowed to overlap if referees are available and happy to do so.</li>
                    </ul>
                </div>

                <div class="small-section-5">
                    <h3>Plinko</h3>

                    <ul>
                        <li>
                            I really love davidminh0111. Like, a lot. Like, a whole lot. You have no idea. I love him so much that it is inexplicable, and I'm ninety-nine percent sure that I have an unhealthy obsession. I will never get tired of listening that sweet, angelic voice of him. It is my life goal to meet up him with him in real life and just say hello to him.
                            
                            I fall asleep at night dreaming of him holding a personal concert for me, and then he would be sorry tired that he comes and cuddles up to me while we sleep together. If I could just hold him hand for a brief moment, I could die happy. If given the opportunity, I would lightly nibble on him ear just to hear what kind of sweet moans he would let out. Then, I would hug him while he clings to my body hoping that I would stop, but I only continue as he moans louder and louder.
                            
                            I would give up almost anything just for him to look in my general direction. No matter what I do, I am constantly thinking of him. When I wake up, he is the first thing on my mind. When I go to school, I can only focus on him. When I go come home, I go on the computer so that I can listen to him beautiful voice. When I go to sleep, I dream of him and I living a happy life together. he is my pride, passion, and joy. If he were to call me "Onii-chan," I would probably get diabetes from him sweetness and die.
                            
                            I wish for nothing but him happiness. If it were for him, I would give my life without any second thoughts. Without him, my life would serve no purpose. I really love davidminh0111.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    <!-- 
        TODO:
        - This is for future purpose (or might be not). If I am being lazy, this will be the one to be use:

            <a href="https://osu.ppy.sh/community/forums/topics/1933154?n=1" style="display: flex; justify-content: center; align-items: center; margin-top: 35rem; font-size: 10rem;">XD</a>
            
        - However, I will still keeping the old implementation working with the (possibly) new implementation as well.
        - In order to do that, an "option" button will be added. This means that it will depends on user's preference to use either of them or not.

        Idea suggested date: 07/07/24
        Starting on implementation: TBD
    -->
    </section>
</div>
