<template>

    <table v-if="!loading && bookmaker.length > 0" class="table custom-table inplay-table-1 w1-table cricket-table1" id="inplay-tableblock-bookmaker">
        <tbody v-if="match.marketId!=undefined">
        <tr>
            <td colspan="7" class="text-color-grey fancybet-block">
                <div class="dark-blue-bg-1 text-color-white">
                    <a>
                        <img :src="pinbg">
                        <img :src="pinbg" class="hover-img">
                    </a>
                    Bookmaker Market <span class="zeroopa">| Zero Commission</span>
                    <div class="mobile-bookmark-min-max-popup">
                        <a href="#feeds_for_bookmarket" data-toggle="collapse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15">
                                <path fill="%233B5160" fill-rule="evenodd"
                                      d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"></path>
                            </svg>
                        </a>
                        <div id="feeds_for_bookmarket" class="collapse fancy_minmax_info text-let">
                            <dl class="text-center">
                                <dt>Min / Max</dt>
                                <dd id="minMax"> {{min_bookmaker_limit}} / {{ max_bookmaker_limit }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="bets-fancy white-bg d-none d-lg-table-row">
            <td colspan="3" style="width: 170px;">
                <div class="minmax-txt minmaxmobile">
                    <span>Min</span>
                    <span id="div_min_bet_odds_limit" class="bookmakerMin">{{min_bookmaker_limit}}</span>
                    <span>Max</span>
                    <span id="div_max_bet_odds_limit" class="bookmakerMax">{{ max_bookmaker_limit }}</span>
                </div>
            </td>
            <td><a class="backall"> <img :src="bluebg1"> <span>Back</span></a></td>
            <td><a class="layall"><img :src="pinkbg1"> <span>Lay</span></a></td>
            <td colspan="2"></td>
        </tr>
        <tr class="bets-fancy white-bg d-lg-none">
            <td style="width: 170px;"></td>
            <td><a class="backall"> <img :src="bluebg1" style="width: 100%; height: 25px;"> <span> Back</span></a></td>
            <td><a class="layall"><img :src="pinkbg1" style="width: 100%; height: 25px;"> <span>Lay</span></a></td>
            <td colspan="2"></td>
        </tr>
        <tr v-if="bookmaker[0].status == 'SUSPENDED'" class="fancy-suspend-tr team1_bm_fancy"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>
        <tr class="white-bg tr_bm_team1">
            <td class="padding3 77777777">{{ bookmaker[0].runnerName }}<br>
                <div><span id="team1_betBM_count_old" :class="betTotalValue.team1_BM_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="team1_BM_total">{{ betTotalValue.team1_BM_total }}</span>)</span> <span id="team1_betBM_count_new" class="tolose text-color-red" style="display: none;">(6.7)</span></div>
            </td>
            <td class="td_team1_bm_back_2">
                <div class="back-gradient text-color-black">
                    <div id="back_3" data-team="team1" class="BmBack light-blue-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team1" data-cls="cyan-bg" data-position="2" :data-val="getPriceValue(bookmaker[0].rate1,'minus',2)"> {{ getPriceValue(bookmaker[0].rate1,'minus',2) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team1_bm_back_1">
                <div class="back-gradient text-color-black">
                    <div id="back_2" data-team="team1" class="BmBack light-blue-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)"  data-team="team1"  data-cls="cyan-bg" data-position="1" :data-val="getPriceValue(bookmaker[0].rate1,'minus',1)"> {{ getPriceValue(bookmaker[0].rate1,'minus',1) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team1_bm_back_0">
                <div class="back-gradient text-color-black">
                    <div id="back_1" data-team="team1" class="BmBack">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team1" data-cls="cyan-bg" data-position="0" :data-val="roundFloatVal(bookmaker[0].rate1)" class="cyan-bg"> {{ roundFloatVal(bookmaker[0].rate1) }} <br>
                        <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team1_bm_lay_0">
                <div class="lay-gradient text-color-black">
                    <div id="lay_1" data-team="team1" class="BmLay pink-bg">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team1" data-cls="pink-bg" data-position="0" :data-val="roundFloatVal(bookmaker[0].rate2)"> {{ roundFloatVal(bookmaker[0].rate2) }} <br>
                        <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team1_bm_lay_1">
                <div class="lay-gradient text-color-black">
                    <div id="lay_2" data-team="team1" class="BmLay light-pink-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team1" data-cls="pink-bg" data-position="1" :data-val="getPriceValue(bookmaker[0].rate2,'plus',1)"> {{ getPriceValue(bookmaker[0].rate2,'plus',1) }}  <br>
                        <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team1_bm_lay_2">
                <div class="lay-gradient text-color-black">
                    <div id="lay_3" data-team="team1" class="BmLay light-pink-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team1" data-cls="pink-bg" data-position="2" :data-val="getPriceValue(bookmaker[0].rate2,'plus',2)"> {{ getPriceValue(bookmaker[0].rate2,'plus',2) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
        </tr>
        <tr class="mobileBack tr_team1_BM mobile_bet_model_div" id="mobile_tr">
            <td colspan="7" class="tr_team1_BM_td_mobile mobile_tr_common_class"></td>
        </tr>
        <tr v-if="bookmaker[1].status == 'SUSPENDED'" class="fancy-suspend-tr team2_bm_fancy"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>
        <tr class="white-bg tr_bm_team2">
            <td class="padding3 77777777">{{ bookmaker[1].runnerName }}<br>
                <div><span id="team2_betBM_count_old" :class="betTotalValue.team2_BM_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="team2_BM_total">{{ betTotalValue.team2_BM_total }}</span>)</span> <span
                    id="team2_betBM_count_new" class="tolose text-color-red" style="display: none;">(6.7)</span></div>
            </td>
            <td class="td_team2_bm_back_2">
                <div class="back-gradient text-color-black">
                    <div id="back_3" data-team="team2" class="BmBack light-blue-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2" data-cls="cyan-bg" data-position="2" :data-val="getPriceValue(bookmaker[1].rate1,'minus',2)"> {{ getPriceValue(bookmaker[1].rate1,'minus',2) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team2_bm_back_1">
                <div class="back-gradient text-color-black">
                    <div id="back_2" data-team="team2" class="BmBack light-blue-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2"  data-cls="cyan-bg" data-position="1" :data-val="getPriceValue(bookmaker[1].rate1,'minus',1)"> {{ getPriceValue(bookmaker[1].rate1,'minus',1) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team2_bm_back_0">
                <div class="back-gradient text-color-black">
                    <div id="back_1" data-team="team2" class="BmBack">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2" data-cls="cyan-bg" data-position="0" :data-val="roundFloatVal(bookmaker[1].rate1)" class="cyan-bg"> {{ roundFloatVal(bookmaker[1].rate1) }} <br>
                            <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team2_bm_lay_0">
                <div class="lay-gradient text-color-black">
                    <div id="lay_1" data-team="team2" class="BmLay pink-bg">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2" data-cls="pink-bg" data-position="0" :data-val="roundFloatVal(bookmaker[1].rate2)"> {{ roundFloatVal(bookmaker[1].rate2) }} <br>
                            <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team2_bm_lay_1">
                <div class="lay-gradient text-color-black">
                    <div id="lay_2" data-team="team2" class="BmLay light-pink-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2" data-cls="pink-bg" data-position="1" :data-val="getPriceValue(bookmaker[1].rate2,'plus',1)"> {{ getPriceValue(bookmaker[1].rate2,'plus',1) }}  <br>
                            <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team2_bm_lay_2">
                <div class="lay-gradient text-color-black">
                    <div id="lay_3" data-team="team2" class="BmLay light-pink-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team2" data-cls="pink-bg" data-position="2" :data-val="getPriceValue(bookmaker[1].rate2,'plus',2)"> {{ getPriceValue(bookmaker[1].rate2,'plus',2) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
        </tr>
        <tr class="mobileBack tr_team2_BM mobile_bet_model_div" id="mobile_tr">
            <td colspan="7" class="tr_team2_BM_td_mobile mobile_tr_common_class"></td>
        </tr>
        <tr v-if="bookmaker[2]!=undefined && bookmaker[2].status == 'SUSPENDED'" class="fancy-suspend-tr team3_bm_fancy"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>
        <tr v-if="bookmaker[2]!=undefined" class="white-bg tr_bm_team3">
            <td class="padding3 77777777">{{ bookmaker[2].runnerName }}<br>
                <div><span id="draw_betBM_count_old" :class="betTotalValue.draw_BM_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="draw_BM_total">{{betTotalValue.draw_BM_total}}</span>)</span>
                    <span id="draw_betBM_count_new" class="tolose text-color-red" style="display: none;">(6.7)</span>
                </div>
            </td>
            <td class="td_team3_bm_back_2">
                <div class="back-gradient text-color-black">
                    <div id="back_3" data-team="team3" class="BmBack light-blue-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3" data-cls="cyan-bg" data-position="2" :data-val="getPriceValue(bookmaker[2].rate1,'minus',2)"> {{ getPriceValue(bookmaker[2].rate1,'minus',2) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team3_bm_back_1">
                <div class="back-gradient text-color-black">
                    <div id="back_2" data-team="team3" class="BmBack light-blue-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3"  data-cls="cyan-bg" data-position="1" :data-val="getPriceValue(bookmaker[2].rate1,'minus',1)"> {{ getPriceValue(bookmaker[2].rate1,'minus',1) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team3_bm_back_0">
                <div class="back-gradient text-color-black">
                    <div id="back_1" data-team="team3" class="BmBack">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3" data-cls="cyan-bg" data-position="0" :data-val="roundFloatVal(bookmaker[2].rate1)" class="cyan-bg"> {{ roundFloatVal(bookmaker[2].rate1) }} <br>
                            <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team3_bm_lay_0">
                <div class="lay-gradient text-color-black">
                    <div id="lay_1" data-team="team3" class="BmLay pink-bg">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3" data-cls="pink-bg" data-position="0" :data-val="roundFloatVal(bookmaker[2].rate2)"> {{ roundFloatVal(bookmaker[2].rate2) }} <br>
                            <span>100</span></a>
                    </div>
                </div>
            </td>
            <td class="td_team3_bm_lay_1">
                <div class="lay-gradient text-color-black">
                    <div id="lay_2" data-team="team3" class="BmLay light-pink-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3" data-cls="pink-bg" data-position="1" :data-val="getPriceValue(bookmaker[2].rate2,'plus',1)"> {{ getPriceValue(bookmaker[2].rate2,'plus',1) }}  <br>
                            <span>100</span></a></div>
                </div>
            </td>
            <td class="td_team3_bm_lay_2">
                <div class="lay-gradient text-color-black">
                    <div id="lay_3" data-team="team3" class="BmLay light-pink-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" data-team="team3" data-cls="pink-bg" data-position="2" :data-val="getPriceValue(bookmaker[2].rate2,'plus',2)"> {{ getPriceValue(bookmaker[2].rate2,'plus',2) }}
                            <br> <span>100</span></a></div>
                </div>
            </td>
        </tr>
        <tr v-if="bookmaker[2]!=undefined" class="mobileBack tr_team3_BM mobile_bet_model_div" id="mobile_tr">
            <td colspan="7" class="tr_team3_BM_td_mobile mobile_tr_common_class"></td>
        </tr>
        </tbody>
    </table>

<!--    <table class="table custom-table inplay-table-1 w1-table cricket-table1" id="inplay-tableblock-bookmaker" v-else-if="loading">-->
<!--        <tr>-->
<!--            <td colspan="7">-->
<!--                <p class="text-center font-weight-bold">Loading Bookmark Data...</p>-->
<!--            </td>-->
<!--        </tr>-->
<!--    </table>-->
</template>

<script>
    export default {
        props: ['event_id', 'bar_image', 'pinkbg1', 'pinbg', 'pinbg1', 'bluebg1', 'min_bookmaker_limit', 'max_bookmaker_limit', 'min_bet_fancy_limit', 'max_bet_fancy_limit','bet_total'],
        data() {
            return {
                match: [],
                bookmaker: [],
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
                this.match = data.records[0];
                this.bookmaker = data.records[0].bookmaker.runners;
                this.loading = false;
                // console.log("match ",data.records)
            });
        },
        methods: {
            getPriceValue(price,type,val){
                if(price!='' && price!=undefined) {
                    if(type == 'plus'){
                        return this.roundFloatVal(price) + val;
                    }else{
                        return this.roundFloatVal(price) - val;
                    }
                }

                return 0;
            },
            roundFloatVal(price) {
                if(price!='' && price!=undefined) {
                    var num2 = price.split(".");
                    var num = num2[1];
                    var m = Number((Math.abs(num) * 100).toPrecision(15));
                    return Math.round(m) / 100 * Math.sign(num);
                }
                return 0;
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
