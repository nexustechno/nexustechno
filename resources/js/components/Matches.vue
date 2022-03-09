<template>
    <div class="matches-section" v-if="!loading">
        <template v-if="matches.length > 0">
            <div class="secondblock-cricket white-bg" v-for="(match, index) in matches" v-if="isVisible(match.gameId,match.eventName,match.inPlay,index)" :key="matchtype+index">
            <div class="mblinplay">
                <span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white" v-if="matchtype == 4 && match.f == 'True'">F</span>
                <span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white" v-if="matchtype == 4 && match.m1 == 'True'">B</span>
                <span style="color:green" class="mplay" v-if="match.inPlay == 'True'">In-Play</span>
            </div>
            <span class="desk" :class="match.inPlay == 'True' ? 'fir-col1-green':'fir-col1'">
                <a :href="'/matchDetail/'+match.gameId" class="text-color-blue-light">
                    {{ getMatchName(match.eventName) }}
                    <span style="color:green" v-if="match.inPlay == 'True'" class="deskinplay">In-Play</span>
                </a>
                <div class="mobileDate" v-if="match.inPlay != 'True'">{{ getMatchDate(match.eventName) }}</div>
                <span v-if="matchtype == 4 && match.f == 'True'" style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>
                <span v-if="matchtype == 4 && match.m1 == 'True'" style="color:green;margin-right: 40px;" class="game-fancy in-play blue-bg-3 text-color-white">B</span>
            </span>
            <span class="fir-col2">
                <a class="backbtn lightblue-bg2" v-if="match.back1 > 0">{{ match.back1 }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.lay1 > 0">{{ match.lay1 }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>
            <span class="fir-col2">
                 <a class="backbtn lightblue-bg2" v-if="match.back12 > 0">{{ match.back12 }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.lay12 > 0">{{ match.lay12 }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>
            <span class="fir-col2">
                 <a class="backbtn lightblue-bg2" v-if="match.back11 > 0">{{ match.back11 }}</a>
                <a class="backbtn lightblue-bg2" v-else>--</a>
                <a class="laybtn lightpink-bg1" v-if="match.lay11 > 0">{{ match.lay11 }}</a>
                <a class="laybtn lightpink-bg1" v-else>--</a>
            </span>
            <span class="fir-col3 text-center">
                <a :data-id="matchesArray[match.gameId]" class="cricket-pin make-fav-match1 " @click="makeFav(matchesArray[match.gameId])" :class="'pin_'+matchesArray[match.gameId]">
                    <span v-if="isFav(match.gameId)">
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
    <div class="matches-section p-5" v-else v-loading="loading"></div>
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
                    this.matches = data.records;
                    this.loading = false;
                });
            }else if(this.matchtype == 2){
                this.broadcastEvent = "tennis";
                window.Echo.channel('matches').listen('.tennis', (data) => {
                    this.matches = data.records;
                    this.loading = false;
                });
            }else if(this.matchtype == 1){
                this.broadcastEvent = "soccer";
                window.Echo.channel('matches').listen('.soccer', (data) => {
                    this.matches = data.records;
                    this.loading = false;
                });
            }
        },
        methods:{
            makeFav(matchId){
                var form = {
                    id:matchId
                }
                axios.post('/user/fav-match', form)
                    .then((res) => {
                        //Perform Success Action
                        console.log(res);
                        if(res.data.result == 'added') {
                            this.favMatchesArray.push(matchId);
                        }else if(res.data.result == 'remove'){
                            this.favMatchesArray.push(matchId);
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
            isVisible(gameId, eventName, inPlay, index){

                var valReturn = false;

                if(this.matchtype!=4) {
                    const res = gameId.startsWith("3");
                    if(res == false){
                        return false;
                    }
                }

                if(this.filtertype!=undefined){
                    if (this.matchesArray[gameId] != undefined && this.matchesArray[gameId] != null) {
                        // console.log("checking ",this.matchesArray[gameId])

                        if(this.filtertype!='inplay') {
                            var eventNameString = eventName.split('/');
                            var eventNameString2 = eventNameString[1].split(this.year);
                            var mDate = eventNameString2[0]+ " "+this.year;

                            var date = new Date(mDate.trim());
                            var ye2 = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
                            var mo2 = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(date);
                            var da2 = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                            var matchDate = `${da2}-${mo2}-${ye2}`;
                        }

                        if (this.filtertype == 'inplay' && inPlay == 'True') {
                            valReturn =  true;
                        }

                        if (this.filtertype == 'today' && matchDate == this.todaydate && inPlay != 'True') {
                            valReturn =  true;
                        }

                        if (this.filtertype == 'tomorrow' && matchDate == this.tomorrowdate && inPlay != 'True') {
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
            getMatchDate(eventName){
                var eventNameString = eventName.split('/');
                var eventNameString2 = eventNameString[1].split(this.year);

                if(eventNameString2[0] == undefined){
                    return '';
                }

                var mDate = eventNameString2[0]+ " "+this.year;

                var date = new Date(mDate.trim());
                var ye2 = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
                var mo2 = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(date);
                var da2 = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                var matchDate = `${da2}-${mo2}-${ye2}`;

                var time = eventNameString2[1];
                time = time.replace("(IST)",'');
                if(matchDate == this.todaydate){
                    return time.trim();
                }else if(matchDate == this.tomorrowdate){
                    return 'Tomorrow '+time.trim();
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
