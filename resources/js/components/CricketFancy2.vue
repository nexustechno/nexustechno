<template>
    <div v-if="!loading" class="fancy-section">
        <div id="fancybetdiv" class="fancy-bet-txt " style="padding-top:10px"
             v-if="t4.length > 0 || match.t3.length > 0">
            <div id="fancyBetHead" class="" :class="fancyType=='premium' ? 'sportsbook_bet-head':'fancy_bet-head'"
                 style="">
                <template v-if="fancyType=='premium'">
                    <h4 @click="fancyType='premium'" v-if="t4.length > 0" class="fa-in-play">
                        <span>Premium</span>
                        <a href="#feeds_premium" data-toggle="collapse" class="btn-head_rules">Rules</a>
                        <div id="feeds_premium" class="collapse premium_minmax_info text-left">
                            <dl>
                                <dt>Min / Max</dt>
                                <dd> {{min_premium_limit}} / {{max_premium_limit}}</dd>
                            </dl>
                        </div>
                    </h4>
                    <a id="showSportsBookBtn" v-if="match.t3.length > 0" class="other-tab" style=""
                       @click="fancyType='general'">Fancy Bet</a>
                </template>
                <template v-else>
                    <h4 @click="fancyType='general'" class="fa-in-play">
                        <span>Fancy Bet</span>
                        <a data-toggle="modal" data-target="#rulesFancyBetsModal" class="btn-head_rules">Rules</a>
                    </h4>
                    <a id="showSportsBookBtn" class="other-tab" style="" v-if="t4.length > 0"
                       @click="fancyType='premium'"><span class="tag-new">New</span>Premium</a>
                </template>
            </div>
            <div id="fancyBetTabWrap" v-if="t4.length > 0 || match.t3.length > 0" :class="fancyType"
                 class="fancy_bet_tab-wrap" style="">
                <ul class="nav nav-pills special_bets-tab" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-all" role="tab"
                           aria-controls="pills-all" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-fancy" role="tab"
                           aria-controls="pills-fancy" aria-selected="false">Fancy</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-ball-by-aall"
                           role="tab" aria-controls="pills-ball-by-aall" aria-selected="false">Ball by Ball</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-khadda" role="tab"
                           aria-controls="pills-khadda" aria-selected="false">Khadda</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-lottery" role="tab"
                           aria-controls="pills-lottery" aria-selected="false">Lottery</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-odd-even" role="tab"
                           aria-controls="pills-odd-even" aria-selected="false">Odd/Even</a>
                    </li>
                </ul>
            </div>
        </div>

        <p v-if="fancyType=='general' && match.t3!=undefined && match.t3.length > 0" class="fancyBetSpecialBet p-2">
            <i class="fas fa-thumbtack mr-1"></i>
            Fancy Bet
        </p>

        <table v-if="match.t3!=undefined" :class="fancyType=='premium' ? 'collapse':'' "
               class="table custom-table inplay-table w1-table " id="inplay-tableblock-fancy" style="margin-top:0px;">
            <tbody>
            <tr class="bets-fancy desktop-ui-tr white-bg" v-if="match.t3.length > 0">
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
            <tr class="bets-fancy mobile-ui-tr collapse white-bg" v-if="match.t3.length > 0">
                <td colspan="3"></td>
                <td style="min-width: 70px;">
                    <span style="">No</span>
                </td>
                <td style="min-width: 70px;">
                    <span style="">Yes</span>
                </td>
                <td></td>
            </tr>
            <template v-for="(fancy, index) in match.t3">
                <tr :key="fancy.sId"
                    v-if="status_f=='0' || fancy.status=='BALL RUNNING' || fancy.status=='Ball Running' || fancy.status=='SUSPENDED' || fancy.status=='SUSPEND'"
                    :id="'tr_fancy_suspend_'+index" class="fancy-suspend-tr-1 desktop-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span v-if="status_f=='0'" class="text-uppercase">SUSPENDED</span>
                            <span v-else class="text-uppercase">{{fancy.status}}</span>
                        </div>
                    </td>
                </tr>
                <tr class="white-bg desktop-ui-tr" :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.nat }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.nat"
                               :data-target="'#runPosition'+index">
                                <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index"
                                          :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.sId) }}</span>
                                    <span class="new-fancy-total collapse" :id="'New_Fancy_Total_'+index">0</span>
                                </span>
                            </a>
                        </div>

                    </td>
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index"
                        :data-team="fancy.nat" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat"
                           data-cls="pink-bg" :data-volume="Math.round(fancy.ls1)" :data-val="Math.round(fancy.l1)">{{
                            Math.round(fancy.l1) }}<br> <span>{{ Math.round(fancy.ls1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index"
                        :data-team="fancy.nat" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat"
                           data-cls="cyan-bg" :data-volume="Math.round(fancy.bs1)" :data-val="Math.round(fancy.b1)">{{
                            Math.round(fancy.b1) }}<br> <span>{{ Math.round(fancy.bs1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"><span>Min/Max</span> <br> {{min_bet_fancy_limit}} /
                        {{max_bet_fancy_limit}}
                    </td>
                </tr>
                <tr class="white-bg light-bg-tr-fancy mobile-ui-tr collapse light-bg-tr-fancy"
                    :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.nat }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.nat"
                               :data-target="'#runPosition'+index">
                                 <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index"
                                          :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.sId) }}</span>
                                    <span class="new-fancy-total collapse" :id="'New_Fancy_Total_'+index">0</span>
                                </span>
                            </a>
                        </div>
                    </td>
                    <td colspan="2" class="text-right" :class="'td_fancy_lay_colspan_'+index" align="right"
                        valign="middle">
                        <a :href="'#feeds_'+index" class="" data-toggle="collapse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15">
                                <path fill="%233B5160" fill-rule="evenodd"
                                      d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/>
                            </svg>
                        </a>
                        <div :id="'feeds_'+index" class="collapse fancy_minmax_info text-let">
                            <dl>
                                <dt>Min / Max</dt>
                                <dd id="minMax"> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}}</dd>
                            </dl>
                        </div>
                    </td>
                    <td class="zeroopa1" colspan="1"><span>Min/Max</span> <br> {{min_bet_fancy_limit}} /
                        {{max_bet_fancy_limit}}
                    </td>
                </tr>
                <tr class="white-bg white-bg-tr-fancy mobile-ui-tr collapse" :class="'tr_fancy_'+index">
                    <td colspan="3"></td>
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index"
                        :data-team="fancy.nat" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat"
                           data-cls="pink-bg" :data-volume="Math.round(fancy.ls1)" :data-val="Math.round(fancy.l1)">{{
                            Math.round(fancy.l1) }}<br> <span>{{ Math.round(fancy.ls1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index"
                        :data-team="fancy.nat" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.nat"
                           data-cls="cyan-bg" :data-volume="Math.round(fancy.bs1)" :data-val="Math.round(fancy.b1)">{{
                            Math.round(fancy.b1) }}<br> <span>{{ Math.round(fancy.bs1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"><span>Min/Max</span> <br> {{min_bet_fancy_limit}} /
                        {{max_bet_fancy_limit}}
                    </td>
                </tr>
                <tr v-if="getFancyStatus(fancy.status,index)" :id="'tr_fancy_suspend_'+index"
                    class="fancy-suspend-tr-1 mobile-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span class="text-uppercase">{{fancy.status}}</span>
                        </div>
                    </td>
                </tr>
                <tr v-else class="mobileBack mobile_bet_model_div" :class="'tr_team'+index+'_fancy'" id="mobile_tr">
                    <td colspan="6" class="mobile_tr_common_class" :class="'tr_team'+index+'_fancy_td_mobile'"></td>
                </tr>
            </template>
            </tbody>
        </table>

        <table v-if="t4!=undefined" :class="fancyType=='general' ? 'collapse':'w-100'">
            <tr>
                <td>
                    <table class="bets w-100 premium-table" v-for="(match,index) in t4" :key="match.id">
                        <tr class="special_bet">
                            <td colspan="7" @click="showHideTeam('.collapsable_'+match.id)" style="cursor: pointer;">
                                <h3 class="marketHeader">
                                    <a id="" class="add-pin multiMarketPin" title="Add to Multi Markets">
                                        <i class="fas fa-thumbtack"></i>
                                    </a>
                                    <a class="marketName">
                                        {{match.marketName}}
                                    </a>
                                    <i v-if="isSectionVisible('.collapsable_'+match.id)"
                                       class="fa fa-plus marketNameIcon" aria-hidden="true"></i>
                                    <i v-else class="fa fa-minus marketNameIcon" aria-hidden="true"></i>
                                </h3>
                            </td>
                        </tr>
                        <template v-for="(runner, index2) in match.sub_sb">
                            <tr :key="runner.sId"
                                :class="index < 5 ? 'collapsable_'+match.id : 'collapse collapsable_'+match.id">
                                <th class="" colspan="3">
                                    <dl class="fancy-th-layout">
                                        <dt>
                                            <p class="selectionName">{{runner.nat}}</p>
                                            <span :class="'premium_total'+index2">
                                                <span class="" :id="'premium_total'+index2"
                                                      :class="getPremiumBetValue(match.id,runner.nat) < 0 ? 'tolose text-color-red':'towin text-color-green'">{{ getPremiumBetValue(match.id,runner.nat) }}</span>
                                                <span class="new-fancy-total collapse"
                                                      :id="'premium_total'+index2">0</span>
                                            </span>
                                        </dt>
                                    </dl>
                                </th>
                                <td class="back-1 no-liq" colspan="2">
                                    <a class="info" @click="openBetForm(match,runner)" :data-team="runner.nat"
                                       :data-val="runner.odds">{{runner.odds}}</a>
                                </td>
                                <td class="refer-book" colspan="2"></td>
                            </tr>
                            <tr class="fancy-suspend-tr collapse">
                                <th colspan="3"></th>
                                <td class="fancy-suspend-td" colspan="2">
                                    <div class="fancy-suspend-white"><span>Suspend</span></div>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="fancy-quick-tr" v-if="premiumBetForm.selection_sid == runner.sId">
                                <td colspan="7">
                                    <!-- Quick Bet Wrap -->
                                    <dl class="quick_bet-wrap slip-book">
                                        <dt id="sportsBookBetHeader" class="">
                                        </dt>
                                        <dd class="col-btn"><a id="cancel" class="btn font-weight-bold"
                                                               @click="cancleBetForm()">Cancel</a></dd>
                                        <dd id="oddsHeader" class="col-odd">
                                            <ul class="quick-bet-confirm">
                                                <li id="odds">{{ premiumBetForm.bet_odds }}</li>
                                                <li class="quick-bet-confirm-title">odds</li>
                                            </ul>
                                        </dd>
                                        <dd class="col-stake">
                                            <input class="inputStake text-center" type="number"
                                                   v-model="premiumBetForm.bet_amount">
                                        </dd>
                                        <dd class="col-send">
                                            <a id="placeBet" class="btn-black" v-if="betLoading">Loading...</a>
                                            <a id="placeBet" class="btn-black" v-else @click="placeBet()">Place Bets</a>
                                        </dd>
                                        <dd id="stakePopupList" class="col-stake_list">
                                            <ul>
                                                <li v-for="(stack, sIndex) in preDefinedStacks" :key="sIndex"><a
                                                    class="btn" :data-stake="stack" @click="setDefaultStack(stack)"
                                                    style="cursor:pointer;">{{stack}}</a></li>
                                            </ul>
                                        </dd>
                                    </dl>
                                    <!-- Quick Bet Wrap End -->
                                </td>
                            </tr>
                        </template>
                    </table>
                </td>
            </tr>
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
    import axios from 'axios';

    export default {
        props: ['event_id', 'bar_image', 'clockgreenicon', 'infoicon', 'pinbg', 'pinbg1', 'premium_bet_total', 'min_bet_fancy_limit', 'max_bet_fancy_limit', 'min_premium_limit', 'max_premium_limit', 'pinkbg1_fancy', 'bluebg1_fancy', 'bet_total', 'sports_id', 'status_f', 'stakval', 'fancy_enable', 'premium_enable'],
        data() {
            return {
                match: [],
                fancyType: '',
                t4: [],
                loading: true,
                premiumBetTotal: [],
                oldPremiumBetTotal: [],
                betLoading: false,
                premiumBetForm: {
                    market_id: null,
                    match_id: null,
                    market_name: null,
                    team_name: '',
                    selection_sid: '',
                    bet_amount: '',
                    bet_odds: 0,
                    bet_type: 'PREMIUM',
                    bet_side: 'back',
                    extra: []
                },
            };
        },
        computed: {
            betTotalValue() {
                return JSON.parse(this.bet_total)
            },
            premiumBetTotalValue: {
                // getter
                get() {
                    return this.premiumBetTotal;
                },
                // setter
                set(newValue) {
                    // Note: we are using destructuring assignment syntax here.
                    this.premiumBetTotal = newValue;
                }
            },
            preDefinedStacks() {
                return JSON.parse(this.stakval)
            }
        },
        mounted() {
            if (this.sports_id == 4) {
                this.fancyType = 'general';
            } else {
                this.fancyType = 'premium';
            }

            this.premiumBetTotal = JSON.parse(this.premium_bet_total);
            this.oldPremiumBetTotal = JSON.parse(this.premium_bet_total);
            if (this.fancy_enable == true) {
                window.Echo.channel('match-detail').listen('.' + this.event_id, (data) => {
                    this.loading = false;

                    // $(".mobileBack td.mobile_tr_common_class").html("");

                    if (data.records.t3 != undefined) {
                        var records = data.records;
                        records.t3 = this.sortedArray(data.records.t3);
                        this.match = records;
                    } else {
                        this.match = data.records;
                    }
                    // console.log("match ",data.records)
                });
            }
            if (this.premium_enable == true) {
                window.Echo.channel('premium-detail').listen('.' + this.event_id, (data) => {
                    this.loading = false;

                    // $(".mobileBack td.mobile_tr_common_class").html("");

                    if (data.records.matches.t4 != undefined) {
                        // var records = data.records;
                        var t4 = this.sortedArray(data.records.matches.t4);
                        this.t4 = t4;
                    } else {
                        // this.match = data.records;
                    }
                    // console.log("match ", data.records)
                });
            }
        },
        watch: {
            'premiumBetForm.bet_amount': function () {
                this.calculateBetProfitLoss();
            }
        },
        methods: {
            calculateBetProfitLoss() {
                var oddval = this.premiumBetForm.bet_odds;
                oddval = parseFloat(oddval) - parseInt(1);
                var fval = this.premiumBetForm.bet_amount;
                var finalValue = parseFloat(fval) * parseFloat(oddval);

                if (this.premiumBetTotal[this.premiumBetForm.market_id] != undefined) {
                    var oldValues = this.oldPremiumBetTotal[this.premiumBetForm.market_id];
                    for (var team in this.premiumBetTotal[this.premiumBetForm.market_id]) {

                        var oldProfitLoss = 0;
                        if(this.oldPremiumBetTotal[this.premiumBetForm.market_id]!=undefined && oldValues[team]!=undefined){
                            oldProfitLoss = oldValues[team]['PREMIUM_profitLost'];
                        }

                        if (team == this.premiumBetForm.team_name) {
                            this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = oldProfitLoss + finalValue;
                        } else {
                            this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = oldProfitLoss - fval;
                        }
                    }
                } else {
                    this.premiumBetTotal[this.premiumBetForm.market_id] = {};
                    for (var i=0;i<this.premiumBetForm.extra.length;i++) {
                        var team = this.premiumBetForm.extra[i];
                        this.premiumBetTotal[this.premiumBetForm.market_id][team] = {
                            PREMIUM_profitLost:0
                        };
                        if (team == this.premiumBetForm.team_name) {
                            this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = finalValue;
                        } else {
                            this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = (fval * -1);
                        }
                    }
                }

            },
            isSectionVisible(target) {
                if ($(target).is(':visible')) {
                    return false;
                }

                return true;
            },
            getPremiumBetValue(marketName, teamName) {
                if (this.premiumBetTotalValue[marketName] != undefined && this.premiumBetTotalValue[marketName][teamName] != undefined) {
                    return this.roundFloatVal(this.premiumBetTotalValue[marketName][teamName]['PREMIUM_profitLost']);
                }
                return 0;
            },
            placeBet() {
                if (getUser == undefined || getUser == null || getUser == '') {
                    $("#myLoginModal").modal('show');
                } else {
                    this.betLoading = true;
                    if (this.premiumBetForm.stack < this.min_premium_limit) {
                        toastr.error('Minimum bets is ' + this.min_premium_limit + '!');
                        return false;
                    }
                    if (this.premiumBetForm.stack > this.max_premium_limit) {
                        toastr.error('Minimum bets is ' + this.max_premium_limit + '!');
                        return false;
                    }
                    this.premiumBetForm.match_id = this.event_id;

                    axios({
                        method: 'POST',
                        url: "/MyBetStore",
                        data: this.premiumBetForm
                    }).then((response) => {
                        console.log("response", response.data);
                        if (response.data.status == 'false') {
                            toastr.error(response.data.msg);
                            this.betLoading = false;
                        } else if (response.data.status == 'true') {
                            toastr.success(response.data.msg);
                            this.premiumBetTotal = response.data.oddsBookmakerExposerArr['PREMIUM'];
                            this.oldPremiumBetTotal = response.data.oddsBookmakerExposerArr['PREMIUM'];
                            this.betLoading = false;
                            this.cancleBetForm();
                        }
                    });
                }
            },
            setDefaultStack(stack) {
                this.premiumBetForm.bet_amount = stack;
            },
            cancleBetForm() {

                if(this.oldPremiumBetTotal[this.premiumBetForm.market_id]!=undefined) {
                    for (var team in this.oldPremiumBetTotal[this.premiumBetForm.market_id]) {
                        this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = this.oldPremiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'];
                    }
                }else{
                    for (var team in this.premiumBetTotal[this.premiumBetForm.market_id]) {
                        this.premiumBetTotal[this.premiumBetForm.market_id][team]['PREMIUM_profitLost'] = 0;
                    }
                }

                // this.premiumBetTotal = this.oldPremiumBetTotal;

                this.premiumBetForm = {
                    market_id: null,
                    match_id: null,
                    market_name: null,
                    team_name: '',
                    selection_sid: '',
                    bet_amount: '',
                    bet_type: 'PREMIUM',
                    bet_side: 'back',
                    bet_odds: 0,
                    extra: []
                };
            },
            openBetForm(match, runner) {
                if (getUser == undefined || getUser == null || getUser == '') {
                    $("#myLoginModal").modal('show');
                } else {
                    this.cancleBetForm();
                    this.premiumBetForm.market_id = match.id;
                    this.premiumBetForm.market_name = match.marketName;
                    this.premiumBetForm.team_name = runner.nat;
                    this.premiumBetForm.selection_sid = runner.sId;
                    this.premiumBetForm.bet_odds = runner.odds;

                    for (var i = 0; i < match.sub_sb.length; i++) {
                        // if (match.sub_sb[i].nat != runner.nat) {
                        this.premiumBetForm.extra.push(match.sub_sb[i].nat);
                        // }
                    }
                }
            },
            roundFloatVal(price) {
                if (price != '' && price != undefined && price > 0 && price != '0') {
                    if (price.toString().includes('.')) {
                        return Math.round(price * 100) / 100;
                    }
                    return price;
                }
                return price;
            },
            showHideTeam(target) {
                $(target).toggle();
            },
            getFancyStatus(gstatus, index) {
                if (gstatus == 'BALL RUNNING' || gstatus == 'Ball Running' || gstatus == 'SUSPENDE' || gstatus == 'SUSPEND') {
                    $(".tr_team" + index + "_fancy_td_mobile").html("");
                    return true;
                }

                return false;
            },
            getFancyBetValue(sid) {
                if (this.betTotalValue['fancy_' + sid] != undefined) {
                    return this.betTotalValue['fancy_' + sid];
                }

                return 0;
            },
            sortedArray: function (arrays) {
                function compare(a, b) {
                    if (a.sortPriority < b.sortPriority)
                        return -1;
                    if (a.sortPriority > b.sortPriority)
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
<style>
    .marketNameIcon {
        font-size: 7px;
        position: absolute;
        top: 6px;
        right: 6px;
        border: solid 1px;
        padding: 1px 2px;
        border-radius: 3px;
        opacity: 0.9;
    }

    .bets {
        margin: 0 !important;
    }

    .bets .fancy-suspend-tr th {
        position: relative;
        height: 0;
        border-width: 0;
        margin-bottom: -1px;
        padding: 0;
    }

    .fancy-suspend-white {
        box-sizing: border-box;
        height: 35px;
        background-color: #fff;
        border: 1px solid #d0021b;
        text-align: center;
    }

    .fancy-suspend-white span {
        line-height: calc(35px - 2px);
        color: #d0021b;
        text-shadow: none;
        opacity: 1;
        text-align: center;
    }

    .bets .fancy-suspend-tr span {
        font-size: 12px;
    }

    .bets .special_bet, .bets-HS .special_bet, .bets-GH .special_bet {
        background-color: #243A48;
        color: #fff;
    }

    .bets .special_bet h3, .bets-HS .special_bet h3, .bets-GH .special_bet h3 {
        position: relative;
        padding: 0 0 0 10px;
    }

    .bets .special_bet h3 a, .bets-HS .special_bet h3 a, .bets-GH .special_bet h3 a {
        color: #fff;
    }

    .bets .special_bet a, .bets-HS .special_bet a, .bets-GH .special_bet a {
        height: inherit;
        padding: 0;
    }

    .marketName, .multiMarketPin {
        display: table-cell !important;
        font-size: 15px !important;
        padding: 5px !important;
    }

    .bets .back-1 {
        background-color: #72E3A0;
    }

    .multiMarketPin {
        background: #202f38 !important;
        padding: 5px 10px !important;
    }

    .bets td, .bets-HS td, .bets-GH td {
        width: 20%;
        border-bottom: 1px solid #7e97a7;
        border-left: 1px solid #ddd;
        font-weight: bold;
        vertical-align: top;
        z-index: 1;
    }

    .fancy-th-layout {
        margin-bottom: 0px;
    }

    .fancy-section tr.fancy-suspend-tr {
        /*position: absolute;*/
        /*width: 100%;*/
        /*display: inline-table;*/
    }

    .fancy-th-layout dt {
        flex: 1;
        margin-right: 5px;
        flex-wrap: wrap;
        margin-bottom: 0px;
    }

    .fancy-th-layout dt, .fancy-th-layout dd {
        align-items: center;
    }

    h3.marketHeader {
        line-height: 1;
        padding: 0 !important;
    }

    .bets td a, .bets-HS td a, .bets-GH td a {
        position: relative;
        height: 35px;
        color: #1e1e1e;
        padding: 3px 0 2px;
        cursor: pointer;
    }

    .fancy-section .other-tab:after {
        left: inherit;
        right: -4.53333vw;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 31"><path fill="%23243A48" fill-rule="evenodd" d="M-66 0H.637a8 8 0 0 1 7.595 5.488L17 32h-125l8.768-26.512A8 8 0 0 1-91.637 0H-66z"/></svg>');
    }

    .fancy-section .other-tab:before, .fancy-section .other-tab:after {
        content: '';
        position: absolute;
        top: 0;
        left: -36px;
        width: 37px;
        height: 24px;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 31"><path fill="%23243A48" fill-rule="evenodd" d="M42 0h66.637a8 8 0 0 1 7.595 5.488L125 32H0L8.768 5.488A8 8 0 0 1 16.363 0H42z"/></svg>');
        background-repeat: no-repeat;
        background-size: 101% 101%;
    }

    .fancy-section .other-tab:after {
        left: inherit;
        right: -36px;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 31"><path fill="%23243A48" fill-rule="evenodd" d="M-66 0H.637a8 8 0 0 1 7.595 5.488L17 32h-125l8.768-26.512A8 8 0 0 1-91.637 0H-66z"/></svg>');
    }

    .fancy-section .other-tab .tag-new {
        top: -5px;
        right: -35px;
    }

    .fancy-section .tag-new {
        position: absolute;
        width: 50px;
        height: 16px;
        background: url('data:image/svg+xml,<svg width="32" height="16" viewBox="0 0 32 16" xmlns="http://www.w3.org/2000/svg"><path d="M20 12l-7 4 1-4h-11c-1.657 0-3-1.343-3-3v-6c0-1.657 1.343-3 3-3h26c1.657 0 3 1.343 3 3v6c0 1.657-1.343 3-3 3h-9z" fill="%23D0021B"/></svg>') center no-repeat;
        background-size: contain;
        color: #FFFFFF !important;
        line-height: 1;
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.6));
        z-index: 2;
    }

    .fancy-section .other-tab {
        position: relative;
        /* height: 8vw; */
        /* line-height: 8vw; */
        color: #C5D0D7 !important;
        font-size: 13px;
        font-weight: bold;
        background-color: #243A48;
        padding: 4px;
        margin-left: 22px
    }

    .fancy-section .fancy-bet-txt h4 {
        display: inline-flex !important;
    }

    .fancy_bet-head h4, .sportsbook_bet-head h4 {
        height: 24px;
        color: #fff;
        font-size: 13px;
        margin-right: 0px;
        z-index: 1;
    }

    .fancy_bet-head h4 span, .sportsbook_bet-head h4 span {
        height: 100%;
        padding-left: 10px;
    }

    .fancy_bet-head h4 span {
        padding-right: 10px;
        background-image: linear-gradient(-180deg, #0A92A5 0%, #087989 82%);
    }

    .sportsbook_bet-head h4 span {
        padding-right: 10px;
        background-image: linear-gradient(180deg, #EF6C2C 0%, #E4550F 82%);
    }

    .fancy_bet-head .btn-head_rules {
        background-image: url('/asset/front/img/bg-fanctbet_rules.svg');
    }

    .sportsbook_bet-head .btn-head_rules {
        background-image: url('/asset/front/img/bg-sportsbook_rules.svg');
    }

    .btn-head_rules:before {
        content: '';
        width: 4vw;
        height: 4vw;
        background-image: url('data:image/svg+xml,<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"><path d="M6.35 10.9h1.3V9.6h-1.3v1.3zM7 .5A6.502 6.502 0 00.5 7c0 3.588 2.912 6.5 6.5 6.5s6.5-2.912 6.5-6.5S10.588.5 7 .5zm0 11.7A5.207 5.207 0 011.8 7c0-2.867 2.333-5.2 5.2-5.2s5.2 2.333 5.2 5.2-2.333 5.2-5.2 5.2zm0-9.1a2.6 2.6 0 00-2.6 2.6h1.3c0-.715.585-1.3 1.3-1.3.715 0 1.3.585 1.3 1.3 0 1.3-1.95 1.138-1.95 3.25h1.3c0-1.462 1.95-1.625 1.95-3.25A2.6 2.6 0 007 3.1z" fill="%23FFF" fill-rule="evenodd"/></svg>');
        background-repeat: no-repeat;
        background-size: contain;
    }

    .btn-head_rules:before {
        content: '';
        width: 15px;
        height: 15px;
        background-image: url('data:image/svg+xml,<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"><path d="M6.35 10.9h1.3V9.6h-1.3v1.3zM7 .5A6.502 6.502 0 00.5 7c0 3.588 2.912 6.5 6.5 6.5s6.5-2.912 6.5-6.5S10.588.5 7 .5zm0 11.7A5.207 5.207 0 011.8 7c0-2.867 2.333-5.2 5.2-5.2s5.2 2.333 5.2 5.2-2.333 5.2-5.2 5.2zm0-9.1a2.6 2.6 0 00-2.6 2.6h1.3c0-.715.585-1.3 1.3-1.3.715 0 1.3.585 1.3 1.3 0 1.3-1.95 1.138-1.95 3.25h1.3c0-1.462 1.95-1.625 1.95-3.25A2.6 2.6 0 007 3.1z" fill="%23FFF" fill-rule="evenodd"/></svg>');
        background-repeat: no-repeat;
        background-size: contain;
        display: block;
    }

    .btn-head_rules {
        width: 32px;
        height: 100%;
        background-size: 101% 101%;
        background-repeat: no-repeat;
        align-items: center;
        justify-content: center;
        vertical-align: bottom;
        font-size: 0;
        text-indent: -99999px;
        display: flex;
    }

    .btn-head_rules:before {
        content: '';
        width: 15px;
        height: 15px;
        background-image: url('data:image/svg+xml,<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"><path d="M6.35 10.9h1.3V9.6h-1.3v1.3zM7 .5A6.502 6.502 0 00.5 7c0 3.588 2.912 6.5 6.5 6.5s6.5-2.912 6.5-6.5S10.588.5 7 .5zm0 11.7A5.207 5.207 0 011.8 7c0-2.867 2.333-5.2 5.2-5.2s5.2 2.333 5.2 5.2-2.333 5.2-5.2 5.2zm0-9.1a2.6 2.6 0 00-2.6 2.6h1.3c0-.715.585-1.3 1.3-1.3.715 0 1.3.585 1.3 1.3 0 1.3-1.95 1.138-1.95 3.25h1.3c0-1.462 1.95-1.625 1.95-3.25A2.6 2.6 0 007 3.1z" fill="%23FFF" fill-rule="evenodd"/></svg>');
        background-repeat: no-repeat;
        background-size: contain;
        display: block;
    }

    .fancy_bet-head {
        border-bottom-color: #087989 !important;
    }

    .sportsbook_bet-head {
        border-bottom-color: #E4550F !important;
    }

    .fancy_bet-head, .sportsbook_bet-head {
        position: relative;
        line-height: 1px;
        border-bottom: 2px solid;
    }

    .fancy_bet_tab-wrap.premium {
        background-image: linear-gradient(180deg, #E4550F 15%, #E4550F 100%);
        /*background-color: #E4550F !important;*/
    }

    .fancy_bet .fancy-quick-tr {
        border-width: 0;
    }

    .quick_bet-wrap {
        border: 1px solid #7E97A7;
        border-width: 1px 0 1px 0;
        box-shadow: inset 0 2px 0 rgb(0 0 0 / 10%);
        padding: 0 2px 0 7px;
    }

    .slip-book {
        background-color: #d3edd0;
        border-bottom: 1px solid #9fd899;
        display: inline-table;
        width: 100%;
    }

    .quick_bet-wrap dt {
        width: 53.79665%;
        height: auto;
        line-height: 22px;
        padding: 13px 5px 12px 0;
    }

    .quick_bet-wrap dt, .quick_bet-wrap dd {
        box-sizing: border-box;
        float: left;
    }

    .slip-back dt, .slip-lay dt, .slip-book dt {
        position: relative;
        width: 42.0904%;
        font-weight: bold;
        padding-left: 5px;
    }

    .quick_bet-wrap .col-btn {
        width: 9.00901%;
        min-width: 85px;
        margin-left: 10px;
    }

    .quick_bet-wrap dd {
        padding: 7px 5px 0px 0;
        margin: 0;
    }

    .quick_bet-wrap dt, .quick_bet-wrap dd {
        box-sizing: border-box;
    }

    .slip-back dd, .slip-lay dd, .slip-book dd {
        position: relative;
        color: #243a48;
        padding: 5px 0;
        line-height: 22px;
    }

    .quick_bet-wrap .col-odd {
        width: 65px;
    }

    .quick_bet-wrap dd {
        padding: 7px 5px 7px 0;
    }

    .quick_bet-wrap dt, .quick_bet-wrap dd {
        box-sizing: border-box;
    }

    .slip-back dd, .slip-lay dd, .slip-book dd {
        position: relative;
        color: #243a48;
        padding: 5px 0;
        line-height: 22px;
    }

    .quick_bet-wrap .col-stake {
        width: 100px;
        padding-left: 0;
    }

    .quick_bet-wrap .col-send {
        min-width: 100px;
        width: 10%;
    }

    .quick_bet-wrap .col-stake_list {
        width: calc(100% + 7px + 2px);
        padding: 5px 0;
        margin-left: -7px;
    }

    .slip-book .col-stake_list {
        border-top: 1px solid #9fd899;
        background-color: #e4f4e2;
    }

    .sportsbook_bet .fancy-quick-tr dt span {
        display: inline-block;
    }

    .quick_bet-wrap dt .bet-check {
        font-size: 12px;
        opacity: 1;
    }

    .quick_bet-wrap .max-bet {
        margin-right: 15px;
        opacity: 1;
    }

    .quick_bet-wrap input, .quick_bet-wrap textarea {
        line-height: 33px;
        height: 33px;
    }

    .quick_bet-wrap .max-bet a {
        display: inline-block;
        width: 50px;
        height: 18px;
        background-color: rgba(0, 0, 0, 0.1);
        font-size: 11px;
        font-weight: bold;
        line-height: 18px;
        margin-right: 5px;
        border-radius: 3px;
    }

    .col-stake_list li {
        float: left;
        display: block;
    }

    .slip-back .col-stake_list li, .slip-lay .col-stake_list li, .slip-book .col-stake_list li {
        width: calc(100% / 6);
    }

    .fancy_bet .fancy-quick-tr td a, .sportsbook_bet .fancy-quick-tr td a, .bookmaker_bet .fancy-quick-tr td a {
        padding: 0;
    }

    .quick_bet-wrap .col-stake_list a {
        height: auto;
        line-height: 23px;
        font-size: 12px;
    }

    .col-stake_list a, .col-btn a {
        font-size: 11px;
        line-height: 18px;
        font-weight: normal;
        margin: 0 5px 0 0;
        background-color: white;
        border: 1px solid #bbb;
        border-radius: 4px;
        cursor: pointer;
    }

    .quick_bet-wrap .col-stake_list ul {
        width: 70%;
        padding-right: 5px;
    }

    .col-stake_list ul {
        padding-left: 5px;
        float: right;
    }

    .quick-bet-confirm li {
        width: calc(100% - 10px);
        list-style: none;
        line-height: 18px;
        color: #1e1e1e;
        padding: 0 5px;
        text-align: right;
    }

    .quick-bet-confirm {
        width: 100%;
        height: 33px;
        border-radius: 4px;
        background-color: rgba(255, 255, 255, 0.5);
        flex-direction: column;
        justify-content: center;
    }

    .quick_bet-wrap dd {
        padding: 7px 5px 7px 0;
    }

    .quick-bet-confirm .quick-bet-confirm-title {
        font-size: 10px;
        line-height: 12px;
        color: #222;
        opacity: 0.5;
    }

    .quick_bet-wrap input, .quick_bet-wrap textarea {
        line-height: 33px;
        height: 33px;
        padding: 0 6px 0 0;
        width: 100%;
        font-family: Tahoma, Helvetica, sans-serif;
        color: #1e1e1e;
        font-size: 12px;
        border: 0px #aaa solid;
        background: #fff;
        box-shadow: inset 0px 1px 0px rgb(0 0 0 / 50%);
        border-radius: 4px;
        padding: 5px;
        margin: 0 5px 5px 0;
        box-sizing: border-box;
    }

    .premium_minmax_info {
        position: absolute;
        right: 0;
        top: 20px;
        background: white;
        color: #000;
        width: 100px;
        text-align: center;
        padding: 5px;
        border-radius: 7px;
    }

    .premium_minmax_info dl, .premium_minmax_info dd {
        margin: 0;
    }

    .bets td a {
        position: relative;
        height: 35px;
        color: #1e1e1e;
        padding: 3px 0 2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-flow: column;
    }

    .bets th {
        position: relative;
        padding: 6px 10px;
        border-bottom: 1px solid #7e97a7;
    }

    .bets th p {
        /* width: 292px; */
        margin-bottom: 0;
    }
</style>
