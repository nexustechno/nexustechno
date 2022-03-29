<template>
    <div class="matches-section" v-if="!loading">
        <template v-if="matches.length > 0">
            <div class="secondblock-cricket white-bg" v-for="(match, index) in matches" v-if="isVisible(match,index)" :key="matchtype+index">
            <div class="mblinplay">
<!--                <span v-if="matchtype == 4 && match.f == 'True'" style="color:green" class="game-fancy game-f in-play blue-bg-3 text-color-white"></span>-->
<!--                <span v-if="matchtype == 4 && match.m1 == 'True'" class="game-bookmaker in-play game-fancy" id="bookMakerIcon" style="cursor: pointer; display: inline-flex;"></span>-->
<!--                <span style="color:green" class="mplay" v-if="match.inPlay == 'True'">In-Play</span>-->
            </div>
            <span class="desk" :class="match.markets[0].inPlay == 1 ? 'fir-col1-green':'fir-col1'">
                <a :href="'/matchDetail/'+match.id" class="text-color-blue-light">
                    {{ match.name }}
                </a>
                <span style="color:green" v-if="match.markets[0].inPlay == 1" class="deskinplay">In-Play</span>
                <span class="game-live" v-if="match.markets[0].inPlay == 1 && match.isStreamingOpenTime == 1" id="streamingIcon" style="display: inline-flex;"></span>
                <div class="mobileDate" v-if="match.markets[0].inPlay != 1">{{ getMatchDate(match.openDate) }}</div>
                <span v-if="matchtype == 4 && (match.hasFancyBetMarkets == true || match.hasInPlayFancyBetMarkets == true)" style="color:green" class="game-fancy game-f in-play blue-bg-3 text-color-white"></span>
                <span v-if="matchtype == 4 && (match.hasBookMakerMarkets == true || match.hasInPlayBookMakerMarkets == true)" class="game-bookmaker in-play game-fancy" id="bookMakerIcon" style="cursor: pointer; display: inline-flex;"></span>
            </span>

<!--            <span class="fir-col23" :class="'col3-back-lay'+match.id" v-if="match.markets[0].selections.length > 0">-->
<!--                <pre>{{ match.markets[0].selections[0].availableToBack[0].price }}</pre>-->
<!--                <pre>{{ match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToBack[0].price }}</pre>-->
<!--            </span>-->
            <span class="fir-col2" :class="'col3-back-lay'+match.id">
                <a class="backbtn lightblue-bg2" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1]!=undefined && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToBack[0]!=undefined && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToBack[0].price > 0">{{ match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToBack[0].price }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1]!=undefined && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToLay[0]!=undefined && match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToLay[0].price > 0">{{ match.markets[0].selections[parseInt(match.markets[0].numberOfRunners)-1].availableToLay[0].price }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>

            <span class="fir-col2" :class="'col2-back-lay'+match.id" v-if="match.markets[0].numberOfRunners >=3">
                <a class="backbtn lightblue-bg2" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[1]!=undefined && match.markets[0].selections[1].availableToBack[0]!=undefined && match.markets[0].selections[1].availableToBack[0].price > 0">{{ match.markets[0].selections[1].availableToBack[0].price }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[1]!=undefined && match.markets[0].selections[1].availableToLay[0]!=undefined && match.markets[0].selections[1].availableToLay[0].price > 0">{{ match.markets[0].selections[1].availableToLay[0].price }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>
            <span class="fir-col2" :class="'col2-back-lay'+match.id" v-if="match.markets[0].numberOfRunners <=2">
                <a class="backbtn lightblue-bg2">--</a>
                <a class="laybtn lightpink-bg1">--</a>
            </span>

            <span class="fir-col2" :class="'col1-back-lay'+match.id">
                <a class="backbtn lightblue-bg2" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[0]!=undefined && match.markets[0].selections[0].availableToBack!=undefined && match.markets[0].selections[0].availableToBack[0]!=undefined && match.markets[0].selections[0].availableToBack[0].price > 0">{{ match.markets[0].selections[0].availableToBack[0].price }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.markets[0].totalMatched >= 0 && match.markets[0].selections.length > 0 && match.markets[0].selections[0]!=undefined && match.markets[0].selections[0].availableToLay!=undefined && match.markets[0].selections[0].availableToLay[0]!=undefined && match.markets[0].selections[0].availableToLay[0].price > 0">{{ match.markets[0].selections[0].availableToLay[0].price }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>


            <span class="fir-col3 text-center">
                <a :data-id="matchesArray[match.id]" class="cricket-pin make-fav-match1 " @click="makeFav(matchesArray[match.id])" :class="'pin_'+matchesArray[match.id]">
                    <span v-if="isFav(match.id)">
                        <img class="pin-img hover-img" :src="roundpin">
                        <img class="unpin-img" :src="roundpin1">
                    </span>
                    <span v-else>
                        <img class="unpin-img" :src="roundpin">
                        <img class="pin-img hover-img" :src="roundpin1">
                    </span>
                </a>
            </span>
        </div>
        </template>
        <div class="text-center p-5" v-else>
            <p class="m-0">No data found</p>
        </div>
    </div>
    <div class="matches-section p-5" v-else>
        <div id="site_bet_loading1" class="betloaderimage1 loader-style1">
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
    import moment from 'moment';

    export default {
        props: ['displaymatches','matchtype','favmatches','filtertype','todaydate','tomorrowdate','year','roundpin','roundpin1'],
        data() {
            return {
                matches: [],
                broadcastEvent:'',
                favMatchesArray:[],
                visibleRecordsCount:0,
                loading:true
            };
        },
        computed: {
            // a computed getter
            matchesArray: function () {
                // `this` points to the vm instance
                return this.displaymatches;
            },
        },
        mounted() {

            this.favMatchesArray = this.favmatches;
            if(this.matchtype == 4){
                this.broadcastEvent = "cricket"
                window.Echo.channel('matches').listen('.cricket', (data) => {

                    this.setRecords(data.records);

                    // this.matches = data.records;
                    this.loading = false;
                });
            }else if(this.matchtype == 2){
                this.broadcastEvent = "tennis";
                window.Echo.channel('matches').listen('.tennis', (data) => {
                    // this.matches = data.records;
                    this.setRecords(data.records);
                    this.loading = false;
                });
            }else if(this.matchtype == 1){
                this.broadcastEvent = "soccer";
                window.Echo.channel('matches').listen('.soccer', (data) => {
                    // this.matches = data.records;
                    this.setRecords(data.records);
                    this.loading = false;
                });
            }
        },
        methods:{
            setRecords(data){
                var records = data.events;
                for(var i=0;i<records.length;i++){
                    if(this.isVisible(records[i],i)){

                        if(this.matches[i]!=undefined) {

                            for (var j=0;j<records[i].markets[0].selections.length;j++){
                                if (records[i].markets[0].selections.length > 0 && records[i].markets[0].selections[j]!=undefined && records[i].markets[0].selections[j].availableToBack!=undefined && records[i].markets[0].selections[j].availableToBack[0]!=undefined && this.matches[i].markets[0].selections.length > 0 && this.matches[i].markets[0].selections[j]!=undefined && this.matches[i].markets[0].selections[j].availableToBack!=undefined && this.matches[i].markets[0].selections[j].availableToBack[0]!=undefined && this.matches[i].markets[0].selections[j].availableToBack[0].price != records[i].markets[0].selections[j].availableToBack[0].price) {
                                    $(".col1-back-lay" + records[i].id + " .backbtn").addClass('spark');
                                }
                                if (records[i].markets[0].selections.length > 0 && records[i].markets[0].selections[j]!=undefined && records[i].markets[0].selections[j].availableToLay!=undefined && records[i].markets[0].selections[j].availableToLay[0]!=undefined && this.matches[i].markets[0].selections.length > 0 && this.matches[i].markets[0].selections[j]!=undefined && this.matches[i].markets[0].selections[j].availableToLay!=undefined && this.matches[i].markets[0].selections[j].availableToLay[0]!=undefined && this.matches[i].markets[0].selections[j].availableToLay[0].price != records[i].markets[0].selections[j].availableToLay[0].price) {
                                    $(".col1-back-lay" + records[i].id + " .laybtn").addClass('sparkLay');
                                }
                            }
                        }
                    }
                }

                this.matches = records;
            },
            makeFav(matchId){
                var form = {
                    id:matchId
                }
                axios.post('/user/fav-match', form)
                    .then((res) => {
                        //Perform Success Action
                        // console.log(res);
                        if(res.data.result == 'added') {
                            this.favMatchesArray.push(matchId);
                        }else if(res.data.result == 'remove'){
                            var index = this.favMatchesArray.indexOf(matchId);
                            if (index > -1) {
                                this.favMatchesArray.splice(index, 1); // 2nd parameter means remove one item only
                            }
                        }else if(res.data.result == 'login'){
                            $("#myLoginModal").modal('show');
                        }
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                    }).finally(() => {
                    //Perform action in always
                });
            },
            isFav(gameId){
                var matchId = this.matchesArray[gameId]
                if(this.favMatchesArray.length > 0 && this.favMatchesArray.indexOf(matchId) >=0 ){
                    return true;
                }

                return false;
            },
            isVisible(match, index){

                var valReturn = false;

                var gameId = match.id;
                var openDate = match.openDate;
                var inPlay = 0;
                if(match.markets[0]!=undefined) {
                    inPlay = match.markets[0].inPlay;
                }

                if(this.filtertype!=undefined){
                    if (this.matchesArray[gameId] != undefined && this.matchesArray[gameId] != null)
                    {
                        // console.log("checking ",this.matchesArray[gameId])

                        if(this.filtertype!='inplay') {
                            // var eventNameString = eventName.split('/');
                            // var eventNameString2 = eventNameString[1].split(this.year);
                            var mDate = openDate;

                            var date = new Date(mDate.trim());
                            var ye2 = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
                            var mo2 = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(date);
                            var da2 = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                            var matchDate = `${da2}-${mo2}-${ye2}`;
                        }

                        if (this.filtertype == 'inplay' && inPlay == 1) {
                            valReturn =  true;
                        }

                        if (this.filtertype == 'today' && matchDate == this.todaydate && inPlay != 1) {
                            valReturn =  true;
                        }

                        if (this.filtertype == 'tomorrow' && matchDate == this.tomorrowdate && inPlay != 1) {
                            valReturn =  true;
                        }
                    }
                }
                else {
                    if (this.matchesArray[gameId] != undefined && this.matchesArray[gameId] != null) {
                        valReturn =  true;
                    }
                }

                // if((index+1) == this.matches.length && valReturn == false)
                // {
                //     // console.log("matches-section1", this.matches.length," == ",index);
                //
                //     if(this.filtertype!=undefined){
                //         if(this.filtertype=='inplay') {
                //             if(this.matchtype == 4) {
                //                 setTimeout(()=>{
                //                     if ($("#inplay #inplay-cricket-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#inplay #inplay-cricket-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No cricket data found</p></div>')
                //                     } else {
                //                         $("#inplay #inplay-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 2){
                //                 setTimeout(()=> {
                //                     if ($("#inplay #inplay-tennis-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#inplay #inplay-tennis-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No tennis data found</p></div>')
                //                     } else {
                //                         $("#inplay #inplay-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 1){
                //                 setTimeout(()=> {
                //                     if ($("#inplay #inplay-soccer-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#inplay #inplay-soccer-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No soccer data found</p></div>')
                //                     } else {
                //                         $("#inplay #inplay-soccer-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }
                //         }else if(this.filtertype=='today') {
                //             if(this.matchtype == 4) {
                //                 setTimeout(()=>{
                //                     if ($("#today #today-cricket-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#today #today-cricket-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No cricket data found</p></div>')
                //                     } else {
                //                         $("#today #today-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 2){
                //                 setTimeout(()=> {
                //                     if ($("#today #today-tennis-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#today #today-tennis-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No tennis data found</p></div>')
                //                     } else {
                //                         $("#today #today-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 1){
                //                 setTimeout(()=> {
                //                     if ($("#today #today-soccer-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#today #today-soccer-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No soccer data found</p></div>')
                //                     } else {
                //                         $("#today #today-soccer-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }
                //         }else if(this.filtertype=='tomorrow') {
                //             if(this.matchtype == 4) {
                //                 setTimeout(()=>{
                //                     if ($("#tomorrow #tmr-cricket-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#tomorrow #tmr-cricket-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No cricket data found</p></div>')
                //                     } else {
                //                         $("#tomorrow #tmr-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 2){
                //                 setTimeout(()=> {
                //                     if ($("#tomorrow #tmr-tennis-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#tomorrow #tmr-tennis-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No tennis data found</p></div>')
                //                     } else {
                //                         $("#tomorrow #tmr-cricket-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }else if(this.matchtype == 1){
                //                 setTimeout(()=> {
                //                     if ($("#tomorrow #tmr-soccer-collapse .programe-setcricket .matches-section .secondblock-cricket.white-bg").length == 0) {
                //                         $("#tomorrow #tmr-soccer-collapse .programe-setcricket .matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No soccer data found</p></div>')
                //                     } else {
                //                         $("#tomorrow #tmr-soccer-collapse .programe-setcricket .matches-section .no-data-found").remove();
                //                     }
                //                 },100)
                //             }
                //         }
                //     }else {
                //         setTimeout(()=> {
                //             if ($(".matches-section .secondblock-cricket.white-bg").length == 0) {
                //                 $(".matches-section").html('<div class="no-data-found text-center p-5"><p class="m-0">No data found</p></div>')
                //             } else {
                //                 $(".matches-section .no-data-found").remove();
                //             }
                //         },100);
                //     }
                // }

                return valReturn;
            },
            getMatchDate(date){
                var eventNameString = date.split(' ');
                // var eventNameString2 = eventNameString[1].split(this.year);
                //
                // if(eventNameString2[0] == undefined){
                //     return '';
                // }

                var mDate = eventNameString[0];

                var date = new Date(mDate.trim());
                var ye2 = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
                var mo2 = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(date);
                var da2 = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                var matchDate = `${da2}-${mo2}-${ye2}`;

                var time = eventNameString[1];
                time = time.replace("(IST)",'');
                if(matchDate == this.todaydate){
                    return time.trim();
                }else if(matchDate == this.tomorrowdate){
                    return 'Tomorrow '+time.trim();
                }else{
                    return matchDate +" "+time.trim();
                }

                return  '';

            },
            getMatchName(eventName){
                var eventNameString = eventName.split('/');
                return eventNameString[0];
            }
        }
    };
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
        position: relative !important;
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
