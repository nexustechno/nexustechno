<template>
    <div class="inplay-tableblock" id="inplay-tableblock" v-if="match.t1!=undefined">
        <table class="table custom-table inplay-table w1-table">
            <tbody>
            <tr class="betstr">
                <td class="text-color-grey opacity-1">
                    <span class="totselection seldisplay">{{ selections }} Selections</span>
                    <div class="minmax-txt minmaxmobile">
                        <span>Min</span>
                        <span id="div_min_bet_odds_limit" class="oddMin">{{ min_bet_odds_limit }}</span>
                        <span>Max</span>
                        <span id="div_max_bet_odds_limit" class="oddMax">{{ max_bet_odds_limit }}</span>
                    </div>
                </td>
                <td colspan="2">101.7%</td>
                <td>
                    <a class="backall"> <img :src="bluebg1" style="width: 100%; height: 25px;"> <span>Back</span></a>
                </td>
                <td>
                    <a class="layall"><img :src="pinkbg1" style="width: 100%; height: 25px;"> <span>Lay</span></a>
                </td>
                <td colspan="2">97.9%</td>
            </tr> <!-- DONE -->
            <tr id="team1" class="white-bg tr_team1">
                <td>
                    <img :src="bar_image"> <b class="team1">{{ match.t1[0][0].nat }}</b>
                    <div>
                        <span id="team1_bet_count_old" :class="betTotalValue.team1_bet_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="team1_total">{{betTotalValue.team1_bet_total}}</span>)</span>
                        <span id="team1_bet_count_new" class="towin text-color-green" style="display: none;">0.00</span>
                    </div>
                </td>
                <td data-team="team1" class="light-blue-bg-2 opnForm ODDSBack td_team1_back_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" :data-val="match.t1[0][0].b3" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][0].b3 }} <br><span>{{ nFormatter(match.t1[0][0].bs3,1) }}</span></a>
                </td>
                <td data-team="team1" class="link(target, link)ght-blue-bg-3 ODDSBack td_team1_back_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" data-cls="cyan-bg" :data-val="match.t1[0][0].b2"
                       class="back1btn text-color-black"> {{ match.t1[0][0].b2 }}<br><span>{{ nFormatter(match.t1[0][0].bs2,1) }}</span></a>
                </td>
                <td data-team="team1" class="cyan-bg ODDSBack td_team1_back_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" :data-val="match.t1[0][0].b1" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][0].b1 }} <br><span>{{ nFormatter(match.t1[0][0].bs1,1) }}</span></a>
                </td>
                <td data-team="team1" class="pink-bg ODDSLay td_team1_lay_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" :data-val="match.t1[0][0].l1" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][0].l1 }} <br><span>{{ nFormatter(match.t1[0][0].l1,1) }}</span></a>
                </td>
                <td data-team="team1" class="light-pink-bg-2 ODDSLay td_team1_lay_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" :data-val="match.t1[0][0].l2" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][0].l2 }} <br><span>{{ nFormatter(match.t1[0][0].l2,1) }}</span></a>
                </td>
                <td data-team="team1" class="light-pink-bg-3 ODDSLay td_team1_lay_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team1" :data-val="match.t1[0][0].l3" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][0].l3 }} <br><span>{{ nFormatter(match.t1[0][0].l3,1) }}</span></a>
                </td>
            </tr>
            <tr id="mobile_tr" class="mobileBack tr_team1 mobile_bet_model_div">
                <td colspan="7" class="mobile_tr_common_class tr_team1_td_mobile"></td>
            </tr>
            <tr class="white-bg tr_team2">
                <td>
                    <img :src="bar_image"> <b class="team2">{{ match.t1[0][1].nat }}</b>
                    <div>
                        <span id="team2_bet_count_old" :class="betTotalValue.team2_bet_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="team2_total">{{betTotalValue.team2_bet_total}}</span>)</span>
                        <span id="team2_bet_count_new" class="towin text-color-green" style="display: none;">0.00</span>
                    </div>
                </td>

                <td data-team="team2" class="light-blue-bg-2 opnForm ODDSBack td_team2_back_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" :data-val="match.t1[0][1].b3" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][1].b3 }} <br><span>{{ nFormatter(match.t1[0][1].bs3,1) }}</span></a>
                </td>
                <td data-team="team2" class="link(target, link)ght-blue-bg-3 ODDSBack td_team2_back_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" data-cls="cyan-bg" :data-val="match.t1[0][1].b2"
                       class="back1btn text-color-black"> {{ match.t1[0][1].b2 }}<br><span>{{ nFormatter(match.t1[0][1].bs2,1) }}</span></a>
                </td>
                <td data-team="team2" class="cyan-bg ODDSBack td_team2_back_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" :data-val="match.t1[0][1].b1" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][1].b1 }} <br><span>{{ nFormatter(match.t1[0][1].bs1,1) }}</span></a>
                </td>
                <td data-team="team2" class="pink-bg ODDSLay td_team2_lay_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" :data-val="match.t1[0][1].l1" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][1].l1 }} <br><span>{{ nFormatter(match.t1[0][1].l1,1) }}</span></a>
                </td>
                <td data-team="team2" class="light-pink-bg-2 ODDSLay td_team2_lay_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" :data-val="match.t1[0][1].l2" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][1].l2 }} <br><span>{{ nFormatter(match.t1[0][1].l2,1) }}</span></a>
                </td>
                <td data-team="team2" class="light-pink-bg-3 ODDSLay td_team2_lay_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team2" :data-val="match.t1[0][1].l3" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][1].l3 }} <br><span>{{ nFormatter(match.t1[0][1].l3,1) }}</span></a>
                </td>
            </tr>
            <tr id="mobile_tr" class="mobileBack tr_team2 mobile_bet_model_div">
                <td colspan="7" class="mobile_tr_common_class tr_team2_td_mobile"></td>
            </tr>
            <tr v-if="match.t1[0][2]!=undefined" class="white-bg tr_team3 22222">
                <td>
                    <img :src="bar_image"> <b class="team3">{{ match.t1[0][2].nat }}</b>
                    <div>
                        <span id="draw_bet_count_old" :class="betTotalValue.team_draw_bet_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="draw_total">{{betTotalValue.team_draw_bet_total}}</span>)</span>
                        <span id="draw_bet_count_new" class="tolose text-color-red" style="display: none;">0.00</span>
                    </div>
                </td>

                <td data-team="team3" class="light-blue-bg-2 opnForm ODDSBack td_team3_back_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" :data-val="match.t1[0][2].b3" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][2].b3 }} <br><span>{{ nFormatter(match.t1[0][2].bs3,1) }}</span></a>
                </td>
                <td data-team="team3" class="link(target, link)ght-blue-bg-3 ODDSBack td_team3_back_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" data-cls="cyan-bg" :data-val="match.t1[0][1].b2"
                       class="back1btn text-color-black"> {{ match.t1[0][2].b2 }}<br><span>{{ nFormatter(match.t1[0][2].bs2,1) }}</span></a>
                </td>
                <td data-team="team3" class="cyan-bg ODDSBack td_team3_back_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" :data-val="match.t1[0][2].b1" data-cls="cyan-bg"
                       class="back1btn text-color-black"> {{ match.t1[0][2].b1 }} <br><span>{{ nFormatter(match.t1[0][2].bs1,1) }}</span></a>
                </td>
                <td data-team="team3" class="pink-bg ODDSLay td_team3_lay_0">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" :data-val="match.t1[0][2].l1" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][2].l1 }} <br><span>{{ nFormatter(match.t1[0][2].l1,1) }}</span></a>
                </td>
                <td data-team="team3" class="light-pink-bg-2 ODDSLay td_team3_lay_1">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" :data-val="match.t1[0][2].l2" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][2].l2 }} <br><span>{{ nFormatter(match.t1[0][2].l2,1) }}</span></a>
                </td>
                <td data-team="team3" class="light-pink-bg-3 ODDSLay td_team3_lay_2">
                    <a data-bettype="ODDS" onclick="opnForm(this)" data-team="team3" :data-val="match.t1[0][2].l3" data-cls="pink-bg"
                       class="lay1btn text-color-black"> {{ match.t1[0][2].l3 }} <br><span>{{ nFormatter(match.t1[0][2].l3,1) }}</span></a>
                </td>
            </tr>
            <tr v-if="match.t1[0][2]!=undefined" id="mobile_tr" class="mobileBack tr_team3 mobile_bet_model_div">
                <td colspan="7" class="mobile_tr_common_class tr_team3_td_mobile"></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="inplay-tableblock" id="inplay-tableblock" v-else>
        <table class="table custom-table inplay-table w1-table" >
            <tr>
                <td colspan="7">
                    <p class="text-center font-weight-bold">Loading ODDS Data...</p>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['event_id', 'bar_image', 'min_bet_odds_limit', 'max_bet_odds_limit', 'pinkbg1','pinbg','pinbg1', 'bluebg1','min_bookmaker_limit','max_bookmaker_limit','min_bet_fancy_limit','max_bet_fancy_limit','bet_total'],
        data() {
            return {
                match: [],
                selections:0,
            };
        },
        computed:{
            betTotalValue(){
                return JSON.parse(this.bet_total)
            }
        },
        mounted() {
            console.log(JSON.parse(this.bet_total))
            window.Echo.channel('match-detail').listen('.' + this.event_id, (data) => {

                this.selections = 0;

                var newRecords = data.records;

                if(this.match.t1!=undefined){

                    //team1 spark changes
                    if((this.match.t1[0][0].b3!=newRecords.t1[0][0].b3)){
                        $(".td_team1_back_2").addClass('spark');
                    }
                    if((this.match.t1[0][0].b2!=newRecords.t1[0][0].b2)){
                        $(".td_team1_back_1").addClass('spark');
                    }
                    if((this.match.t1[0][0].b1!=newRecords.t1[0][0].b1)){
                        $(".td_team1_back_0").addClass('spark');
                    }

                    if((this.match.t1[0][0].l3!=newRecords.t1[0][0].l3)){
                        $(".td_team1_lay_2").addClass('sparkLay');
                    }
                    if((this.match.t1[0][0].l2!=newRecords.t1[0][0].l2)){
                        $(".td_team1_lay_1").addClass('sparkLay');
                    }
                    if((this.match.t1[0][0].l1!=newRecords.t1[0][0].l1)){
                        $(".td_team1_lay_0").addClass('sparkLay');
                    }

                    //team2 spark changes
                    if((this.match.t1[0][1].b3!=newRecords.t1[0][1].b3)){
                        $(".td_team2_back_2").addClass('spark');
                    }
                    if((this.match.t1[0][1].b2!=newRecords.t1[0][1].b2)){
                        $(".td_team2_back_1").addClass('spark');
                    }
                    if((this.match.t1[0][1].b1!=newRecords.t1[0][1].b1)){
                        $(".td_team2_back_0").addClass('spark');
                    }

                    if((this.match.t1[0][1].l3!=newRecords.t1[0][1].l3)){
                        $(".td_team2_lay_2").addClass('sparkLay');
                    }
                    if((this.match.t1[0][1].l2!=newRecords.t1[0][1].l2)){
                        $(".td_team2_lay_1").addClass('sparkLay');
                    }
                    if((this.match.t1[0][1].l1!=newRecords.t1[0][1].l1)){
                        $(".td_team2_lay_0").addClass('sparkLay');
                    }

                    if(this.match.t1[0][2]!=undefined){
                        //team3 spark changes
                        if((this.match.t1[0][2].b3!=newRecords.t1[0][2].b3)){
                            $(".td_team3_back_2").addClass('spark');
                        }
                        if((this.match.t1[0][2].b2!=newRecords.t1[0][2].b2)){
                            $(".td_team3_back_1").addClass('spark');
                        }
                        if((this.match.t1[0][2].b1!=newRecords.t1[0][2].b1)){
                            $(".td_team3_back_0").addClass('spark');
                        }

                        if((this.match.t1[0][2].l3!=newRecords.t1[0][2].l3)){
                            $(".td_team3_lay_2").addClass('sparkLay');
                        }
                        if((this.match.t1[0][2].l2!=newRecords.t1[0][2].l2)){
                            $(".td_team3_lay_1").addClass('sparkLay');
                        }
                        if((this.match.t1[0][2].l1!=newRecords.t1[0][2].l1)){
                            $(".td_team3_lay_0").addClass('sparkLay');
                        }
                    }
                }
                
                this.match = newRecords;

                if(this.match.t1[0][0]!=undefined){
                    this.selections++;
                }
                if(this.match.t1[0][1]!=undefined){
                    this.selections++;
                }
                if(this.match.t1[0][2]!=undefined){
                    this.selections++;
                }
                // console.log("match ",data.records)
            });
        },
        methods: {
            nFormatter(num, digits) {
                var si = [
                    {value: 1, symbol: ""},
                    {value: 1E3, symbol: "k"},
                    {value: 1E6, symbol: "M"},
                    {value: 1E9, symbol: "G"},
                    {value: 1E12, symbol: "T"},
                    {value: 1E15, symbol: "P"},
                    {value: 1E18, symbol: "E"}
                ];
                var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
                var i;
                for (i = si.length - 1; i > 0; i--) {
                    if (num >= si[i].value) {
                        break;
                    }
                }
                return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
            }
        }
    }
</script>
