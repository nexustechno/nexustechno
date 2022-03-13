<template>
    <div class="casinotrap-table blue-dark-bg" v-if="!loading && teams.length!=undefined">
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
                <div class="casinocards-container" v-if="casino.casino_name  == '20dt' || casino.casino_name  == 'dt202' || casino.casino_name  == '1daydt'">
                    <span class="text-color-white text-uppercase">Dragon</span>
                    <div class="card_con" id="casinoCarda">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white mt-1 text-uppercase">Tiger</span>
                    <div class="card_con" id="casinoCardb">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
                <div class="casinocards-container" v-if="casino.casino_name  == 'l7a' || casino.casino_name  == 'l7b' || casino.casino_name  == 'aaa' || casino.casino_name  == 'bollywood'">
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
                <div class="casinocards-container" v-if="casino.casino_name  == 'ab1' || casino.casino_name  == 'ab2'">
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
                <div class="casinocards-container" v-if="casino.casino_name  == '32a' || casino.casino_name  == '32b'">
                    <span class="text-color-white text-uppercase">Player 8 <span class="text-color-green font-weight-bold"">({{data.t1[0].C1}})</span></span>
                    <div class="card_con" id="casinoCarda" v-if="cards[0].length > 0">
                        <span v-for="(card,index) in cards[0]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase">Player 9 <span class="text-color-green font-weight-bold">({{data.t1[0].C2}})</span></span>
                    <div class="card_con" id="casinoCardb" v-if="cards[1].length > 0">
                        <span v-for="(card,index) in cards[1]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase">Player 10 <span class="text-color-green font-weight-bold">({{data.t1[0].C3}})</span></span>
                    <div class="card_con" id="casinoCardb" v-if="cards[2].length > 0">
                        <span v-for="(card,index) in cards[2]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                    <span class="text-color-white text-uppercase">Player 11 <span class="text-color-green font-weight-bold">({{data.t1[0].C4}})</span></span>
                    <div class="card_con" id="casinoCardb" v-if="cards[3].length > 0">
                        <span v-for="(card,index) in cards[3]" :key="index" class="text-color-white"><img :src="basepath+'/'+card+'.png'"></span>
                    </div>
                </div>
            </div>
            <div class="casino_time" v-if="autotime!==null">
                <flip-countdown :deadline="autotime" :showDays="false" :showHours="false" :showMinutes="false"></flip-countdown>
            </div>
        </div>
        <div class="casino-videodetails" id="appendData">
            <table id="fullMarketBoard" class="bets">
                <tbody>
                <tr class="bet-all">
                    <td style="width: 60%"></td>
                    <td style="width: 20%" colspan="2"><a id="backAll" class="cyan-bg"><span class="text-uppercase">Back</span></a></td>
                    <td style="width: 20%" colspan="2"><a id="backAll" class="pink-bg"><span class="text-uppercase">Lay</span></a></td>
                </tr>
                <template v-for="(team,index) in teams">
                    <tr :key="team.sid" class="fancy-suspend-tr team1_bm_fancy">
                        <td style="width: 60%"></td>
                        <td style="width: 20%" class="fancy-suspend-td" colspan="2">
                            <div v-if="team.gstatus == 0 || team.gstatus == 'SUSPENDED' || team.gstatus == 'CLOSED'" class="fancy-suspend black-bg-5 text-color-white">
                                <span> <i class="fa fa-lock" aria-hidden="true"></i></span>
                            </div>
                        </td>
                        <td style="width: 20%" class="fancy-suspend-td" colspan="2">
                            <div v-if="lay_enable==false || (team.gstatus == 0 || team.gstatus == 'SUSPENDED' || team.gstatus == 'CLOSED')" class="fancy-suspend black-bg-5 text-color-white"><span><i class="fa fa-lock" aria-hidden="true"></i></span> </div>
                        </td>
                    </tr>
                    <tr style="display: table-row;" :id="'fullSelection_'+team.sid">
                        <th style="width: 60%">
                            <p v-if="team.nation!=undefined"><span class="text-color-red font-weight-bold pr-1" v-if="casino.casino_name  == 'aaa' || casino.casino_name  == 'bollywood'">{{alphabets[index]}}.</span> {{team.nation}}</p>
                            <p v-else-if="team.nat!=undefined"><span class="text-color-red font-weight-bold pr-1" v-if="casino.casino_name  == 'aaa' || casino.casino_name  == 'bollywood'">{{alphabets[index]}}.</span>{{team.nat}}</p>
                            <span :id="team.sid+'-profit'" :class="playerprofit[team.sid]!=undefined && playerprofit[team.sid] > 0 ? 'towin text-color-green' : 'tolose text-color-red'" v-if="playerprofit[team.sid]!=undefined">{{playerprofit[team.sid]}}</span>
                            <span :id="team.sid+'-profit'" class="towin text-color-green" v-else>0</span>
                        </th>
                        <td style="width: 20%" id="back_1" colspan="2" class="cyan-bg suspended">
                            <a onclick="opnForm(this)" :data-team-name="team_name" data-bet-side="back" :data-val="team.b1" v-if="team.nation!=undefined" :data-team-sid="team.sid" :data-team="team.nation">{{team.b1}}<span class="black" v-if="team.bs1!=undefined">{{team.bs1}}</span></a>
                            <a onclick="opnForm(this)" :data-team-name="team_name" data-bet-side="back" :data-val="team.b1" v-else-if="team.nat!=undefined" :data-team-sid="team.sid" :data-team="team.nat">{{team.b1}}<span class="black" v-if="team.bs1!=undefined">{{team.bs1}}</span></a>
                        </td>
                        <td style="width: 20%" id="back_1" colspan="2" class="pink-bg suspended">
                            <a onclick="opnForm(this)" :data-team-name="team_name" data-bet-side="lay" :data-val="team.l1" v-if="team.nation!=undefined" :data-team-sid="team.sid" :data-team="team.nation">{{team.l1}}<span class="black" v-if="team.ls1!=undefined">{{team.ls1}}</span></a>
                            <a onclick="opnForm(this)" :data-team-name="team_name" data-bet-side="lay" :data-val="team.l1" v-else-if="team.nat!=undefined" :data-team-sid="team.sid" :data-team="team.nat">{{team.l1}}<span class="black" v-if="team.ls1!=undefined">{{team.ls1}}</span></a>
                        </td>
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
                <p id="last-result" class="text-right" v-if="casino.casino_name == '20dt' || casino.casino_name == 'dt202' || casino.casino_name == '1daydt'">
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
                        <span style="cursor: pointer;" v-if="result.result== 0" @click="getResult(result.mid)" class="ball-runs last-result playera">T</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == '20poker'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 21" @click="getResult(result.mid)" class="ball-runs last-result playerb">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 11" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == 'ab1' || casino.casino_name  == 'ab2'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playerb">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == 'aaa'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 3" @click="getResult(result.mid)" class="ball-runs last-result playerb">C</span>
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playera">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == 'bollywood'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 6" @click="getResult(result.mid)" class="ball-runs last-result playerb">F</span>
                        <span style="cursor: pointer;" v-if="result.result== 5" @click="getResult(result.mid)" class="ball-runs last-result playerb">E</span>
                        <span style="cursor: pointer;" v-if="result.result== 4" @click="getResult(result.mid)" class="ball-runs last-result playerb">D</span>
                        <span style="cursor: pointer;" v-if="result.result== 3" @click="getResult(result.mid)" class="ball-runs last-result playerb">C</span>
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playera">B</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">A</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == '32a' || casino.casino_name  == '32b'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 4" @click="getResult(result.mid)" class="ball-runs last-result playerb">11</span>
                        <span style="cursor: pointer;" v-if="result.result== 3" @click="getResult(result.mid)" class="ball-runs last-result playerb">10</span>
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playera">9</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">8</span>
                    </template>
                </p>
                <p id="last-result" class="text-right" v-if="casino.casino_name  == '1daydt'">
                    <template v-for="(result,index) in results">
                        <span style="cursor: pointer;" v-if="result.result== 2" @click="getResult(result.mid)" class="ball-runs last-result playera">T</span>
                        <span style="cursor: pointer;" v-if="result.result== 1" @click="getResult(result.mid)" class="ball-runs last-result playera">D</span>
                    </template>
                </p>
            </div>
        </div>
    </div>
    <div class="casinotrap-table blue-dark-bg" v-else-if="loading">
        <div class="betloaderimage1 site_bet_loading1 loader-style1 mt-5">
            <ul class="loading1">
                <li>
                    <img src="/asset/front/img/loaderajaxbet.gif">
                </li>
                <li>Loading...</li>
            </ul>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import FlipCountdown from 'vue2-flip-countdown'

    export default {
        components: { FlipCountdown },
        data() {
            return {
                alphabets:['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
                data: {},
                results: {},
                roundId: 0,
                fullRoundId: 0,
                cards: [],
                teams: [],
                team_name:'',
                autotime:null,
                lay_enable:false,
                loading:true,
            }
        },
        props: ['casino', 'basepath', 'playerprofit','today'],
        mounted() {
            this.autotime = this.today;
            window.Echo.channel('casino-detail').listen('.' + this.casino.casino_name, (data) => {
                // console.log("data: ",data);

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

                    var playerA = this.data.t2[0];
                    playerA.b1 = playerA.rate;
                    playerA.bs1 = 0;
                    playerA.l1 = 0;
                    playerA.ls1 = 0;

                    var playerB = this.data.t2[2];
                    playerB.b1 = playerB.rate;
                    playerB.bs1 = 0;
                    playerB.l1 = 0;
                    playerB.ls1 = 0;

                    this.teams.push(playerA);
                    this.teams.push(playerB);

                }
                else if (this.casino.casino_name == '20dt' || this.casino.casino_name == 'dt202'){

                    var playerA = this.data.t2[0];
                    playerA.b1 = playerA.rate;
                    playerA.bs1 = 0;
                    playerA.l1 = 0;
                    playerA.ls1 = 0;

                    var playerB = this.data.t2[1];
                    playerB.b1 = playerB.rate;
                    playerB.bs1 = 0;
                    playerB.l1 = 0;
                    playerB.ls1 = 0;

                    var playerC = this.data.t2[2];
                    playerC.b1 = playerC.rate;
                    playerC.bs1 = 0;
                    playerC.l1 = 0;
                    playerC.ls1 = 0;

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                    this.teams.push(playerC);
                }
                else if (this.casino.casino_name == 'l7a' || this.casino.casino_name == 'l7b'){

                    var playerA = this.data.t2[0];
                    playerA.b1 = playerA.rate;
                    playerA.bs1 = 0;
                    playerA.l1 = 0;
                    playerA.ls1 = 0;

                    var playerB = this.data.t2[1];
                    playerB.b1 = playerB.rate;
                    playerB.bs1 = 0;
                    playerB.l1 = 0;
                    playerB.ls1 = 0;

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                }
                else if (this.casino.casino_name == '20poker'){

                    var playerA = this.data.t2[0];
                    playerA.nation = "Player A";
                    playerA.b1 = playerA.rate;
                    playerA.bs1 = 0;
                    playerA.l1 = 0;
                    playerA.ls1 = 0;

                    var playerB = this.data.t2[1];
                    playerB.nation = "Player B";
                    playerB.b1 = playerB.rate;
                    playerB.bs1 = 0;
                    playerB.l1 = 0;
                    playerB.ls1 = 0;

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                }
                else if (this.casino.casino_name == 'ab1' || this.casino.casino_name == 'ab2'){

                    var playerA = this.data.t2[0];
                    playerA.nation = "Player A";
                    playerA.b1 = "2.00";
                    playerA.bs1 = 0;
                    playerA.l1 = "2.00";
                    playerA.ls1 = 0;

                    var playerB = this.data.t2[3];
                    playerB.nation = "Player B";
                    playerB.b1 = "2.00";
                    playerB.bs1 = 0;
                    playerB.l1 = "2.00";
                    playerB.ls1 = 0;

                    this.teams.push(playerA);
                    this.teams.push(playerB);
                }
                else if (this.casino.casino_name == 'odtp'){
                    this.teams.push(this.data.bf[0]);
                    this.teams.push(this.data.bf[1]);
                }
                else if (this.casino.casino_name == 'aaa'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                    this.teams.push(this.data.t2[2]);
                    this.lay_enable = true;
                }
                else if (this.casino.casino_name == 'bollywood'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                    this.teams.push(this.data.t2[2]);
                    this.teams.push(this.data.t2[3]);
                    this.teams.push(this.data.t2[4]);
                    this.teams.push(this.data.t2[5]);
                    this.lay_enable = true;
                }
                else if (this.casino.casino_name == '32a' || this.casino.casino_name == '32b'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                    this.teams.push(this.data.t2[2]);
                    this.teams.push(this.data.t2[3]);
                    this.lay_enable = true;
                }
                else if (this.casino.casino_name == '1daydt'){
                    this.teams.push(this.data.t2[0]);
                    this.teams.push(this.data.t2[1]);
                    this.lay_enable = true;
                }

                var team_name = '';
                for(var t=0;t<this.teams.length;t++){
                    team_name += this.teams[t].sid+",";
                }

                this.team_name = team_name;

                if (this.casino.casino_name == 'teen20' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined && this.data.t1[0].C3 != undefined && this.data.t1[0].C4 != undefined && this.data.t1[0].C5 != undefined && this.data.t1[0].C6 != undefined) {
                    this.cards.push([this.data.t1[0].C1, this.data.t1[0].C2, this.data.t1[0].C3])
                    this.cards.push([this.data.t1[0].C4, this.data.t1[0].C5, this.data.t1[0].C6])
                }
                else if ((this.casino.casino_name == '20dt' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined) || (this.casino.casino_name == 'dt202' && this.data.t1[0].C1 != undefined && this.data.t1[0].C2 != undefined) || (this.casino.casino_name == '1daydt' && this.data.t1[0].C1 != undefined)){
                    this.cards.push([this.data.t1[0].C1])
                    this.cards.push([this.data.t1[0].C2])
                }
                else if ((this.casino.casino_name == 'l7a' && this.data.t1[0].C1 != undefined) || (this.casino.casino_name == 'l7b' && this.data.t1[0].C1 != undefined) || (this.casino.casino_name == 'aaa' && this.data.t1[0].C1 != undefined) || (this.casino.casino_name == 'bollywood' && this.data.t1[0].C1 != undefined)){
                    this.cards.push([this.data.t1[0].C1])
                }
                else if (this.casino.casino_name == '20poker'){
                    this.cards.push([this.data.t1[0].C1,this.data.t1[0].C2])
                    this.cards.push([this.data.t1[0].C3,this.data.t1[0].C4])

                    this.cards.push([this.data.t1[0].C5,this.data.t1[0].C6,this.data.t1[0].C7,this.data.t1[0].C8,this.data.t1[0].C9])
                }
                else if (this.casino.casino_name == 'ab1' || this.casino.casino_name == 'ab2'){
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
                else if (this.casino.casino_name == '32a' || this.casino.casino_name == '32b'){
                    var playersCardsString = this.data.t1[0].desc;
                    var playersCards = playersCardsString.split(",");
                    var playerCardAArray = [];
                    var playerCardBArray = [];
                    var playerCardCArray = [];
                    var playerCardDArray = [];
                    var j = 1;
                    for(var x=0;x<playersCards.length;x++){
                        if(playersCards[x]!=1){
                            if(j == 1) {
                                playerCardAArray.push(playersCards[x]);
                            }else if(j == 2) {
                                playerCardBArray.push(playersCards[x]);
                            }else if(j == 3) {
                                playerCardCArray.push(playersCards[x]);
                            }else if(j == 4) {
                                playerCardDArray.push(playersCards[x]);
                            }
                        }
                        if(j == 4){
                            j=1;
                        }else {
                            j++;
                        }
                    }
                    this.cards.push(playerCardAArray);
                    this.cards.push(playerCardBArray);
                    this.cards.push(playerCardCArray);
                    this.cards.push(playerCardDArray);
                }

                if (this.teams.length > 0 &&  (this.teams[0].gstatus == 0 || this.teams[0].gstatus == 'SUSPENDED' || this.teams[0].gstatus == 'CLOSED')) {
                    $(".showForm").hide();
                }

                if ($(".casino-bet-item").length > 0) {
                    if(this.resultReceivedCurrentRoundId(this.fullRoundId)) {
                        this.declareResult(this.fullRoundId);
                    }
                }

                this.fullRoundId = mid;

                this.timerteen20(this.data.t1[0].autotime);

                this.loading = false;

            });
        },
        methods: {
            resultReceivedCurrentRoundId(roundId){
                var resultValue = false;
                if(this.results.length > 0){
                    for (var i=0;i<this.results.length;i++){
                        if(this.results[i].mid == roundId){
                            resultValue = true;
                            break;
                        }
                    }
                }

                return resultValue;
            },
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
            formatTime(time) {
                // const minutes = Math.floor(time / 60);
                let seconds = time % 60;
                if (seconds < 0) {
                    seconds = `0`;
                }
                return `${seconds}`;
            },
            timerteen20(val) {
                var date = new Date();

                console.log("autotime ",this.formatTime(val))

                date.setSeconds( date.getSeconds() + this.formatTime(val) );

                var ye2 = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
                var mo2 = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(date);
                var da2 = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                var h = new Intl.DateTimeFormat('en', { hour: 'numeric',hour12: false }).format(date);
                var m = new Intl.DateTimeFormat('en', { minute: '2-digit' }).format(date);
                var s = new Intl.DateTimeFormat('en', { second: 'numeric' }).format(date);
                var dateime = `${ye2}-${mo2}-${da2} ${h}:${m}:${s}`;
                // var dateime = `${ye2}-${mo2}-${da2} 24:00:00`;

                console.log("dateime: ",dateime," == ",this.formatTime(val))

                setTimeout(()=>{
                    $(".flip-clock__piece .flip-clock__slot").css('display','none');
                },100);

                // this.autotime = dateime;
            }
        }
    }
</script>


<style>
    .flip-clock__piece{
        display: none;
    }
</style>
