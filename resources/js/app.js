require('./bootstrap');

import Vue from 'vue';

import 'element-ui/lib/theme-chalk/index.css'

import { Loading } from 'element-ui';
Vue.use(Loading.directive);
Vue.prototype.$loading = Loading.service;

// import VueEcho from 'vue-echo-laravel';
//
// Vue.use(VueEcho, {
//     broadcaster: 'socket.io',
//     host: 'http://127.0.0.1:6001/',
// });



import Matches from './components/Matches';
import CasinoComponent from './components/CasinoComponent';

// for server 1
import CricketOdds from './components/CricketOdds';
import CricketBookmarks from './components/CricketBookmarks';
import CricketFancy from './components/CricketFancy';
import TennisSoccerOdds from './components/TennisSoccerOdds';

// for server 2
// import CricketOdds from './components/CricketOdds2';
// import CricketBookmarks from './components/CricketBookmarks2';
// import CricketFancy from './components/CricketFancy2';
// import TennisSoccerOdds from './components/TennisSoccerOdds2';
Vue.component('matches', Matches);
Vue.component('cricketodds', CricketOdds);
Vue.component('cricketoddsbookmarks', CricketBookmarks);
Vue.component('cricketoddsfancy', CricketFancy);
Vue.component('tennissoccerodds', TennisSoccerOdds);
Vue.component('casino', CasinoComponent);
const app = new Vue({
    el: '#app',
});
