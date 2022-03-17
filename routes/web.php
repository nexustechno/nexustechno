<?php

use Illuminate\Support\Facades\Route;

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|
*/

Route::group(['middleware' => ['nexusapi','prevent-back-history']], function () {


    Route::get('/', 'FrontController@index')->name('front');
    Route::get('hirarchy_insert', function () {
        return view('hirarchy_insert');
    });
    Route::get('frontLogout', 'FrontController@frontLogout')->name('frontLogout');
    Route::post('multiaccountlogout', 'FrontController@multiaccountlogout')->name('multiaccountlogout');
    Route::get('userLogin', 'PlayerController@userLogin')->name('userLogin');
    Route::post('frontLogin', 'PlayerController@frontLogin')->name('frontLogin');
    Route::post('frontLogin_popup', 'PlayerController@frontLogin_popup')->name('frontLogin_popup');
    Route::get('change_pass_pl', 'PlayerController@change_pass_pl')->name('change_pass_pl');
    Route::post('updatePasswordPL/{id}', 'PlayerController@updatePasswordPL')->name('updatePasswordPL');
    Route::post('changePassLogoutuser', 'PlayerController@changePassLogoutuser')->name('changePassLogoutuser');
    Route::get('get-curl', 'SettingController@getCURL');
    Route::get('myaccount', 'FrontController@myaccount')->name('myaccount');
    Route::post('matchDeclareRedirect', 'PlayerController@matchDeclareRedirect')->name('matchDeclareRedirect');
    Route::get('matchDetail/{id}', 'FrontController@matchDetail')->name('matchDetail')->middleware('nexusapi');
    Route::post('getmatchdetails', 'FrontController@getmatchdetails')->name('getmatchdetails');
    Route::post('getmatchdetailTwo', 'FrontController@getmatchdetailTwo')->name('getmatchdetailTwo');
    Route::post('matchCall/{matchId}', 'FrontController@matchCall')->name('matchCall');
    Route::post('matchCallForFancyNBM/{matchId}', 'FrontController@matchCallForFancyNBM')->name('matchCallForFancyNBM');
    Route::post('matchCallOdds/{matchId}', 'FrontController@matchCallOdds')->name('matchCallOdds');
    Route::post('matchCallForFancyOnly/{matchId}', 'FrontController@matchCallForFancyOnly')->name('matchCallForFancyOnly');
    Route::post('matchCallFor_FANCY/{matchId}', 'FrontController@matchCallFor_FANCY')->name('matchCallFor_FANCY');
    Route::post('matchCallFor_BM/{matchId}', 'FrontController@matchCallFor_BM')->name('matchCallFor_BM');
    Route::get('casino', 'FrontController@casino')->name('casino');
    Route::get('inplay', 'FrontController@inplay')->name('inplay');
    Route::get('inplay-formatted-data/{type}', 'FrontController@getInPlayFormattedData')->name('inplay-formatted-data');
    Route::get('inplay-data-count', 'FrontController@getInPlayDataCount')->name('inplay-data-count');
    Route::post('getmatchdetailsOfInplay', 'FrontController@getmatchdetailsOfInplay')->name('getmatchdetailsOfInplay');
    Route::post('getInplaydata', 'FrontController@getInplaydata')->name('getInplaydata');
    Route::post('Inplaydata', 'FrontController@Inplaydata')->name('Inplaydata');
    Route::get('cricket', 'FrontController@cricket')->name('cricket');
    Route::post('user/fav-match', 'FrontController@userFavMatch')->name('user.fav.match');
    Route::post('getmatchdetailsOfCricket', 'FrontController@getmatchdetailsOfCricket')->name('getmatchdetailsOfCricket');
    Route::get('soccer', 'FrontController@soccer')->name('soccer');
    Route::post('getmatchdetailsOfSoccer', 'FrontController@getmatchdetailsOfSoccer')->name('getmatchdetailsOfSoccer');
    Route::get('tennis', 'FrontController@tennis')->name('tennis');
    Route::post('getmatchdetailsOfTennis', 'FrontController@getmatchdetailsOfTennis')->name('getmatchdetailsOfTennis');
    Route::post('getInplayToday', 'FrontController@getInplayToday')->name('getInplayToday');
    Route::post('getInplayTomrw', 'FrontController@getInplayTomrw')->name('getInplayTomrw');
    Route::get('casino/{id}/{name}', 'FrontController@casinoDetail')->name('casinoDetail');
//    Route::get('teen20/{id}', 'FrontController@casinoDetail')->name('teen20');
//    Route::get('baccarat/{id}', 'FrontController@casinoDetail')->name('baccarat');
//    Route::get('dt202/{id}', 'FrontController@casinoDetail')->name('dt202');
//    Route::get('ab20/{id}', 'FrontController@casinoDetail')->name('ab20');
//    Route::get('32card/{id}', 'FrontController@casinoDetail')->name('32card');
    Route::post('casino/{id}/data/ajax-call', 'CasinoFrontController@getCasinoDetailAjaxCall')->name('casino.live.data-call');
    Route::post('getteen20LastResult', 'CasinoFrontController@getteen20LastResult')->name('getteen20LastResult');
    Route::post('getteen20LastResultpopup', 'CasinoFrontController@getteen20LastResultpopup')->name('getteen20LastResultpopup');
    Route::post('getbaccaratLastResult', 'CasinoFrontController@getbaccaratLastResult')->name('getbaccaratLastResult');
    Route::post('getCasinoab20', 'CasinoFrontController@getCasinoab20')->name('getCasinoab20');
    Route::post('getab20LastResultpopup', 'CasinoFrontController@getab20LastResultpopup')->name('getab20LastResultpopup');
    Route::post('getbaccarat', 'CasinoFrontController@getbaccarat')->name('getbaccarat');
    Route::post('getCasinodt202', 'CasinoFrontController@getCasinodt202')->name('getCasinodt202');
    Route::post('getdt202LastResult', 'CasinoFrontController@getdt202LastResult')->name('getdt202LastResult');
    Route::post('getbaccaratLastResultpopup', 'CasinoFrontController@getbaccaratLastResultpopup')->name('getbaccaratLastResultpopup');
    Route::post('get32cardLastResult', 'CasinoFrontController@get32cardLastResult')->name('get32cardLastResult');
    Route::post('get32cardbLastResultpopup', 'CasinoFrontController@get32cardbLastResultpopup')->name('get32cardbLastResultpopup');
    Route::post('getdt202LastResultpopup', 'CasinoFrontController@getdt202LastResultpopup')->name('getdt202LastResultpopup');
    Route::post('getab20LastResult', 'CasinoFrontController@getab20LastResult')->name('getab20LastResult');
    Route::post('get32cardvideo', 'CasinoFrontController@get32cardvideo')->name('get32cardvideo');
    Route::post('getCasino32cardb', 'CasinoFrontController@getCasino32cardb')->name('getCasino32cardb');
    Route::post('getAllBetsForMobile', 'PlayerController@getAllBetsForMobile')->name('getAllBetsForMobile');
//matchdetail
    Route::post('MyBetStore', 'PlayerController@MyBetStore')->name('MyBetStore');
    Route::post('GetOtherMatchBet', 'PlayerController@GetOtherMatchBet')->name('GetOtherMatchBet');
    Route::get('getPlayerBalance', 'PlayerController@getPlayerBalance')->name('getPlayerBalance');
    Route::post('getleftpanelMenu', 'FrontController@getleftpanelMenu')->name('getleftpanelMenu');
    Route::post('getScoreBoard', 'ScoreBoardController@getScoreBoard')->name('getScoreBoard');
// my account
    Route::get('myprofile', 'FrontController@myprofile')->name('myprofile');
    Route::get('balance-overview', 'FrontController@balanceoverview')->name('balance-overview');
    Route::get('account-statement', 'FrontController@accountstatement')->name('account-statement');
    Route::post('accountstmtdata', 'FrontController@accountstmtdata')->name('accountstmtdata');
    Route::post('getAccountPopup', 'FrontController@getAccountPopup')->name('getAccountPopup');

    Route::get('setMinMax', 'FrontController@setMinMax')->name('setMinMax');

    Route::get('my-bets', 'FrontController@mybets')->name('my-bets');
    Route::get('activity-log', 'FrontController@activitylog')->name('activity-log');
    Route::post('updateUserPassword/{id}', 'FrontController@updateUserPassword')->name('updateUserPassword');
    Route::post('betHistory', 'FrontController@betHistory')->name('betHistory');
    Route::post('betToday', 'FrontController@betToday')->name('betToday');
    Route::post('betYest', 'FrontController@betYest')->name('betYest');
    Route::post('getPLdata', 'FrontController@getPLdata')->name('getPLdata');
    Route::post('plToday', 'FrontController@plToday')->name('plToday');
    Route::post('plYest', 'FrontController@plYest')->name('plYest');
    Route::post('plSport', 'FrontController@plSport')->name('plSport');
    Route::get('getCommissionReport', 'ReportController@getCommissionReport')->name('getCommissionReport');
    Route::post('getCommissionPopup', 'ReportController@getCommissionPopup')->name('getCommissionPopup');

    Route::post('marketPLdata', 'MyaccountController@marketPLdata')->name('marketPLdata');

// multi market
    Route::get('multimarket', 'FrontController@multimarket')->name('multimarket');
    Route::get('frontAutoLogout', 'PlayerController@frontAutoLogout')->name('frontAutoLogout');
    Route::get('maintenance', 'RestrictionController@maintenance')->name('maintenance');
    Route::post('stakechange', 'PlayerController@stakechange')->name('stakechange');
//casino report
    Route::get('casinoreport', 'CasinoReportController@casinoreport')->name('casinoreport');
    Route::post('dataCasinoReport', 'CasinoReportController@dataCasinoReport')->name('dataCasinoReport');
    Route::post('teen20LastResultpopup', 'CasinoReportController@teen20LastResultpopup')->name('teen20LastResultpopup');

    Route::post('casino_bet', 'CasinoCalculationController@casino_bet')->name('casino_bet');
    Route::post('declare/casino_bet/winner', 'CasinoCalculationController@declareCasinoBetWinner')->name('declare.casino_bet.winner');
    Route::get('casino/winner/{roundid}/cards/{casino_name}', 'CasinoCalculationController@getWinnerCards')->name('get.casino_bet.winner.cards');
    Route::post('fancy/calculation', 'FrontController@fancyUserCalculation')->name('fancy.user.calculation');
});

//backpanel

Route::prefix('backpanel')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('backpanel');
    Auth::routes();
});

Route::group(['prefix' => 'backpanel', 'middleware' => ['auth']], function () {
    Route::get('profitloss-downline', 'MyaccountController@profitlossdownline')->name('profitloss-downline');
    Route::get('profitloss-market', 'MyaccountController@profitlossmarket')->name('profitloss-market');
    Route::get('/getUser', 'HomeController@getUser')->name('getUser');
    Route::get('/changepasspage', 'HomeController@changepasspage')->name('changepasspage');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('changePass/{id}', 'HomeController@changePass')->name('changePass');
    Route::get('balanceoverview/{id}', 'HomeController@balanceoverview')->name('balanceoverview');
    Route::post('updatePassword/{id}', 'HomeController@updatePassword')->name('updatePassword');
    Route::post('updatePasswordadmin/{id}', 'HomeController@updatePasswordadmin')->name('updatePasswordadmin');
    Route::get('change_pass_first', 'HomeController@change_pass_first')->name('change_pass_first');
    Route::resource('agent', 'AgentController');
    Route::get('getusername', 'AgentController@getusername')->name('getusername');
    Route::post('addPlayer', 'PlayerController@addPlayer')->name('addPlayer');
    Route::get('userBet/{id}', 'SettingController@userBet')->name('userBet');
    Route::get('message', 'SettingController@index')->name('message');
    Route::post('storeMessage', 'SettingController@storeMessage')->name('storeMessage');
    Route::get('privileges', 'SettingController@privilege')->name('privileges');
    Route::post('deleteprvlg', 'SettingController@deleteprvlg')->name('deleteprvlg');
    Route::post('changePrivilegePass', 'SettingController@changePrivilegePass')->name('changePrivilegePass');
    Route::get('betlist', 'BetListController@index')->name('betlist');
    Route::post('getHistory', 'BetListController@getHistory')->name('getHistory');
    Route::get('betlistlive', 'BetListController@betlistlive')->name('betlistlive');
    Route::post('getHistorylive', 'BetListController@getHistorylive')->name('getHistorylive');
    Route::post('playersearch', 'BetListController@playersearch')->name('playersearch');
    Route::get('main_market', 'SettingController@main_market')->name('backpanel/main_market');
    Route::get('match/{id}', 'SettingController@match')->name('match');
    Route::post('addMatch/{id}', 'SettingController@addMatch')->name('addMatch');
    Route::get('sports', 'SettingController@sports')->name('sports');
    Route::post('addSport', 'SettingController@addSport')->name('addSport');
    Route::get('listmatch/{id}', 'SettingController@listmatch')->name('listmatch');
    Route::get('risk-management', 'SettingController@risk_management')->name('backpanel/risk-management');
    Route::post('storeBalance', 'SettingController@storeBalance')->name('storeBalance');
    Route::post('getriskdetails', 'SettingController@getriskdetails')->name('getriskdetails');
    Route::post('getriskdetailTwo', 'SettingController@getriskdetailTwo')->name('getriskdetailTwo');
    Route::get('risk-management-details/{id}', 'SettingController@risk_management_details')->name('backpanel/risk-management-details');
    Route::post('risk_management_details_ajax/{matchId}', 'SettingController@risk_management_details_ajax')->name('risk_management_details_ajax');
    Route::post('risk_management_matchCallForFancyNBM/{matchId}', 'SettingController@risk_management_matchCallForFancyNBM')->name('risk_management_matchCallForFancyNBM');
    Route::post('risk_management_odds_bet', 'SettingController@risk_management_odds_bet')->name('risk_management_odds_bet');
    Route::post('risk_management_odds_search', 'SettingController@risk_management_odds_search')->name('risk_management_odds_search');
    Route::post('risk_management_bm_search', 'SettingController@risk_management_bm_search')->name('risk_management_bm_search');
    Route::post('risk_management_fancy_search', 'SettingController@risk_management_fancy_search')->name('risk_management_fancy_search');
// block unblock
    Route::get('blockMatch/{id}', 'SettingController@blockMatch')->name('blockMatch');
    Route::get('unblockMatch/{id}', 'SettingController@unblockMatch')->name('unblockMatch');
    Route::get('blockBook/{id}', 'SettingController@blockBook')->name('blockBook');
    Route::get('unblockBook/{id}', 'SettingController@unblockBook')->name('unblockBook');
    Route::get('blockFancy/{id}', 'SettingController@blockFancy')->name('blockFancy');
    Route::get('unblockFancy/{id}', 'SettingController@unblockFancy')->name('unblockFancy');
    Route::get('allBlock/{id}', 'SettingController@allBlock')->name('allBlock');
    Route::get('allunBlock/{id}', 'SettingController@allunBlock')->name('allunBlock');
    Route::post('matchDetailCall/{matchId}', 'SettingController@matchDetailCall')->name('matchDetailCall');
    Route::post('resultRollback', 'SettingController@resultRollback')->name('resultRollback');
    Route::get('match_history', 'SettingController@match_history')->name('match_history');
    Route::post('matchHistoryData', 'SettingController@matchHistoryData')->name('matchHistoryData');
    Route::post('matchHistoryCricket', 'SettingController@matchHistoryCricket')->name('matchHistoryCricket');
    Route::get('matchuser/{id}', 'SettingController@matchuser')->name('matchuser');
    Route::get('fancy_history', 'SettingController@fancy_history')->name('fancy_history');
    Route::get('fancyHistoryDetail/{id}', 'SettingController@fancyHistoryDetail')->name('fancyHistoryDetail');
    Route::get('fancyuser/{id}', 'SettingController@fancyuser')->name('fancyuser');

    Route::post('resultRollbackMatch', 'SettingController@resultRollbackMatch')->name('resultRollbackMatch');
    Route::get('sportLeage', 'SportLeageController@index')->name('sportLeage');
    Route::post('getallMatch', 'SportLeageController@getallMatch')->name('getallMatch');
    Route::post('getLeageData', 'SportLeageController@getLeageData')->name('getLeageData');
    Route::post('storeReference', 'HomeController@storeReference')->name('storeReference');
    Route::get('player-banking', 'SettingController@player_banking')->name('backpanel/player-banking');
    Route::get('agent-banking', 'SettingController@agent_banking')->name('backpanel/agent-banking');
    Route::post('addPlayerBanking', 'SettingController@addPlayerBanking')->name('addPlayerBanking');
    Route::post('addAgentBanking', 'SettingController@addAgentBanking')->name('addAgentBanking');
    Route::post('addMatchFromAPI', 'SportLeageController@addMatchFromAPI')->name('addMatchFromAPI');
    Route::get('sports-list', 'SettingController@sports_list')->name('backpanel/sports-list');
//for previlage users
    Route::post('storeuser', 'AgentController@storeuser')->name('storeuser');
    Route::post('changeprivilageuser', 'SettingController@changestatusListClient')->name('changeprivilageuser');
    Route::post('saveMatchStatus', 'SettingController@saveMatchStatus')->name('saveMatchStatus');
    Route::post('saveMatchAction', 'SettingController@saveMatchAction')->name('saveMatchAction');
    Route::post('saveMatchOddsLimit', 'SettingController@saveMatchOddsLimit')->name('saveMatchOddsLimit');
    Route::get('manage_fancy', 'SettingController@manage_fancy')->name('manage_fancy');
    Route::get('manageFancyDetail/{id}', 'SettingController@manageFancyDetail')->name('manageFancyDetail');
    Route::post('getFancy/{id}', 'SettingController@getFancy')->name('getFancy');
    Route::post('resultDeclare', 'SettingController@resultDeclare')->name('resultDeclare');
    Route::post('resultDeclarecancel', 'SettingController@resultDeclarecancel')->name('resultDeclarecancel');
    Route::post('saveMatchBetsMinLimit', 'SettingController@saveMatchBetsMinLimit')->name('saveMatchBetsMinLimit');
    Route::post('saveMatchBetsMaxLimit', 'SettingController@saveMatchBetsMaxLimit')->name('saveMatchBetsMaxLimit');
    Route::post('saveMatchBmMinLimit', 'SettingController@saveMatchBmMinLimit')->name('saveMatchBmMinLimit');
    Route::post('saveMatchBmMaxLimit', 'SettingController@saveMatchBmMaxLimit')->name('saveMatchBmMaxLimit');
    Route::post('saveMatchFancyMinLimit', 'SettingController@saveMatchFancyMinLimit')->name('saveMatchFancyMinLimit');
    Route::post('saveMatchFancyMaxLimit', 'SettingController@saveMatchFancyMaxLimit')->name('saveMatchFancyMaxLimit');
    Route::post('decideMatchWinner', 'SettingController@decideMatchWinner')->name('decideMatchWinner');
    Route::post('delete_user_bet', 'SettingController@delete_user_bet')->name('delete_user_bet');
    Route::post('rollback_user_bet', 'SettingController@rollback_user_bet')->name('rollback_user_bet');

    Route::get('casinoAll', 'CasinoController@index')->name('casinoAll');
    Route::get('addCasino', 'CasinoController@addCasino')->name('addCasino');
    Route::post('insertCasino', 'CasinoController@insertCasino')->name('insertCasino');
    Route::get('listCasino', 'CasinoController@listCasino')->name('listCasino');
    Route::post('chkstatusactive', 'CasinoController@chkstatusactive')->name('chkstatusactive');
////Theme mangement
    Route::get('themeAll', 'ThemeController@index')->name('themeAll');
    Route::get('addTheme', 'ThemeController@addTheme')->name('addTheme');
    Route::post('insertTheme', 'ThemeController@insertTheme')->name('insertTheme');
    Route::get('listTheme', 'ThemeController@listTheme')->name('listTheme');
    Route::post('chkstatusactiveTheme', 'ThemeController@chkstatusactiveTheme')->name('chkstatusactiveTheme');
    Route::get('delTheme/{id}', 'ThemeController@delTheme')->name('delTheme');
    Route::get('editTheme/{id}', 'ThemeController@editTheme')->name('editTheme');
    Route::post('updatetheme/{id}', 'ThemeController@updatetheme')->name('updatetheme');

    Route::post('savecasinoMaxLimit', 'CasinoController@savecasinoMaxLimit')->name('savecasinoMaxLimit');
    Route::post('savecasinoMinLimit', 'CasinoController@savecasinoMinLimit')->name('savecasinoMinLimit');

    Route::post('saveMatchSuspend', 'SettingController@saveMatchSuspend')->name('saveMatchSuspend');
    Route::get('teen20back/{id}', 'CasinoController@casinoDetail')->name('teen20back');
    Route::get('baccaratback/{id}', 'CasinoController@casinoDetail')->name('baccaratback');
    Route::get('dt202back/{id}', 'CasinoController@casinoDetail')->name('dt202back');
    Route::get('ab20back/{id}', 'CasinoController@casinoDetail')->name('ab20back');
    Route::get('32cardback/{id}', 'CasinoController@casinoDetail')->name('32cardback');
    Route::get('downline-list', 'SettingController@downline_list')->name('downline-list');
    Route::post('getAgentChildAgent', 'SettingController@getAgentChildAgent')->name('getAgentChildAgent');
    Route::post('getAdminAgentBalance', 'SettingController@getAdminAgentBalance')->name('getAdminAgentBalance');
    Route::post('autoLogout', 'SettingController@autoLogout')->name('autoLogout');
    Route::get('socialmedia', 'SettingController@socialmedia')->name('socialmedia');
    Route::post('addsocial', 'SettingController@addsocial')->name('addsocial');
    Route::get('websetting', 'SettingController@websetting')->name('websetting');
    Route::post('addWebsite', 'SettingController@addWebsite')->name('addWebsite');
    Route::post('updateWebsetting', 'SettingController@updateWebsetting')->name('updateWebsetting');
    Route::post('updateWebsiteTheme', 'SettingController@updateWebsiteTheme')->name('updateWebsiteTheme');
    Route::get('WebsettingData/{id}', 'SettingController@WebsettingData')->name('WebsettingData');
    Route::post('updateWebsettingData', 'SettingController@updateWebsettingData')->name('updateWebsettingData');

// Admin Myaccount
    Route::get('myaccount-summary', 'MyaccountController@index')->name('myaccount-summary');
    Route::get('myaccount-profile', 'MyaccountController@accountprofile')->name('myaccount-profile');
    Route::post('updateAccountPassword/{id}', 'MyaccountController@updateAccountPassword')->name('updateAccountPassword');
    Route::get('myaccount-statement', 'MyaccountController@myaccountstatement')->name('myaccount-statement');
    Route::post('data-myaccount-statement', 'MyaccountController@datamyaccountstatement')->name('data-myaccount-statement');
    Route::get('myaccount-trasferred-log', 'MyaccountController@myaccounttrasferredlog')->name('myaccount-trasferred-log');
    Route::get('myaccount-active-log', 'MyaccountController@myaccountactivelog')->name('myaccount-active-log');
    Route::get('commision-report', 'MyaccountController@commisionreport')->name('commision-report');
    Route::post('SubBackDetail', 'MyaccountController@SubBackDetail')->name('SubBackDetail');
    Route::post('SubDetail', 'MyaccountController@SubDetail')->name('SubDetail');
    Route::post('getHistoryPL', 'MyaccountController@getHistoryPL')->name('getHistoryPL');
    Route::get('betHistoryBack/{id}', 'MyaccountController@betHistoryBack')->name('betHistoryBack');
    Route::get('activityLog/{id}', 'MyaccountController@activityLog')->name('activityLog');
    Route::get('transactionHistory/{id}', 'MyaccountController@transactionHistory')->name('transactionHistory');
    Route::post('getBetHistoryPL', 'MyaccountController@getBetHistoryPL')->name('getBetHistoryPL');
    Route::get('betHistoryPLBack/{id}', 'MyaccountController@betHistoryPLBack')->name('betHistoryPLBack');
    Route::post('getBetHistoryPLBack', 'MyaccountController@getBetHistoryPLBack')->name('getBetHistoryPLBack');
    Route::post('back_accountstmtdata', 'MyaccountController@back_accountstmtdata')->name('back_accountstmtdata');
    Route::post('back_getAccountPopup', 'MyaccountController@back_getAccountPopup')->name('back_getAccountPopup');
// User Restriction by admin
    Route::post('suspend_pa', 'RestrictionController@suspend_pa')->name('suspend_pa');
    Route::post('agentSubDetail', 'RestrictionController@agentSubDetail')->name('agentSubDetail');
    Route::post('agentSubBackDetail', 'RestrictionController@agentSubBackDetail')->name('agentSubBackDetail');
    Route::post('userWiseBlock', 'RestrictionController@userWiseBlock')->name('userWiseBlock');
// setting manage tv
    Route::get('managetv', 'SettingController@managetv')->name('managetv');
    Route::post('addManageTv', 'SettingController@addManageTv')->name('addManageTv');
    Route::post('chkstatusbm', 'SettingController@chkstatusbm')->name('chkstatusbm');
    Route::post('chkstatusfancy', 'SettingController@chkstatusfancy')->name('chkstatusfancy');
    Route::post('addBanner', 'SettingController@addBanner')->name('addBanner');
    Route::get('delBanner/{id}', 'SettingController@delBanner')->name('delBanner');
    Route::get('editBanner/{id}', 'SettingController@editBanner')->name('editBanner');
    Route::post('updatebanner/{id}', 'SettingController@updatebanner')->name('updatebanner');
});

Route::get('read/env/file', function (){
    return $_ENV;
});

Route::get('/jitendra/update/code', function (){
    die("asdsad");
    $output = shell_exec('ls');

    // Display the list of all file
    // and directory
    echo "<pre>$output</pre>";
});


