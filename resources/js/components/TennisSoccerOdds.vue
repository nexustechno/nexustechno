<template>
    <div class="inplay-tableblock" id="inplay-tableblock" v-if="match[0]!=undefined && match[0].runners!=undefined && match[0].runners.length > 0">
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
            <template v-for="(runner, index) in match[0].runners">
                <tr :key="runner.selectionId" v-if="match[0].status == 'CLOSED' || match[0].status == 'SUSPENDED' || runner.status == 'CLOSED' || runner.status == 'SUSPENDED'" class="fancy-suspend-tr 222222" :class="'team'+(index+1)+'_fancy'"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>CLOSED</span></div></td></tr>
                <tr :id="'team'+(index+1)" class="white-bg " :class="'tr_team'+(index+1)">
                    <td v-if="index < 2">
                        <img :src="bar_image"> <b :class="'team'+(index+1)">{{ team[index] }}</b>
                        <div>
                            <span :id="'team'+(index+1)+'_bet_count_old'" :class="betTotalValue['team'+(index+1)+'_bet_total'] < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span :id="'team'+(index+1)+'_total'">{{betTotalValue['team'+(index+1)+'_bet_total']}}</span>)</span>
                            <span :id="'team'+(index+1)+'_bet_count_new'" class="towin text-color-green" style="display: none;">0.00</span>
                        </div>
                    </td>
                    <td v-else>
                        <img :src="bar_image"> <b :class="'team'+(index+1)">The Draw</b>
                        <div>
                            <span id="draw_bet_count_old" :class="betTotalValue.team_draw_bet_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="draw_total">{{betTotalValue.team_draw_bet_total}}</span>)</span>
                            <span id="draw_bet_count_new" class="tolose text-color-red" style="display: none;">0.00</span>
                        </div>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-blue-bg-2 opnForm ODDSBack" :class="'td_team'+(index+1)+'_back_2'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" :data-val="runner.ex.availableToBack[2].price" data-cls="cyan-bg"
                           class="back1btn text-color-black"> {{ runner.ex.availableToBack[2].price }} <br><span>{{ runner.ex.availableToBack[2].size }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-blue-bg-3 ODDSBack" :class="'td_team'+(index+1)+'_back_1'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" data-cls="cyan-bg" :data-val="runner.ex.availableToBack[1].price"
                           class="back1btn text-color-black"> {{ runner.ex.availableToBack[1].price }}<br><span>{{ runner.ex.availableToBack[1].size }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="cyan-bg ODDSBack" :class="'td_team'+(index+1)+'_back_0'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" :data-val="runner.ex.availableToBack[0].price" data-cls="cyan-bg"
                           class="back1btn text-color-black"> {{ runner.ex.availableToBack[0].price }} <br><span>{{ runner.ex.availableToBack[0].size }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="pink-bg ODDSLay" :class="'td_team'+(index+1)+'_lay_0'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" :data-val="runner.ex.availableToLay[0].price" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ runner.ex.availableToLay[0].price }} <br><span>{{ runner.ex.availableToLay[0].size }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-pink-bg-2 ODDSLay" :class="'td_team'+(index+1)+'_lay_1'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" :data-val="runner.ex.availableToLay[1].price" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ runner.ex.availableToLay[1].price }} <br><span>{{ runner.ex.availableToLay[1].size }}</span></a>
                    </td>
                    <td :data-team="'team'+(index+1)" class="light-pink-bg-3 ODDSLay" :class="'td_team'+(index+1)+'_lay_2'">
                        <a data-bettype="ODDS" :data-team="'team'+(index+1)" onclick="opnForm(this)" :data-val="runner.ex.availableToLay[2].price" data-cls="pink-bg"
                           class="lay1btn text-color-black"> {{ runner.ex.availableToLay[2].price }} <br><span>{{ runner.ex.availableToLay[2].size }}</span></a>
                    </td>
                </tr>
                <tr id="mobile_tr" class="mobileBack mobile_bet_model_div" :class="'tr_team'+(index+1)">
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
        props: ['event_id', 'bar_image', 'min_bet_odds_limit', 'max_bet_odds_limit', 'pinkbg1','pinbg','pinbg1', 'bluebg1','team','bet_total','sports_id'],
        data() {
            return {
                match: [],
                selections:0
            };
        },
        computed:{
            betTotalValue(){
                return JSON.parse(this.bet_total)
            }
        },
        mounted() {

            if(this.sports_id == 4) {
                var LaravelEcho = window.Echo;
            }else{
                var LaravelEcho = window.Echo2;
            }

            LaravelEcho.channel('match-detail').listen('.' + this.event_id, (data) => {

                var newRecords = data.records;

                if(this.match[0]!=undefined){

                    for(var i=0;i<this.match[0].runners.length;i++){
                        //team1 spark changes
                        if((this.match[0].runners[i].ex.availableToBack[2].price!=newRecords[0].runners[i].ex.availableToBack[2])){
                            $(".td_team"+(i+1)+"_back_2").addClass('spark');
                        }
                        if((this.match[0].runners[i].ex.availableToBack[1].price!=newRecords[0].runners[i].ex.availableToBack[1])){
                            $(".td_team"+(i+1)+"_back_1").addClass('spark');
                        }
                        if((this.match[0].runners[i].ex.availableToBack[0].price!=newRecords[0].runners[i].ex.availableToBack[0])){
                            $(".td_team"+(i+1)+"_back_0").addClass('spark');
                        }

                        if((this.match[0].runners[i].ex.availableToLay[2].price!=newRecords[0].runners[i].ex.availableToLay[2])){
                            $(".td_team"+(i+1)+"_lay_2").addClass('sparkLay');
                        }
                        if((this.match[0].runners[i].ex.availableToLay[1].price!=newRecords[0].runners[i].ex.availableToLay[1])){
                            $(".td_team"+(i+1)+"_lay_1").addClass('sparkLay');
                        }
                        if((this.match[0].runners[i].ex.availableToLay[0].price!=newRecords[0].runners[i].ex.availableToLay[0])){
                            $(".td_team"+(i+1)+"_lay_0").addClass('sparkLay');
                        }
                    }
                }

                this.match = newRecords;

                this.selections = this.match[0].runners.length;

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
