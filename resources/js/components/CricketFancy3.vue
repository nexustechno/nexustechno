<template>
    <div v-if="!loading" class="fancy-section">
        <div v-if="match.fancy!=undefined && match.fancy.length > 0" id="fancybetdiv" class="fancy-bet-txt " style="padding-top:10px">
            <div id="fancyBetHead" class="" :class="fancyType=='premium' ? 'sportsbook_bet-head':'fancy_bet-head'" style="">
                <template v-if="fancyType=='premium'">
                    <h4 @click="fancyType='premium'" class="fa-in-play">
                        <span>Premium F</span>
                        <a data-toggle="modal" data-target="#rulesFancyBetsModal" class="btn-head_rules">Rules</a>
                    </h4>
                    <a id="showSportsBookBtn" class="other-tab" style="" @click="fancyType='general'">Fancy Bet</a>
                </template>
                <template v-else-if="fancyType=='general'">
                    <h4 @click="fancyType='general'" class="fa-in-play">
                        <span>Fancy Bet</span>
                        <a data-toggle="modal" data-target="#rulesFancyBetsModal" class="btn-head_rules">Rules</a>
                    </h4>
                    <a id="showSportsBookBtn" class="other-tab" style="" @click="fancyType='premium'"><span class="tag-new">New</span>Premium F</a>
                </template>
            </div>

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

        <p v-if="match.fancy!=undefined && match.fancy.length > 0" class="fancyBetSpecialBet">
            <i class="fas fa-thumbtack"></i>
            Fancy Bet
        </p>

        <table v-if="match.fancy!=undefined && match.fancy.length > 0" class="table custom-table inplay-table w1-table " id="inplay-tableblock-fancy" style="margin-top:0px;">
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
            <template v-for="(fancy, index) in match.fancy">
                <tr :key="fancy.sid" v-if="fancy.GameStatus=='Ball Running' || fancy.GameStatus=='SUSPENDED'" :id="'tr_fancy_suspend_'+index" class="fancy-suspend-tr-1 desktop-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span class="text-uppercase">{{fancy.GameStatus}}</span>
                        </div>
                    </td>
                </tr>
                <tr class="white-bg desktop-ui-tr" :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.RunnerName }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.RunnerName" :data-target="'#runPosition'+index">
                                <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index" :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.SelectionId) }}</span>
                                    <span class="new-fancy-total collapse" :id="'New_Fancy_Total_'+index">0</span>
                                </span>
                            </a>
                        </div>

                    </td>
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index" :data-team="fancy.RunnerName" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.RunnerName" data-cls="pink-bg" :data-volume="Math.round(fancy.LaySize1)" :data-val="Math.round(fancy.LayPrice1)">{{ Math.round(fancy.LayPrice1) }}<br> <span>{{ Math.round(fancy.LaySize1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index" :data-team="fancy.RunnerName" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.RunnerName" data-cls="cyan-bg" :data-volume="Math.round(fancy.BackSize1)" :data-val="Math.round(fancy.BackPrice1)">{{ Math.round(fancy.BackPrice1) }}<br> <span>{{ Math.round(fancy.BackSize1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}}</td>
                </tr>
                <tr class="white-bg light-bg-tr-fancy mobile-ui-tr collapse light-bg-tr-fancy" :class="'tr_fancy_'+index">
                    <td colspan="3"><b>{{ fancy.RunnerName }}</b>
                        <div>
                            <a class="openfancymodel_dynamic fancy-calculation-exposer" :data-fancy-name="fancy.RunnerName" :data-target="'#runPosition'+index">
                                 <span :class="'fancy_total'+index">
                                    <span class="fancy-total-amount tolose text-color-red" :id="'Fancy_Total_'+index" :class="'Fancy_Total_'+index">{{ getFancyBetValue(fancy.SelectionId) }}</span>
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
                    <td class="pink-bg back1btn text-center FancyLay" :class="'td_fancy_lay_'+index" :data-team="fancy.RunnerName" :id="'td_fancy_lay_'+index" onClick="colorclick(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.RunnerName" data-cls="pink-bg" :data-volume="Math.round(fancy.LaySize1)" :data-val="Math.round(fancy.LayPrice1)">{{ Math.round(fancy.LayPrice1) }}<br> <span>{{ Math.round(fancy.LaySize1) }}</span></a></td>
                    <td class="lay1btn cyan-bg text-center FancyBack" :class="'td_fancy_back_'+index" :data-team="fancy.RunnerName" :id="'td_fancy_back_'+index" onClick="colorclickback(this.id)">
                        <a data-bettype="SESSION" onclick="opnForm(this)" :data-position="index" :data-team="fancy.RunnerName" data-cls="cyan-bg" :data-volume="Math.round(fancy.BackSize1)" :data-val="Math.round(fancy.BackPrice1)">{{ Math.round(fancy.BackPrice1) }}<br> <span>{{ Math.round(fancy.BackSize1) }}</span></a>
                    </td>
                    <td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> {{min_bet_fancy_limit}} / {{max_bet_fancy_limit}}</td>
                </tr>
                <tr v-if="getFancyStatus(fancy.GameStatus,index)" :id="'tr_fancy_suspend_'+index" class="fancy-suspend-tr-1 mobile-ui-tr team_session_fancy">
                    <td colspan="3"></td>
                    <td colspan="2" class="fancy-suspend-td-1">
                        <div class="fancy-suspend-1 black-bg-5 text-color-white">
                            <span class="text-uppercase">{{fancy.GameStatus}}</span>
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
        props: ['event_id', 'sports_id', 'bar_image', 'clockgreenicon', 'infoicon','min_bet_fancy_limit', 'max_bet_fancy_limit','pinkbg1_fancy','bluebg1_fancy','bet_total'],
        data() {
            return {
                match: [],
                fancyType:'general',
                loading:true,
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
                this.loading = false;
                if(data.records[0].fancy!=undefined) {
                    var records = data.records[0];
                    if(data.records[0].fancy.length > 0 && data.records[0].fancy[0].SelectionId != undefined) {
                        records.fancy = this.sortedArray(data.records[0].fancy);
                    }else {
                        records.fancy = [];
                    }
                    this.match = records;
                }else{
                    this.match = data.records[0];
                }
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
                    if (a.SelectionId < b.SelectionId)
                        return -1;
                    if (a.SelectionId > b.SelectionId)
                        return 1;
                    return 0;
                }

                var fancies =  arrays.sort(compare);
                const duplciates = [];
                return  fancies.filter((c, index) => {

                    if(duplciates.indexOf(c.RunnerName) == -1){
                        duplciates.push(c.RunnerName);
                        return true;
                    }else{
                        return false;
                    }
                });
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
        filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.6));
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
    .fancy-section .fancy-bet-txt h4{
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
        background-image: linear-gradient( -180deg, #0A92A5 0%, #087989 82%);
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
</style>
