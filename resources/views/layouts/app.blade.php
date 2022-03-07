<!doctype html>
<?php
$main_url = explode(".", $_SERVER['HTTP_HOST']);
use App\Website;
$website = Website::where('title', $main_url[1])->first();
?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php /*?> <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"><?php */?>
    <meta property="og:image" href="{{ asset('asset/front/img')}}/{{$website->favicon}}">
    <meta property="og:description" content="Place your data here">
    <link rel="shortcut icon" href="{{ asset('asset/front/img')}}/{{$website->favicon}}" type="image/x-icon">
    <title>{{$website->title}}</title>
    <link href="{{ asset('asset/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/color-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/jquery-ui.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('asset/js/datatables/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/js/datatables/css/jquery.dataTables.min.css') }}">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">
<?php /*?><link href="{{ asset('asset/css/responsive.css') }}" rel="stylesheet"><?php */?>
<!-- toster script and js -->
    <!--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
    <script src="{{ asset('asset/js/jquery.min.js') }}"></script>
    <link href="{{ asset('asset/css/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('asset/js/toastr.min.js') }}"></script>
    <!-- Styles -->
    <style>
        .add_balance {
            color: #000 !important;
            background: none;
            font-weight: bold;
        }

        .toast {
            left: 50% !important;
            position: fixed !important;
            transform: translate(-50%, 0px) !important;
            z-index: 9999 !important;
        }
    </style>
</head>
<?php
$url = explode("/", $_SERVER['REQUEST_URI']);
$page1 = $url[1];
$page = explode(".php", $page1);
$page2 = $page[0];
use App\setting;
use App\User;
use App\CreditReference;
$settings = ""; $balance = 0;
$loginuser = Auth::user();
$ttuser = User::where('id', $loginuser->id)->first();
$auth_id = $loginuser->id;
$auth_type = $loginuser->agent_level;
if ($auth_type == 'COM') {
    $settings = setting::latest('id')->first();
    $balance = $settings->balance;
} else {
    $settings = CreditReference::where('player_id', $auth_id)->first();
    $balance = $settings['available_balance_for_D_W'];
}
?>

<body class="white-bg text-color-black1 chreme-bg {{ $website->themeClass }}">
<div class="page-wrapper">
    <header class="main-header">
        <div class="top_header">
            <div class="container">
                <div class="row">
                    <div class="logo">
                        <a href="{{route('home')}}"><img src="{{ URL::to('asset/front/img')}}/{{$website->logo}}"></a>
                    </div>
                    <ul class="account-wrap">
                        <li class="text-color-yellow1">
                            <span class="black-bg text-color-white">{{$ttuser->agent_level}}</span>
                            <strong>{{$loginuser->user_name}}</strong>
                        </li>
                        <li class="main-pth text-color-yellow1">
                            <a>
                                <span class="black-bg text-color-white">Main</span><strong
                                    id="myadminbalance">PTH {{number_format($balance,2, '.', '')}}</strong>
                            </a>
                            <a class="refreshimg black-bg-rgb1" id="refreshpage">
                                <i class="fas fa-redo"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bottom-header yellow-gradient-bg">
            <div class="container">
                <div class="row">
                    <div class="mainmenu">
                        <nav id='cssmenu'>
                            <div class="button">
                                <i class="fas fa-align-justify"></i>
                                <i class="far fa-window-close"></i>
                            </div>
                            <ul>
                                @if(($ttuser->list_client==1) || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')

                                    <li <?php if(@$url[2] == 'home') { ?> class="active" <?php } ?>>
                                        <a href="{{route('home')}}" class="text-color-white">Downline List </a>
                                    </li>
                                @endif

                                @if($ttuser->my_account==1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')
                                    <li <?php if($page2 == 'myaccount-summary') { ?> class="active" <?php } ?>>
                                        <a href="{{route('myaccount-summary')}}" class="text-color-white">My Account</a>
                                    </li>
                                @endif
                                @if($ttuser->my_report==1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')

                                    <li <?php if($page2 == 'profitloss-downline' || $page2 == 'profitloss-market') { ?> class="active" <?php } ?>>
                                        <a href="#" class="text-color-white">My Report</a>
                                        <ul class="black-bg1">
                                            <li <?php if ($page2 == 'profitloss-downline') { ?> <?php } ?>>
                                                <a href="{{route('profitloss-downline')}}" class="text-color-yellow1">Profit/Loss
                                                    Report by Downline</a>
                                            </li>
                                            <li <?php if ($page2 == 'profitloss-market') { ?> <?php } ?>>
                                                <a href="{{route('profitloss-market')}}" class="text-color-yellow1">Profit/Loss
                                                    Report by Market</a>
                                            </li>
                                            @if($ttuser->agent_level == 'COM')
                                                <li <?php if ($page2 == 'commision-report') { ?> <?php } ?>>
                                                    <a href="{{route('commision-report')}}" class="text-color-yellow1">Commision
                                                        Report</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif

                                @if($ttuser->bet_list ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')

                                    <li class="{{ (request()->is('backpanel/betlist' )) ? 'active' : '' }}">
                                        <a href="{{route('betlist')}}" class="text-color-white">BetList</a>
                                    </li>

                                @endif

                                @if($ttuser->bet_list_live ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')

                                    <li class="{{ (request()->is('backpanel/betlistlive' )) ? 'active' : '' }}">
                                        <a href="{{route('betlistlive')}}" class="text-color-white">BetListLive </a>
                                    </li>

                                @endif

                                @if($ttuser->live_casino ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')
                                    <li <?php if($page2 == 'betlistlive') { ?> class="active"
                                        <?php } ?> class="casino-menu">
                                        <a href="{{route('listCasino')}}" class="text-color-white black-gradient-bg1">Live
                                            Casino
                                            <img src="{{ URL::to('asset/img/card-game.svg')}}" alt="">
                                        </a>
                                    </li>
                                @endif

                                @if($ttuser->risk_management ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')
                                    <li class="{{ (request()->is('backpanel/risk-management*' )) ? 'active' : '' }}">
                                        <a href="{{route('backpanel/risk-management')}}" class="text-color-white">Risk
                                            Management</a>
                                    </li>
                                @endif

                                @if($ttuser->agent_banking ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')
                                    <li class="{{ (request()->is('backpanel/agent-banking*' )) ? 'active' : '' }}">
                                        <a href="{{route('backpanel/agent-banking')}}" class="text-color-white">Agent
                                            Banking</a>
                                    </li>
                                @endif

                                @if($ttuser->player_banking ==  1 || $ttuser->agent_level == 'COM' || $ttuser->agent_level == 'MDL' || $ttuser->agent_level ==  'SMDL' || $ttuser->agent_level ==  'DL' || $ttuser->agent_level ==  'AD' || $ttuser->agent_level ==  'SP')
                                    <li class="{{ (request()->is('backpanel/player-banking*' )) ? 'active' : '' }}">
                                        <a href="{{route('backpanel/player-banking')}}" class="text-color-white">Player
                                            Banking</a>
                                    </li>
                                @endif

                                @if($ttuser->sports_leage ==  1 || $ttuser->agent_level == 'COM')
                                    <li class="{{ (request()->is('backpanel/sportLeage*' )) ? 'active' : '' }}">
                                        <a href="{{route('sportLeage')}}" class="text-color-white black-gradient-bg1">Sport-Leage</a>
                                    </li>
                                @endif
                                <?php
                                $loginUser = $loginuser->agent_level;
                                ?>
                                @if($ttuser->agent_level == 'COM' || $ttuser->sports_main_market==1 || $ttuser->main_market==1 || $ttuser->manage_fancy==1 || $ttuser->fancy_history==1 || $ttuser->match_history==1 || $ttuser->message==1 || $ttuser->casino_manage==1)
                                    <li class="{{ (request()->is('backpanel/main_market*' ) || request()->is('backpanel/message*' ) || request()->is('backpanel/privilege*' )) ? 'active' : '' }}">
                                        <a href="#" class="text-color-white black-gradient-bg1">Setting</a>
                                        <ul class="black-bg1">
                                            @if($ttuser->main_market==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('backpanel/main_market')}}"
                                                       class="text-color-yellow1">Manual Match Add</a>
                                                </li>
                                            @endif

                                            @if($ttuser->sports_main_market==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('backpanel/sports-list')}}"
                                                       class="text-color-yellow1">Sports Main Market</a>
                                                </li>
                                            @endif

                                            @if($ttuser->manage_fancy==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('manage_fancy')}}" class="text-color-yellow1">
                                                        Manage Fancy</a>
                                                </li>

                                            @endif

                                            @if($ttuser->fancy_history==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('fancy_history')}}" class="text-color-yellow1">Fancy
                                                        History</a>
                                                </li>
                                            @endif

                                            @if($ttuser->match_history==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('match_history')}}" class="text-color-yellow1">
                                                        Match History</a>
                                                </li>
                                            @endif

                                            @if($ttuser->message==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('message')}}"
                                                       class="text-color-yellow1">Message</a>
                                                </li>
                                            @endif

                                            @if($ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('managetv')}}" class="text-color-yellow1">Manage
                                                        Tv</a>
                                                </li>
                                            @endif

                                            @if($ttuser->casino_manage==1 || $ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('casinoAll')}}" class="text-color-yellow1">Casino
                                                        Manage</a>
                                                </li>
                                            @endif

                                            @if($ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('privileges')}}" class="text-color-yellow1">Manage
                                                        Privilege</a>
                                                </li>
                                            @endif

                                            @if($ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('websetting')}}" class="text-color-yellow1">Website
                                                        Setting</a>
                                                </li>
                                                <li>
                                                    <a href="{{route('themeAll')}}" class="text-color-yellow1">Theme
                                                        Management</a>
                                                </li>
                                            @endif

                                            @if($ttuser->agent_level == 'COM')
                                                <li>
                                                    <a href="{{route('socialmedia')}}" class="text-color-yellow1">Social
                                                        Media</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </nav>

                        <ul class="right-logout">
                            @if($ttuser->agent_level == 'COM' || $ttuser->add_balance==1)
                                <li class="text-color-white black-gradient-bg1"><span class="text-color-lime-green"><a
                                            class="text-color-white add_balance grey-gradient-bg" data-toggle="modal"
                                            data-target="#myAddBalance" style="color: #fff !important;">Add Balance</a></span>
                                </li>        @endif
                            <li><span class="text-color-lime-green">Time Zone :</span> GMT+5:30</li>
                            <li class="logout-txt">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">Logout<img
                                        src="/asset/img/logout-black.svg">
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="pt-2">
        <div class="container">
            @if(!empty($settings->agent_msg))
                <div class="news-addvertisment black-gradient-bg text-color-white">
                    <h4>News</h4>
                    <marquee scrollamount="3">
                        <a href="#" class="text-color-blue">{{$settings->agent_msg}}</a>
                    </marquee>
                </div>
            @endif
        </div>
    </section>
    @yield('content')
    @include('backpanel/footer')
</div>
<!-- Add Balace Model -->

<div class="modal credit-modal" id="myAddBalance">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Add Balance</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="/asset/img/close-icon.png"></button>
            </div>
            <form method="post" action="{{route('storeBalance')}}" id="balanceform">
                @csrf
                <div class="modal-body">
                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>Amount</span>
                                <span>
                                    <input type="text" id="balance_amount" name="balance_amount"
                                           placeholder="Enter Amount" maxlength="16" class="form-control white-bg"
                                           onkeypress="return isNumberKey(event)">
                                    <em class="text-color-red">*</em>
                                </span>
                                <span class="text-danger cls-error" id="errbalance"></span>
                            </div>
                        </div>

                        <div class="button-wrap pb-0">
                            <input type="submit" value="Add" name="addbalance_btn" id="addbalance_btn"
                                   class="submit-btn text-color-yellow">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('asset/js/index.js') }}"></script>
<script type="text/javascript">
    /*
       function disableBack() { window.history.forward(); }
        setTimeout("disableBack()", 0);
        window.onunload = function () { null };

            // right click disable
            $(document).bind("contextmenu",function(e){

                window.location.replace("https://www.google.com/");
                return false;
            });

            // disable using keys
            $(document).keydown(function(e){
                if(e.which === 123){
                   return false;
                }

                if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                    return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                    return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                    return false;
                }
                if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                    return false;
                }
            });

        console.log('Is DevTools open:', window.devtools.isOpen);


        console.log('DevTools orientation:', window.devtools.orientation);


        window.addEventListener('devtoolschange', event => {
            if(event.detail.isOpen){

                $.ajax({

                    type: 'POST',

                    url: '{{route("autoLogout")}}',

                success: function(data) {

                 window.location.replace("https://www.google.com/");
                }

            });
            window.location.replace("https://www.google.com/");
        }
        if(window.devtools.isOpen){
            $.ajax({

                type: 'POST',

                url: '{{route("autoLogout")}}',

                success: function(data) {

                 window.location.replace("https://www.google.com/");
                }

            });
            window.location.replace("https://www.google.com/");
        }
        console.log('Is DevTools open1:', event.detail.isOpen);
        console.log('DevTools orientation1:', event.detail.orientation);
    });
*/
</script>

<script>
    @if(Session::has('message'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.success("{{ session('message') }}");
    @endif

        @if(Session::has('error'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.error("{{ session('error') }}");
    @endif

        @if(Session::has('info'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.info("{{ session('info') }}");
    @endif

        @if(Session::has('warning'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.warning("{{ session('warning') }}");
    @endif
</script>
<script>
    $('#refreshpage').click(function () {
        window.location.reload();
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 45)
            return false;
        return true;
    }

    $('#addbalance_btn').click(function () {
        var errsub = $('#errsub').text();
        var balance_amount = $('#balance_amount').val();
        $('#errbalance').html('');
        if (balance_amount == '') {
            $('#errbalance').html('Amount field is required.');
            return false;
        }
    });
    $('.period_date5').datepicker({
        dateFormat: "yy-mm-dd",
        "setDate": new Date(),
    });
    $('.period_date6').datepicker({
        dateFormat: "yy-mm-dd",
        "setDate": new Date(),
    });
    $('.period_date3').datepicker({
        dateFormat: "yy-mm-dd"
    });
    $('.period_date4').datepicker({
        dateFormat: "yy-mm-dd"
    });
    $('.period_date1').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date2').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date_plf').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date_plt').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date5a').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date6a').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date5b').datepicker({
        dateFormat: "dd-mm-yy"
    });
    $('.period_date6b').datepicker({
        dateFormat: "dd-mm-yy"
    });
</script>

</body>
</html>
