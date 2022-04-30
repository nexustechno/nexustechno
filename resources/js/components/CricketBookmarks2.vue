<template>

    <table v-if="!loading && bookmaker.length > 0" class="table custom-table inplay-table-1 w1-table cricket-table1" id="inplay-tableblock-bookmaker">
        <tbody>
        <tr>
            <td colspan="7" class="text-color-grey fancybet-block">
                <div class="dark-blue-bg-1 text-color-white p-2">
                    <a>
                        <i class="fas fa-thumbtack mr-2"></i>
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
        <template v-for="(team,index) in bookmaker">
        <tr v-if="status_b == '0' || team.status == 'CLOSED' || team.status == 'SUSPENDED' || team.status == 'SUSPEND' || team.status == 'BALL RUNNING'" class="fancy-suspend-tr" :class="'team'+(index+1)+'_bm_fancy'"><td></td> <td colspan="6" class="fancy-suspend-td"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>
        <tr :key="team.sId" class="white-bg" :class="'tr_bm_team'+(index+1)">
            <td class="padding3">
                {{ team.nat }}<br>
                <div v-if="index < 2">
                    <span :id="'team'+(index+1)+'_betBM_count_old'" :class="betTotalValue['team'+(index+1)+'_BM_total'] < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span :id="'team'+(index+1)+'_BM_total'">{{betTotalValue['team'+(index+1)+'_BM_total']}}</span>)</span>
                    <span :id="'team'+(index+1)+'_betBM_count_new'" class="towin text-color-green" style="display: none;">0.00</span>
                </div>
                <div v-else>
                    <span id="draw_betBM_count_old" :class="betTotalValue.draw_BM_total < 0 ? 'tolose text-color-red':'towin text-color-green'">(<span id="draw_BM_total">{{betTotalValue.draw_BM_total}}</span>)</span>
                    <span id="draw_betBM_count_new" class="tolose text-color-red" style="display: none;">(6.7)</span>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_back_2'">
                <div class="back-gradient text-color-black">
                    <div id="back_3" :data-team="'team'+(index+1)" class="BmBack light-blue-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="cyan-bg" data-position="2" :data-val="roundFloatVal(team.b3)"> {{ roundFloatVal(team.b3) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_back_1'">
                <div class="back-gradient text-color-black">
                    <div id="back_2" :data-team="'team'+(index+1)" class="BmBack light-blue-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)"  :data-team="'team'+(index+1)"  data-cls="cyan-bg" data-position="1" :data-val="roundFloatVal(team.b2)"> {{ roundFloatVal(team.b2) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_back_0'">
                <div class="back-gradient text-color-black">
                    <div id="back_1" :data-team="'team'+(index+1)" class="BmBack">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="cyan-bg" data-position="0" :data-val="roundFloatVal(team.b1)" class="cyan-bg"> {{ roundFloatVal(team.b1) }} <br>
                        <span>100</span></a>
                    </div>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_lay_0'">
                <div class="lay-gradient text-color-black">
                    <div id="lay_1" :data-team="'team'+(index+1)" class="BmLay pink-bg">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="pink-bg" data-position="0" :data-val="roundFloatVal(team.l1)"> {{ roundFloatVal(team.l1) }} <br>
                        <span>100</span></a>
                    </div>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_lay_1'">
                <div class="lay-gradient text-color-black">
                    <div id="lay_2" :data-team="'team'+(index+1)" class="BmLay light-pink-bg-2">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="pink-bg" data-position="1" :data-val="roundFloatVal(team.l2)"> {{ roundFloatVal(team.l2) }} <br>
                        <span>100</span></a></div>
                </div>
            </td>
            <td :class="'td_team'+(index+1)+'_bm_lay_2'">
                <div class="lay-gradient text-color-black">
                    <div id="lay_3" :data-team="'team'+(index+1)" class="BmLay light-pink-bg-3">
                        <a data-bettype="BOOKMAKER" onclick="opnForm(this)" :data-team="'team'+(index+1)" data-cls="pink-bg" data-position="2" :data-val="roundFloatVal(team.l3)"> {{ roundFloatVal(team.l3) }}
                        <br> <span>100</span></a></div>
                </div>
            </td>
        </tr>
        <tr class="mobileBack mobile_bet_model_div" id="mobile_tr" :class="'tr_team'+(index+1)+'_BM'">
            <td colspan="7" class="mobile_tr_common_class" :class="'tr_team'+(index+1)+'_BM_td_mobile'"></td>
        </tr>
        </template>
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
        props: ['event_id', 'sports_id', 'bar_image', 'pinkbg1', 'pinbg', 'pinbg1', 'bluebg1', 'min_bookmaker_limit', 'max_bookmaker_limit', 'min_bet_fancy_limit', 'max_bet_fancy_limit','bet_total','status_b'],
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
            if(this.sports_id == 4) {
                var LaravelEcho = window.Echo;
            }else{
                var LaravelEcho = window.Echo2;
            }
            LaravelEcho.channel('match-detail').listen('.' + this.event_id, (data) => {
                // this.match = data.records[0];
                var newRecords = data.records;

                if(newRecords.t2!=undefined){
                    newRecords.t2 = this.sortedArray(newRecords.t2);
                    for (var i=0;i < newRecords.t2.length;i++) {
                        //team1 spark changes
                        if(this.bookmaker[i]!=undefined) {
                            if ((this.bookmaker[i].b3 != newRecords.t2[i].b3)) {
                                $(".td_team"+(i+1)+"_bm_back_2").addClass('spark');
                            }
                            if ((this.bookmaker[i].b2 != newRecords.t2[i].b2)) {
                                $(".td_team"+(i+1)+"_bm_back_1").addClass('spark');
                            }
                            if ((this.bookmaker[i].b1 != newRecords.t2[i].b1)) {
                                $(".td_team"+(i+1)+"_bm_back_0").addClass('spark');
                            }

                            if ((this.bookmaker[i].l3 != newRecords.t2[i].l3)) {
                                $(".td_team"+(i+1)+"_bm_lay_2").addClass('sparkLay');
                            }
                            if ((this.bookmaker[i].l2 != newRecords.t2[i].l2)) {
                                $(".td_team"+(i+1)+"_bm_lay_1").addClass('sparkLay');
                            }
                            if ((this.bookmaker[i].l1 != newRecords.t2[i].l1)) {
                                $(".td_team"+(i+1)+"_bm_lay_0").addClass('sparkLay');
                            }
                        }
                    }

                    this.bookmaker = newRecords.t2;
                }
                // this.bookmaker = data.records[0].bookmaker.runners;
                this.loading = false;
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

                return arrays.sort(compare);
            },
            getPriceValue(price,type,val){
                if(price!='' && price!=undefined) {
                    if (type == 'plus') {
                        var val2 = this.roundFloatVal(price) + val;
                        if(val2 > 0){
                            return  val2;
                        }
                    } else {
                        var val2 =  this.roundFloatVal(price) - val;
                        if(val2 > 0){
                            return  val2;
                        }
                    }
                }
                return 0;
            },
            roundFloatVal(price) {
                if(price!='' && price!=undefined && price > 0 && price!='0') {
                    if(price.toString().includes('.')) {
                        return Math.round(price * 100) / 100;
                    }

                    return price;
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
