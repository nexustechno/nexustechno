<template>
    <div v-if="!loading" class="fancy-section">
        <div v-if="match.t3!=undefined" id="fancybetdiv" class="fancy-bet-txt " style="padding-top:10px">
            <h4>
                <span class="blue-bg-3 text-color-white"> <img :src="clockgreenicon"> <b> Fancy Bet </b> </span>
                <a data-toggle="modal" data-target="#rulesFancyBetsModal"> <img :src="infoicon"> </a>
            </h4>
            <div id="fancyBetTabWrap" class="fancy_bet_tab-wrap" style="">
                <ul class="nav nav-pills special_bets-tab" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-all" role="tab" aria-controls="pills-all" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-fancy" role="tab" aria-controls="pills-fancy" aria-selected="false">Fancy</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-ball-by-aall" role="tab" aria-controls="pills-ball-by-aall" aria-selected="false">Ball by Ball</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-khadda" role="tab" aria-controls="pills-khadda" aria-selected="false">Khadda</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-lottery" role="tab" aria-controls="pills-lottery" aria-selected="false">Lottery</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-odd-even" role="tab" aria-controls="pills-odd-even" aria-selected="false">Odd/Even</a>
                    </li>
                </ul>
            </div>
        </div>

        <p v-if="match.t3!=undefined" class="fancyBetSpecialBet">
            <i class="fas fa-thumbtack"></i>
            Fancy Bet
        </p>

        <table v-if="match.t3!=undefined" class="table custom-table inplay-table w1-table " id="inplay-tableblock-fancy" style="margin-top:0px;">
            <tbody>
            <tr class="bets-fancy desktop-ui-tr white-bg">
                <td colspan="3">
                    <div class="minmax-txt minmaxmobile" style="padding-left:0px">
                        <span>Min</span>
                        <span id="div_min_bet_odds_limit" class="fancyMin">{{ min_bet_fancy_limit }}</span>
                        <span>Max</span>
                        <span id="div_max_bet_odds_limit" class="fancyMax">{{ max_bet_fancy_limit }}</span>
                    </div>
                </td>
                <td style="padding-left: 0px;
						padding-right: 0px;
						padding-bottom: 0px;
						vertical-align: bottom;">
                    <a class="layall_fancy" style="position: relative;
						line-height: 17px;
						cursor: pointer;">
                        <img :src="pinkbg1_fancy" style="width: 100%;
						height: 25px;">
                        <span style="position: absolute;
						top: 0;
						left: 5%;
						width: 90%;
						text-align: center;
						font-weight: 700;">No</span>
                    </a></td>
                <td style="padding-left: 0px;
						padding-right: 0px;
						padding-bottom: 0px;
						vertical-align: bottom;">
                    <a class="backall_fancy" style="position: relative;
						line-height: 17px;
						cursor: pointer;">
                        <img :src="bluebg1_fancy" style="width: 100%;
						height: 25px;">
                        <span style="position: absolute;
						top: 0;
						left: 5%;
						width: 90%;
						text-align: center;
						font-weight: 700;">Yes</span>
                    </a>
                </td>
                <td colspan="1"></td>
            </tr>
            <tr class="bets-fancy mobile-ui-tr collapse white-bg">
                <td colspan="3"></td>
                <td style="min-width: 70px;">
                    <span style="">No</span>
                </td>
                <td style="min-width: 70px;">
                    <span style="">Yes</span>
                </td>
                <td></td>
            </tr>
            <template v-for="(fancy, index) in match.t3" >
                <tr :key="fancy.sId" v-if="fancy.status=='BALL RUNNING' || fancy.status=='Ball Running' || fancy.status=='SUSPENDED' || fancy.status=='SUSPEND'" :id="'tr_fancy_suspend_'+index" class="fancy-suspend-tr-1 desktop-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span class="text-uppercase">{{fancy.status}}</span>
                        </div>
                    </td>
                </tr>
                <tr class="white-bg desktop-ui-tr" :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.nat }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.nat" :data-target="'#runPosition'+index">
                                <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index" :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.sId) }}</span>
                                    <span class="new-fancy-total collapse" :id="'New_Fancy_Total_'+index">0</span>
                                </span>
                            </a>
                        </div>

                    </td>
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index" :data-team="fancy.nat" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat" data-cls="pink-bg" :data-volume="Math.round(fancy.ls1)" :data-val="Math.round(fancy.l1)">{{ Math.round(fancy.l1) }}<br> <span>{{ Math.round(fancy.ls1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index" :data-team="fancy.nat" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat" data-cls="cyan-bg" :data-volume="Math.round(fancy.bs1)" :data-val="Math.round(fancy.b1)">{{ Math.round(fancy.b1) }}<br> <span>{{ Math.round(fancy.bs1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}}</td>
                </tr>
                <tr class="white-bg light-bg-tr-fancy mobile-ui-tr collapse light-bg-tr-fancy" :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.nat }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.nat" :data-target="'#runPosition'+index">
                                 <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index" :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.sId) }}</span>
                                    <span class="new-fancy-total collapse" :id="'New_Fancy_Total_'+index">0</span>
                                </span>
                            </a>
                        </div>
                    </td>
                    <td colspan="2" class="text-right" :class="'td_fancy_lay_colspan_'+index" align="right" valign="middle">
                        <a :href="'#feeds_'+index" class="" data-toggle="collapse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path fill="%233B5160" fill-rule="evenodd" d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/></svg>
                        </a>
                        <div :id="'feeds_'+index" class="collapse fancy_minmax_info text-let">
                            <dl>
                                <dt>Min / Max</dt>
                                <dd id="minMax"> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}} </dd>
                            </dl>
                        </div>
                    </td>
                    <td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}} </td>
                </tr>
                <tr class="white-bg white-bg-tr-fancy mobile-ui-tr collapse" :class="'tr_fancy_'+index">
                    <td colspan="3"></td>
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index" :data-team="fancy.nat" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat" data-cls="pink-bg" :data-volume="Math.round(fancy.ls1)" :data-val="Math.round(fancy.l1)">{{ Math.round(fancy.l1) }}<br> <span>{{ Math.round(fancy.ls1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index" :data-team="fancy.nat" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat" data-cls="cyan-bg" :data-volume="Math.round(fancy.bs1)" :data-val="Math.round(fancy.b1)">{{ Math.round(fancy.b1) }}<br> <span>{{ Math.round(fancy.bs1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}}</td>
                </tr>
                <tr v-if="getFancyStatus(fancy.gstatus,index)" :id="'tr_fancy_suspend_'+index" class="fancy-suspend-tr-1 mobile-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span class="text-uppercase">{{fancy.gstatus}}</span>
                        </div>
                    </td>
                </tr>
                <tr v-else class="mobileBack mobile_bet_model_div" :class="'tr_team'+index+'_fancy'" id="mobile_tr">
                    <td colspan="6" class="mobile_tr_common_class" :class="'tr_team'+index+'_fancy_td_mobile'"></td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <!--    <div class="fancy-section w-100" v-else-if="loading">-->
    <!--        <table width="100%" class="table custom-table inplay-table-1 w1-table cricket-table1" id="inplay-tableblock-bookmaker">-->
    <!--            <tr>-->
    <!--                <td colspan="7">-->
    <!--                    <p class="text-center font-weight-bold">Loading Fancy Data...</p>-->
    <!--                </td>-->
    <!--            </tr>-->
    <!--        </table>-->
    <!--    </div>-->
</template>

<script>
    export default {
        props: ['event_id', 'bar_image', 'clockgreenicon', 'infoicon','min_bet_fancy_limit', 'max_bet_fancy_limit','pinkbg1_fancy','bluebg1_fancy','bet_total','sports_id'],
        data() {
            return {
                match: [],
                loading:true
            };
        },
        computed:{
            betTotalValue(){
                return JSON.parse(this.bet_total)
            }
        },
        mounted() {
            window.Echo.channel('match-detail').listen('.' + this.event_id, (data) => {
                this.loading = false;

                // $(".mobileBack td.mobile_tr_common_class").html("");

                if(data.records.t3!=undefined) {
                    var records = data.records;
                    records.t3 = this.sortedArray(data.records.t3);
                    this.match = records;
                }else{
                    this.match = data.records;
                }
                // console.log("match ",data.records)
            });
        },
        methods: {
            getFancyStatus(gstatus,index){
                if(gstatus=='Ball Running' || gstatus=='SUSPENDED'){
                    $(".tr_team"+index+"_fancy_td_mobile").html("");
                    return true;
                }

                return false;
            },
            getFancyBetValue(sid){
                if(this.betTotalValue['fancy_'+sid]!=undefined){
                    return this.betTotalValue['fancy_'+sid];
                }

                return 0;
            },
            sortedArray: function(arrays) {
                function compare(a, b) {
                    if (a.sId < b.sId)
                        return -1;
                    if (a.sId > b.sId)
                        return 1;
                    return 0;
                }

                return arrays.sort(compare);
            },
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
