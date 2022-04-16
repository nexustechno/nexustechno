<template>
    <div class="inplay-tableblock" id="inplay-tableblock" v-if="teams.length > 0">
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
            <template v-for="(team,index) in teams">
                <tr v-if="team.status == 'CLOSED' || team.status == 'SUSPENDED' || team.status == 'SUSPEND'" class="fancy-suspend-tr 222222" :class="'team'+(index+1)+'_fancy'"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>CLOSED</span></div></td></tr>
                <tr :key="team.sId" :id="'team'+(index+1)" :class="'tr_team'+(index+1)" class="white-bg">
                    <td>
                        <img :src="bar_image"> <b :class="'team'+(index+1)">{{ team.nat }}</b>
                        <div v-if="index < 2">
                            <span :id="'team'+(index+1)+'_bet_count_old'" :class="betTotalValue['team'+(index+1)+'_bet_total'] < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span :id="'team'+(index+1)+'_total'">{{betTotalValue['team'+(index+1)+'_bet_total']}}</span>)</span>
                            <span :id="'team'+(index+1)+'_bet_count_new'" class="towin text-color-green" style="display: none;">0.00</span>
                        </div>
                        <div v-else>
                            <span id="draw_bet_count_old" :class="betTotalValue.team_draw_bet_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="draw_total">{{betTotalValue.team_draw_bet_total}}</span>)</span>
                            <span id="draw_bet_count_new" class="tolose text-color-red" style="display: none;">0.00</span>
                        </div>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-blue-bg-2 opnForm ODDSBack" :class="'td_team'+(index+1)+'_back_2'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" :data-val="roundFloatVal(team.b3)" data-cls="cyan-bg"
                           class="back1btn text-color-black"> {{ roundFloatVal(team.b3) }} <br><span>{{ team.bs3 }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-blue-bg-3 ODDSBack" :class="'td_team'+(index+1)+'_back_1'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="cyan-bg" :data-val="roundFloatVal(team.b2)"
                           class="back1btn text-color-black"> {{ roundFloatVal(team.b2) }}<br><span>{{ team.bs2 }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="cyan-bg ODDSBack" :class="'td_team'+(index+1)+'_back_0'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" :data-val="roundFloatVal(team.b1)" data-cls="cyan-bg"
                           class="back1btn text-color-black"> {{ roundFloatVal(team.b1) }} <br><span>{{ team.bs1 }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="pink-bg ODDSLay" :class="'td_team'+(index+1)+'_lay_0'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" :data-val="roundFloatVal(team.l1)" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ roundFloatVal(team.l1) }} <br><span>{{ team.ls1 }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-pink-bg-2 ODDSLay" :class="'td_team'+(index+1)+'_lay_1'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" :data-val="roundFloatVal(team.l2)" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ roundFloatVal(team.l2) }} <br><span>{{ team.ls2 }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-pink-bg-3 ODDSLay" :class="'td_team'+(index+1)+'_lay_2'">
                        <a data-bettype="ODDS" onclick="opnForm(this)" :data-team="'team'+(index+1)" :data-val="roundFloatVal(team.l3)" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ roundFloatVal(team.l3) }} <br><span>{{ team.ls3 }}</span></a>
                    </td>
                </tr>
                <tr id="mobile_tr" :class="'tr_team'+(index+1)" class="mobileBack mobile_bet_model_div">
                    <td colspan="7" class="mobile_tr_common_class" :class="'tr_team'+(index+1)+'_td_mobile'"></td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div class="inplay-tableblock" id="inplay-tableblock" v-else>
        <table class="table custom-table inplay-table w1-table" >
            <tr>
                <td colspan="7" class="text-center pt-3">
                    <div id="site_bet_loading1" class="betloaderimage1 loader-style1">
                        <ul class="loading1">
                            <li>
                                <img src="/asset/front/img/loaderajaxbet.gif">
                            </li>
                            <li>Loading...</li>
                        </ul>
                    </div>
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
                teams: []
            };
        },
        computed:{
            betTotalValue(){
                return JSON.parse(this.bet_total)
            }
        },
        mounted() {
            window.Echo.channel('match-detail').listen('.' + this.event_id, (data) => {

                this.selections = 0;
                var newRecords = data.records;

                if(newRecords.t1!=undefined){
                    newRecords.t1 = this.sortedArray(newRecords.t1);
                    for (var i=0;i < newRecords.t1.length;i++) {
                        //team1 spark changes
                        if(this.teams[i]!=undefined) {
                            if ((this.teams[i].b3 != newRecords.t1[i].b3)) {
                                $(".td_team"+(i+1)+"_back_2").addClass('spark');
                            }
                            if ((this.teams[i].b2 != newRecords.t1[i].b2)) {
                                $(".td_team"+(i+1)+"_back_1").addClass('spark');
                            }
                            if ((this.teams[i].b1 != newRecords.t1[i].b1)) {
                                $(".td_team"+(i+1)+"_back_0").addClass('spark');
                            }

                            if ((this.teams[i].l3 != newRecords.t1[i].l3)) {
                                $(".td_team"+(i+1)+"_lay_2").addClass('sparkLay');
                            }
                            if ((this.teams[i].l2 != newRecords.t1[i].l2)) {
                                $(".td_team"+(i+1)+"_lay_1").addClass('sparkLay');
                            }
                            if ((this.teams[i].l1 != newRecords.t1[i].l1)) {
                                $(".td_team"+(i+1)+"_lay_0").addClass('sparkLay');
                            }
                        }
                    }

                    this.teams = newRecords.t1;
                }

                // this.match = newRecords;

                this.selections = this.teams.length;
                // console.log("match ",data.records)
            });
        },
        methods: {
            sortedArray: function(arrays) {
                function compare(a, b) {
                    if (a.sortPriority < b.sortPriority)
                        return -1;
                    if (a.sortPriority > b.sortPriority)
                        return 1;

                    return 0;
                }

                var arr2 =  arrays.sort(compare);
                return arr2;
            },
            roundFloatVal(num) {
                var m = Number((Math.abs(num) * 100).toPrecision(15));
                return Math.round(m) / 100 * Math.sign(num);
            },
        }
    }
</script>

<style>
    body {
        overflow: hidden;
    }

    .fir-col3.pinimg img {
        width: 100%;
    }

    .betloaderimage1 {
        top: 50%;
        height: 135px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 5px 10px rgb(0 0 0 / 50%);
        padding-top: 30px;
        z-index: 50;
        position: absolute;
        left: 50%;
        width: 190px;
        margin-left: -95px;
    }

    .loading1 img {
        background-position: -42px -365px;
        height: 51px;
        width: 51px;
    }

    .loading1 li {
        list-style: none;
        text-align: center;
        font-size: 11px;
    }
</style>
