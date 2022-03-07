<template>
    <div class="casinotrap-table blue-dark-bg" v-if="data.t1!=undefined">
        <div id="gameHead" class="game-head">
            <div class="game-team">
                <div class="game-name">
                    <span>{{ casino.casino_title }}</span>
                    <span class="rules_underline" data-toggle="modal" data-target="#exampleModal2">Rules</span>
                    <span class="float-right round"> Round ID: <span class="roundId" :data-status="data.t2[0].gstatus" :data-round-id="fullRoundId">{{roundId}}</span> | Min: <span>{{casino.min_casino}}</span> | Max: <span>{{casino.max_casino}}</span></span>
                </div>
            </div>
        </div>

        <div class="casino-video">
            <div class="video-block">
                <iframe :src="casino.casino_link" title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen allowtransparency="yes" scrolling="no"
                        marginwidth="0" marginheight="0"></iframe>
            </div>
            <div class="casinocards">
                <div class="casinocards-container" v-if="casino.casino_name == 'teen20'">
                    <span class="text-color-white">PLAYER A</span>
                    <div class="card_con" id="casinoCarda">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white mt-1">PLAYER B</span>
                    <div class="card_con" id="casinoCardb">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
                <div class="casinocards-container" v-if="casino.casino_name  == '20dt' || casino.casino_name  == 'dt202'">
                    <span class="text-color-white text-uppercase">Dragon</span>
                    <div class="card_con" id="casinoCarda">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white mt-1 text-uppercase">Tiger</span>
                    <div class="card_con" id="casinoCardb">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
                <div class="casinocards-container" v-if="casino.casino_name  == 'l7a' || casino.casino_name  == 'l7b'">
                    <span class="text-color-white text-uppercase">Card</span>
                    <div class="card_con" id="casinoCarda">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
                <div class="casinocards-container" v-if="casino.casino_name  == '20poker'">
                    <span class="text-color-white text-uppercase">Player A</span>
                    <div class="card_con" id="casinoCarda">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase">Player B</span>
                    <div class="card_con" id="casinoCardb">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase">Card</span>
                    <div class="card_con" id="casinoCardc">
                        <span v-for="(card,index) in cards[2]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
                <div class="casinocards-container" v-if="casino.casino_name  == 'ab2'">
                    <span class="text-color-white text-uppercase"></span>
                    <div class="card_con" id="casinoCardc">
                        <span v-for="(card,index) in cards[2]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase" v-if="cards[0].length > 0">Player A</span>
                    <div class="card_con" id="casinoCarda" v-if="cards[0].length > 0">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase" v-if="cards[1].length > 0">Player B</span>
                    <div class="card_con" id="casinoCardb" v-if="cards[1].length > 0">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
            </div>
            <div class="casino_time">
                <div id="timer">
                    <div class="base-timer">
                        <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <g class="base-timer__circle">
                                <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                                <path id="base-timer-path-remaining"
                                      stroke-dasharray="283"
                                      class="base-timer__path-remaining text-color-green-1"
                                      d="
                          M 50, 50
                          m -45, 0
                          a 45,45 0 1,0 90,0
                          a 45,45 0 1,0 -90,0
                        "
                                ></path>
                            </g>
                        </svg>
                        <span id="base-timer-label" class="base-timer__label text-color-green-1">0</span></div>
                </div>
            </div>
        </div>
        <div class="casino-videodetails" id="appendData">
            <table id="fullMarketBoard" class="bets">
                <tbody>
                <tr class="bet-all">
                    <td style="width: 60%"></td>
                    <td style="width: 20%" colspan="2"><a id="backAll" class="back-allcasino"><span>Back</span></a></td>
                    <td style="width: 20%" colspan="2"><a id="backAll" class="back-allcasino"><span>Lay</span></a></td>
                </tr>
                <template v-for="(team,index) in teams">
                    <tr :key="team.sid" class="fancy-suspend-tr team1_bm_fancy">
                        <td style="width: 60%"></td>
                        <td style="width: 20%" class="fancy-suspend-td" colspan="2">
                            <div v-if="team.gstatus == 0" class="fancy-suspend black-bg-5 text-color-white">
                                <span> <i class="fa fa-lock" aria-hidden="true"></i></span>
                            </div>
                        </td>
                        <td style="width: 20%" class="fancy-suspend-td" colspan="2">
                            <div class="fancy-suspend black-bg-5 text-color-white"><span><i class="fa fa-lock" aria-hidden="true"></i></span> </div>
                        </td>
                    </tr>
                    <tr style="display: table-row;" :id="'fullSelection_'+team.sid">
                        <th style="width: 60%">
                            <p v-if="team.nation!=undefined">{{team.nation}}</p>
                            <p v-else-if="team.nat!=undefined">{{team.nat}}</p>
                            <span :id="team.sid+'-profit'" class="towin text-color-green" v-if="playerprofit[team.sid]!=undefined">{{playerprofit[team.sid]}}</span>
                            <span :id="team.sid+'-profit'" class="towin text-color-green" v-else>0</span>
                        </th>
                        <td style="width: 20%" id="back_1" colspan="2" class="back-1 suspended">
                            <a onclick="opnForm(this)" :data-val="team.rate" v-if="team.nation!=undefined" :data-team-sid="team.sid" :data-team="team.nation">{{team.rate}}<span class="black">0</span></a>
                            <a onclick="opnForm(this)" :data-val="team.rate" v-else-if="team.nat!=undefined" :data-team-sid="team.sid" :data-team="team.nat">{{team.rate}}<span class="black">0</span></a>
                        </td>
                        <td style="width: 20%" id="back_1" colspan="2" class="back-1 suspended"><a>0.00<span class="black">0</span></a></td>
                    </tr>
                    <tr class="collapse mobile-casino-bet-tr" :id="'mobile-casino-bet-tr-'+team.sid">
                        <td colspan="5" class="casino_right_side" :id="'mobile-casino-bet-td-'+team.sid"></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
        <div class="mobile_res_data">

            <div id="gameHead" class="game-head">
                <div class="game-team">
                    <div class="game-name">Last Result</div>
                </div>
            </div>
            <div class="mb-10">
                <p id="last-result" class="text-right" v-if="casino.casino_name == 'teen20'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 3" @click="getResult(result.mid)" class="ball-runs last-result playerb">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name == '20dt' || casino.casino_name == 'dt202'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 3" @click="getResult(result.mid)" class="ball-runs last-result playerb">TIE</span>
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playerb">T</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">D</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == 'l7a' || casino.casino_name  == 'l7b'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playerb">H</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">L</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == '20poker'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 21" @click="getResult(result.mid)" class="ball-runs last-result playerb">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 11" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == 'ab2'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playerb">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
            </div>

            <div class="casino_rules_table mt-2">
                <div class="casinolay_bettitle black-bg-2 text-color-white">
                    <span>Rules</span>
                </div>
                <div class="table-responsive">

                    <table class="table table-bordered rules-table"
                           style="background-color: white;">
                        <tbody>
                        <tr class="text-center">
                            <th colspan="2">Pair Plus</th>
                        </tr>
                        <tr>
                            <td width="60%">Pair (Double)</td>
                            <td>1 To 1</td>
                        </tr>
                        <tr>
                            <td width="60%">Flush (Color)</td>
                            <td>1 To 4</td>
                        </tr>
                        <tr>
                            <td width="60%">Straight (Rown)</td>
                            <td>1 To 6</td>
                        </tr>
                        <tr>
                            <td width="60%">Trio (Teen)</td>
                            <td>1 To 35</td>
                        </tr>
                        <tr>
                            <td width="60%">Straight Flush (Pakki Rown)</td>
                            <td>1 To 45</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                data: {},
                results: {},
                roundId: 0,
                fullRoundId: 0,
                cards: [],
                teams: []
            }
        },
        props: ['casino', 'basepath', 'playerprofit'],
        mounted() {
            window.Echo.channel('casino-detail').listen('.' + this.casino.casino_name, (data) => {

                this.teams = [];

                this.cards = [];
                var playerACards = '';
                var playerBCards = '';
                this.data = data.data;
                this.results = data.results;
                var mid = this.data.t1[0].mid;
                var explode = mid.split(".");
                this.roundId = explode[1];

                if (this.casino.casino_name == 'teen20') {
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[2]);
                }else if (this.casino.casino_name == '20dt' || this.casino.casino_name == 'dt202'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                    this.teams.push(this.data.t2[2]);
                }else if (this.casino.casino_name == 'l7a' || this.casino.casino_name == 'l7b'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                }else if (this.casino.casino_name == '20poker'){

                    var playerA = this.data.t2[0];
                    playerA.nation = "Player A";

                    var playerB = this.data.t2[1];
                    playerB.nation = "Player B";

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                }else if (this.casino.casino_name == 'ab2'){

                    var playerA = this.data.t2[0];
                    playerA.nation = "Player A";
                    playerA.rate = playerA.b1;

                    var playerB = this.data.t2[3];
                    playerB.nation = "Player B";
                    playerB.rate = playerB.b1;

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                }


                if (this.casino.casino_name == 'teen20' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined && this.data.t1[0].C3 != undefined && this.data.t1[0].C4 != undefined && this.data.t1[0].C5 != undefined && this.data.t1[0].C6 != undefined) {
                    this.cards.push([this.data.t1[0].C1, this.data.t1[0].C2, this.data.t1[0].C3])
                    this.cards.push([this.data.t1[0].C4, this.data.t1[0].C5, this.data.t1[0].C6])
                }else if ((this.casino.casino_name == '20dt' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined) || (this.casino.casino_name == 'dt202' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined)){
                    this.cards.push([this.data.t1[0].C1])
                    this.cards.push([this.data.t1[0].C2])
                }else if ((this.casino.casino_name == 'l7a' && this.data.t1[0].C1 != undefined) || (this.casino.casino_name == 'l7b' && this.data.t1[0].C1 != undefined)){
                    this.cards.push([this.data.t1[0].C1])
                }
                else if (this.casino.casino_name == '20poker'){
                    this.cards.push([this.data.t1[0].C1,this.data.t1[0].C2])
                    this.cards.push([this.data.t1[0].C3,this.data.t1[0].C4])

                    this.cards.push([this.data.t1[0].C5,this.data.t1[0].C6,this.data.t1[0].C7,this.data.t1[0].C8,this.data.t1[0].C9])
                }else if (this.casino.casino_name == 'ab2'){
                    var playersCardsString = this.data.t1[0].Cards;
                    var playersCards = playersCardsString.split(",");
                    var playerCardAArray = [];
                    var playerCardBArray = [];
                    for(var x=1;x<playersCards.length;x++){
                        if(playersCards[x]!=1){
                            if (x === 0 || x % 2 === 0) {
                                playerCardAArray.push(playersCards[x]);
                            }
                            else {
                                playerCardBArray.push(playersCards[x]);
                            }
                        }
                    }
                    this.cards.push(playerCardAArray);
                    this.cards.push(playerCardBArray);
                    this.cards.push([playersCards[0]]);
                }

                if (this.data.t2[0].gstatus == 0) {
                    $(".showForm").hide();
                }

                if ($(".casino-bet-item").length > 0 && mid == 0) {
                    this.declareResult(this.fullRoundId);
                }

                this.fullRoundId = mid;

                this.timerteen20(this.data.t1[0].autotime);

            });
        },
        methods: {
            getResult(fullRoundId) {
                axios.get('/casino/winner/' + fullRoundId + '/cards/' + this.casino.casino_name)
                    .then((res) => {
                        if (res.data.html != undefined) {
                            $("#card-result-dialog .result-cards-html-section").html(res.data.html);
                            $("#card-result-dialog").modal('show');
                        }
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                    }).finally(() => {
                    //Perform action in always
                });
            },
            declareResult(fullRoundId) {
                if(fullRoundId <= 0){
                    return false;
                }
                var form = {
                    roundid: fullRoundId,
                    cards: this.cards,
                    casino_name: this.casino.casino_name,
                    result: JSON.stringify(this.results)
                }
                axios.post('/declare/casino_bet/winner', form)
                    .then((res) => {
                        //Perform Success Action
                        toastr.success(res.data.message);
                        console.log(res);
                        // if(res.data.betHtml != '') {
                        $("#bet-list-section").html(res.data.betHtml);
                        for (var i=0;i<this.teams.length;i++) {
                            $("#"+this.teams[i].sid+"-profit").html(0);
                        }

                        // }
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                    }).finally(() => {
                    //Perform action in always
                });
            },
            timerteen20(val) {
                const FULL_DASH_ARRAY = 283;
                const WARNING_THRESHOLD = 10;
                const ALERT_THRESHOLD = 5;
                const COLOR_CODES = {
                    info: {
                        color: "green"
                    },
                    warning: {
                        color: "orange",
                        threshold: WARNING_THRESHOLD
                    },
                    alert: {
                        color: "red",
                        threshold: ALERT_THRESHOLD
                    }
                };
                const TIME_LIMIT = val;
                let timePassed = 0;
                let timeLeft = TIME_LIMIT;
                let timerInterval = null;
                let remainingPathColor = COLOR_CODES.info.color;
                var innerHTML = ``;

                $("#timer #base-timer-label").html(formatTime(timeLeft));
                $("#timer .base-timer__path-remaining").addClass(remainingPathColor);

                // startTimer();

                // function onTimesUp() {
                //     clearInterval(timerInterval);
                // }
                //
                // function startTimer() {
                //     timerInterval = setInterval(() => {
                //         timePassed = timePassed += 1;
                //         timeLeft = TIME_LIMIT - timePassed;
                //         $("#base-timer-label").html(formatTime(timeLeft));
                //         setCircleDasharray();
                //         setRemainingPathColor(timeLeft);
                //
                //         if (timeLeft === 0) {
                //             onTimesUp();
                //         }
                //     }, 1000);
                // }

                function formatTime(time) {
                    const minutes = Math.floor(time / 60);
                    let seconds = time % 60;
                    if (seconds < 0) {
                        seconds = `0`;
                    }
                    return `${seconds}`;
                }

                function setRemainingPathColor(timeLeft) {
                    const {
                        alert,
                        warning,
                        info
                    } = COLOR_CODES;
                    if (timeLeft <= alert.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(warning.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(alert.color);
                    } else if (timeLeft <= warning.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(info.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(warning.color);
                    }
                }

                function calculateTimeFraction() {
                    const rawTimeFraction = timeLeft / TIME_LIMIT;
                    return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
                }

                function setCircleDasharray() {
                    const circleDasharray = `${(
                        calculateTimeFraction() * FULL_DASH_ARRAY
                    ).toFixed(0)} 283`;
                    document
                        .getElementById("base-timer-path-remaining")
                        .setAttribute("stroke-dasharray", circleDasharray);
                }
            }
        }
    }
</script>
