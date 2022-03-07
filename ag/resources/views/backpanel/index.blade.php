@extends('layouts.app')

@section('content')

    <style>
        .text-color-lght-grey {
            color: #000;
            font-weight: bold;
        }
    </style>

    <!-- ss comment -->
{{--     <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.js"></script>--}}
    <!--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <link rel="Stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->

    <?php
    use App\Http\Controllers\AgentController;
    $getUser = Auth::user();
    ?>

    <section>
        <?php
        $loginuser = Auth::user();
        use App\CreditReference;
        use App\User;
        use App\UserExposureLog;
        use App\UserHirarchy;
        $total_ref_pl = 0;
        $myPl = '';
        ?>

        @if($errors->any())
            <h4>{{$errors->first()}}</h4>
        @endif

        <style type="text/css">
            .betloaderimage1 {
                top: 50%;
                height: 135px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 5px 10px rgb(0 0 0 / 50%);
                padding-top: 30px;
                z-index: 50;
                position: absolute;
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
        <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none">
            <ul class="loading1">
                <li>
                    <img src="/asset/front/img/loaderajaxbet.gif">
                </li>
                <li>Loading...</li>
            </ul>
        </div>

        <div class="container">
            <div class="findmember-section">
                <div class="search-wrap">
                    <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z"
                            fill="rgb(30,30,30"/>
                    </svg>
                    <div>
                        <input class="search-input" type="text" name="userId" id="userSearch"
                               placeholder="Find member...">
                        <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                    </div>
                </div>

                <ul class="agentlist" style="display: none;">
{{--                    <li class="agentlistadmin" id="{{$getUser->id}}"></li>--}}
                </ul>

                <div class="player-right">
                    @if($loginuser->agent_level != 'SL')
                        @if($loginuser->agent_level != 'DL')
                            <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddAgent">
                                <?php  $url = $_SERVER['REQUEST_URI']; ?>
                                <img src="{{ URL::to('asset/img/user-add.png') }}">Add Agent
                            </a>
                        @endif
                        <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddPlayer">
                            <img src="{{ URL::to('asset/img/user-add.png') }}">Add Player
                        </a>
                    @endif
                    <a class="refreshbtn grey-gradient-bg" id="refreshpage">
                        <img src="{{ URL::to('asset/img/refresh.png') }}">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Player Model -->

    <div class="modal credit-modal" id="myAddPlayer">
        <div class="modal-dialog">
            <div class="modal-content light-grey-bg-1">
                <form method="post" action="{{route('addPlayer')}}" id="playerform">
                    @csrf
                    <div class="modal-header border-0">
                        <h4 class="modal-title text-color-blue-1">Add Player</h4>
                        <button type="button" class="close" data-dismiss="modal"><img
                                src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-modal addform-modal">
                            <div class="addform-block">
                                <div>
                                    <span>Username</span>
                                    <span>
                                    <input type="text" id="puser_name" name="puser_name" placeholder="Enter"
                                           maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                                </div>
                                <span class="text-danger cls-error pusrnamecls" id="errplyrusername"></span>
                                <div>
                                    <span>Password</span>
                                    <span><input id="ppassword" name="ppassword" type="password" placeholder="Enter"
                                                 class="form-control white-bg"><em class="text-color-red">*</em></span>
                                </div>

                                <span class="text-danger cls-error" id="errplyrpass"></span>

                                <div>
                                    <span>Confirm Password</span>
                                    <span><input id="pcpassword" name="pcpassword" type="password" placeholder="Enter"
                                                 class="form-control white-bg"><em class="text-color-red">*</em></span>
                                </div>

                                <span class="text-danger cls-error" id="errplyrcpass"></span>
                            </div>

                            <div class="addform-block">
                                <div>
                                    <span>First Name</span>

                                    <span>
                                    <input type="text" id="pfname" name="pfname" placeholder="Enter" maxlength="16"
                                           class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                                </div>

                                <span class="text-danger cls-error" id="errplyrfname"></span>

                                <div>
                                    <span>Last Name</span>
                                    <span><input id="planame" name="planame" type="text" class="form-control white-bg"></span>
                                </div>

                                <span class="text-danger cls-error" id="errplyrlname"></span>

                                <div>
                                    @php $readonly='readonly';  @endphp
                                    @if(Auth::user()->agent_level == 'COM')
                                        @php $readonly='';  @endphp
                                    @endif
                                    <span>Commission(%)</span>
                                    <span><input id="pcommission" name="pcommission" type="text"
                                                 value="{{$loginuser->commission}}" {{$readonly}} placeholder="Enter"
                                                 class="form-control white-bg"
                                                 onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em
                                            class="text-color-red">*</em></span>
                                </div>

                                <span class="text-danger cls-error" id="errplyrerrcm"></span>

                                @if(Auth::user()->agent_level == 'COM')
                                    <div>

                                        <span>Rolling Delay</span>

                                        <span>
                                    <label class="switch switch-label switch-primary pull-left">
                                        <input class="switch-input ng-untouched ng-valid ng-dirty" id="dealy_time1"
                                               name="dealy_time" type="checkbox">
                                        <span class="switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>
                                </span>

                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>

                                    <div class="checked-hide1" style="display: none;">
                                        <div>
                                            <span>Odds</span>
                                            <span><input id="odds" name="odds" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>

                                        <div>
                                            <span>Bookmaker</span>
                                            <span><input id="bookmaker" name="bookmaker" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Fancy</span>
                                            <span><input id="fancy" name="fancy" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Soccer</span>
                                            <span><input id="soccer" name="soccer" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Tennis</span>
                                            <span><input id="tennis" name="tennis" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                    </div>

                                @endif

                                <div>
                                    <span>Time Zone</span>
                                    <span>
                                    <select name="ptime" id="ptime" class="form-control white-bg">
                                        <option value="GMT+5:30">IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                                    </select>

                                    <em class="text-color-red">*</em>
                                </span>
                                </div>
                                <span class="text-danger cls-error" id="errplyrtime"></span>
                            </div>

                            <div class="button-wrap pb-0">
                                <input type="submit" value="Create" name="addplayer" id="addplayer"
                                       class="submit-btn text-color-yellow">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Agent Model -->

    <div class="modal credit-modal" id="myAddAgent">
        <div class="modal-dialog">
            <div class="modal-content light-grey-bg-1">
                <div class="modal-header border-0">
                    <h4 class="modal-title text-color-blue-1">Add Agent</h4>
                    <button type="button" class="close" data-dismiss="modal"><img
                            src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                </div>

                <div class="modal-body">
                    <form method="post" action="{{route('agent.store')}}" id="agentform">
                        @csrf

                        <div class="form-modal addform-modal">
                            <div class="addform-block">
                                <div>
                                    <span>Agent Level</span>
                                    <span>
                                    <select class="form-control white-bg" name="agent_level" id="agent_level">
                                        @if($loginuser->agent_level == 'MDL')
                                            <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'SMDL')
                                            <option value="MDL">SUPER MASTER (MDL)</option>
                                            <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'AD')
                                            <option value="SP">SUPER (SP)</option>
                                            <option value="SMDL">SUB ADMIN (SMDL)</option>
                                            <option value="MDL">SUPER MASTER (MDL)</option>
                                            <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'SP')
                                            <option value="SMDL">SUB ADMIN (SMDL)</option>
                                            <option value="MDL">SUPER MASTER (MDL)</option>
                                            <option value="DL">MASTER (DL)</option>
                                        @else
                                            <option value="AD">ADMIN (AD)</option>
                                            <option value="SP">SUPER (SP)</option>
                                            <option value="SMDL">SUB ADMIN (SMDL)</option>
                                            <option value="MDL">SUPER MASTER (MDL)</option>
                                            <option value="DL">MASTER (DL)</option>
                                        @endif
                                    </select>
                                    <em class="text-color-red">*</em>
                                </span>
                                </div>
                                <span class="text-danger cls-error" id="errage"></span>
                                <div>
                                    <span>User Name</span>
                                    <span><input id="user_name" type="text" name="user_name" placeholder="Enter"
                                                 class="form-control white-bg user_name"><em
                                            class="text-color-red">*</em></span>
                                </div>
                                <span class="userNm text-danger cls-error" id="errsub"></span>
                                <div>
                                    <span>Password</span>
                                    <span><input id="password" name="password" type="password" placeholder="Enter"
                                                 class="form-control white-bg"><em class="text-color-red">*</em></span>
                                </div>
                                <span class="text-danger cls-error" id="errpass"></span>
                                <div>
                                    <span>Confirm Password</span>
                                    <span><input id="confirm_password" name="confirm_password" type="password"
                                                 placeholder="Enter" class="form-control white-bg"><em
                                            class="text-color-red">*</em></span>
                                </div>
                                <span class="text-danger cls-error" id="errcnpass"></span>
                            </div>
                            <div class="addform-block">
                                <div>
                                    <span>First Name</span>
                                    <span>
                                    <input type="text" id="first_name" name="first_name" placeholder="Enter"
                                           maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                                </div>
                                <span class="text-danger cls-error" id="errfn"></span>
                                <div>
                                    <span>Last Name</span>
                                    <span><input id="last_name" name="last_name" placeholder="Enter" type="text"
                                                 class="form-control white-bg"></span>
                                </div>
                                <span class="text-danger cls-error" id="errln"></span>
                                <div>
                                    <span>Commission(%)</span>
                                    @if($loginuser->agent_level == 'COM')
                                        <span><input id="commission" type="text" name="commission" placeholder="Enter"
                                                     class="form-control white-bg"
                                                     onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em
                                                class="text-color-red">*</em></span>
                                    @else
                                        <span><input id="commission" type="text" name="commission"
                                                     value="{{$loginuser->commission}}" readonly placeholder="Enter"
                                                     class="form-control white-bg"
                                                     onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em
                                                class="text-color-red">*</em></span>
                                    @endif
                                </div>
                                <span class="text-danger cls-error" id="errcm"></span>

                                @if($loginuser->agent_level == 'COM')
                                    <div>
                                        <span>Rolling Delay</span>
                                        <span>
                                    <label class="switch switch-label switch-primary pull-left">
                                        <input class="switch-input ng-untouched ng-valid ng-dirty" id="dealy_time"
                                               name="dealy_time" type="checkbox">
                                        <span class="switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>
                                </span>

                                    </div>
                                    {{--<div>
                                        <span>Dealy Time</span>
                                        <span><input  type="text"  placeholder="Enter" class="form-control white-bg" value="{{$loginuser->dealy_time}}" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>--}}
                                    <span class="text-danger cls-error" id="errdly"></span>
                                    <div class="checked-hide" style="display: none;">
                                        <div>
                                            <span>Odds</span>
                                            <span><input id="odds" name="odds" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>

                                        <div>
                                            <span>Bookmaker</span>
                                            <span><input id="bookmaker" name="bookmaker" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Fancy</span>
                                            <span><input id="fancy" name="fancy" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Soccer</span>
                                            <span><input id="soccer" name="soccer" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                        <div>
                                            <span>Tennis</span>
                                            <span><input id="tennis" name="tennis" type="text" placeholder="Enter"
                                                         class="form-control white-bg"
                                                         onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                        </div>

                                        <span class="text-danger cls-error" id="errdly"></span>
                                    </div>
                                @endif
                                <div>
                                    <span>Time Zone</span>
                                    <span>
                                    <select name="time_zone" id="time_zone" class="form-control white-bg">
                                        <option value="GMT+5:30">IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                                    </select>
                                    <em class="text-color-red">*</em>
                                </span>
                                </div>
                                <span class="text-danger cls-error" id="errtim"></span>
                            </div>
                            <div class="button-wrap pb-0">
                                <input type="submit" value="Create" name="" id="agentSubmit"
                                       class="submit-btn text-color-yellow">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="container">
            <div id="remaining-wrap" class="remaining-wrap white-bg text-color-blue-1">
                <div class="block-remain">
                    <span class="text-color-lght-grey">Remaining Balance</span>
                    <h4>{{ $website->currency }} <span class="d-inline-block balance">0.00</span></h4>
                </div>
                @if($loginuser->agent_level != 'DL')
                    <div class="block-remain">
                        <span class="text-color-lght-grey">Total Agent Balance</span>
                        <h4>{{ $website->currency }} <span class="d-inline-block hirUser_bal">0.00</span></h4>
                    </div>
                @endif
                <div class="block-remain">
                    <span class="text-color-lght-grey">Total Client Balance</span>
                    <h4>{{ $website->currency }} <span class="d-inline-block totalClientBal">0.00</span></h4>
                </div>
                <div class="block-remain">
                    <span class="text-color-lght-grey">Exposure</span>
                    <h4>{{ $website->currency }}
                        <div class="text-color-red"><span class="d-inline-block totalExposure">0.00</span></div>
                    </h4>
                </div>
                <div class="block-remain">
                    <span class="text-color-lght-grey">Available Balance</span>
                    <h4>{{ $website->currency }} <span class="d-inline-block remain_bal">0.00</span></h4>
                </div>
                <div class="block-remain">
                    <span class="text-color-lght-grey">MY P/L</span>
                    <h4>{{ $website->currency }}
                        <div class="exp_div" data-old-val="0" id="ledger_exposure_div">0.00</div>
                    </h4>
                </div>
            </div>
        </div>
    </section>

    @if($loginuser->agent_level != 'DL')

        <?php
            $total_ref_pl = 0;
        ?>

        <section>
            <div class="container">
                <table class="table custom-table white-bg text-color-blue-2 search-result" id="pager">
                    <thead>
                    <tr>
                        <th class="light-grey-bg">Account(Agent)</th>
                        <th class="light-grey-bg">Credit Ref.</th>
                        <th class="light-grey-bg">Remaining bal.</th>
                        <th class="light-grey-bg">Total Agent bal.</th>
                        <th class="light-grey-bg">Total client bal.</th>
                        <th class="light-grey-bg">Available bal.</th>
                        <th class="light-grey-bg">Exposure</th>
                        <th class="light-grey-bg">Ref. P/L</th>
                        <th class="light-grey-bg">Cumulative P/L</th>
                        <th class="light-grey-bg">Status</th>
                        @if($website->enable_partnership == 1)
                            <th class="light-grey-bg">Partnership</th>
                        @endif
                        <th class="light-grey-bg">Action</th>
                    </tr>
                    </thead>

                    <tbody id="bodyData"></tbody>
                </table>

                <div class="agent pagination-wrap light-grey-bg-1"></div>
            </div>
        </section>
    @endif


    <!-- Setting Pop-up Start -->
    <div class="modal credit-modal" id="myStatus">
        <div class="modal-dialog">
            <div class="alert" style="color: red; font-weight: bold; text-align: center;font-size: x-large;"></div>
            <form id="changestatus">
                @csrf
                <div class="modal-content light-grey-bg-1">
                    <div class="modal-header">
                        <h4 class="modal-title text-color-blue-1">Change Status</h4>
                        <button type="button" class="close" data-dismiss="modal"><img
                                src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <div class="status-block">
                            <div class="status_id white-bg">
                                <p>
                                    <span class="highlight-1 purple-bg text-color-white" id="agent"></span>
                                    <span id="username"></span>
                                </p>

                                <p class="status-active" id="status" style="text-transform: capitalize;"><span
                                        class="round-circle green-bg"></span></p>
                            </div>
                            <div class="status-button white-bg">
                                <ul>
                                    <li>
                                        <a data-check="active" id="tagval1"
                                           class="but_active white-bg text-color-green check active-sts">
                                            <img class="disable_img"
                                                 src="{{ URL::to('asset/img/active-icon-disable.png') }}">
                                            <img class="" src="{{ URL::to('asset/img/active-icon.png') }}">
                                            <img class="white-icon"
                                                 src="{{ URL::to('asset/img/active-white-icon.png') }}">Active
                                        </a>
                                    </li>

                                    <li>
                                        <a data-check="suspend" class="but_suspend text-color-red check suspend-sts"
                                           id="tagval2">
                                            <img class="disable_img"
                                                 src="{{ URL::to('asset/img/disable-icon-disable.png') }}">
                                            <img class="" src="{{ URL::to('asset/img/disable-icon.png') }}"><img
                                                class="white-icon"
                                                src="{{ URL::to('asset/img/disable-white-icon.png') }}">Suspend
                                        </a>
                                    </li>
                                    <li>
                                        <a data-check="locked" class="but_locked text-color-1 check locked-sts"
                                           id="tagval3"><img class="" src="{{ URL::to('asset/img/lock-icon.png') }}">
                                            <img class="white-icon"
                                                 src="{{ URL::to('asset/img/lock-white-icon.png') }}">Locked
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="buttton-change">
                                <dl class="form_list">
                                    <span>Password</span>
                                    <input id="spassword" name="spassword" type="password" placeholder="Enter"
                                           class="form-control white-bg">
                                </dl>
                                <input type="button" class="appendVal submit-btn text-color-yellow" value="Change"
                                       name="submit" onclick="chngstusval(this.id);">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Setting Pop-up End -->

    @if($loginuser->agent_level != 'DL')
{{--        <section>--}}
{{--            <div class="container">--}}
{{--                <div class="pagination-wrap light-grey-bg-1">--}}
{{--                    <ul class="pages">--}}
{{--                        <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>--}}
{{--                        <li id="pageNumber"><a class="active text-color-yellow">1</a></li>--}}
{{--                        <li id="next"><a class="">Next</a></li>--}}
{{--                        <input type="number" id="goToPageNumber_1" maxlength="6" size="4" class="pageinput white-bg"><a id="goPageBtn_1">GO</a>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </section>--}}
    @endif

    <!-- Player Section -->

    <section>
        <div class="container">
            <table class="table custom-table white-bg text-color-blue-2 search-result" id="playerpager">
                <thead>
                <tr>
                    <th class="light-grey-bg">Account(Player)</th>
                    <th class="light-grey-bg">Credit Ref</th>
                    <th class="light-grey-bg">Remaining bal.</th>
                    <th class="light-grey-bg">Exposure</th>
                    <th class="light-grey-bg">Ref. P/L</th>
                    <th class="light-grey-bg">Cumulative P/L</th>
                    <th class="light-grey-bg">Status</th>
                    <th class="light-grey-bg">Action</th>
                </tr>
                </thead>
                <tbody id="playerData">

                </tbody>
            </table>
            <div class="player pagination-wrap light-grey-bg-1"></div>
        </div>
    </section>
{{--    <section>--}}
{{--        <div class="container">--}}
{{--            <div class="pagination-wrap light-grey-bg-1">--}}
{{--                <ul class="pages">--}}
{{--                    <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>--}}
{{--                    <li id="pageNumber"><a class="active text-color-yellow">1</a></li>--}}
{{--                    <li id="next"><a class="">Next</a></li>--}}
{{--                    <input type="number" id="goToPageNumber_1" maxlength="6" size="4" class="pageinput white-bg"><a--}}
{{--                        id="goPageBtn_1">GO</a>--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </section>--}}

    <!-- Credit Reference model -->

    <div class="modal credit-modal" id="openCreditpopup">
        <div class="modal-dialog">
            <div class="modal-content light-grey-bg-1">
                <div class="modal-header border-0">
                    <h4 class="modal-title text-color-blue-1">Credit Reference Edit</h4>
                    <button type="button" class="close" data-dismiss="modal"><img
                            src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                </div>
                <form method="post" action="{{route('storeReference')}}">
                    @csrf
                    <input type="hidden" name="player_id" id="player_id" value="">
                    <input type="hidden" name="route_name" value="home">
                    <div class="modal-body">
                        <div class="form-modal addform-modal">
                            <div class="addform-block">
                                <div>
                                    <span>Current</span>
                                    <span>
                                    <input type="text" id="creditapp" name="" maxlength="16"
                                           class="form-control white-bg" readonly="" value="0">
                                    <em class="text-color-red">*</em>
                                </span>
                                    <span class="text-danger cls-error"></span>
                                </div>

                                <div>
                                    <span>New</span>
                                    <span>
                                    <input type="text" id="credit" name="credit" placeholder="" maxlength="16"
                                           class="form-control white-bg" onkeypress="return isNumberKey(event)">
                                    <em class="text-color-red">*</em>
                                </span>

                                    <span class="text-danger cls-error" id="errnew_amount"></span>
                                </div>
                                <div>
                                    <span>Password</span>
                                    <span>
                                    <input type="password" id="current_pass" name="current_pass" placeholder=""
                                           maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                                    <span class="text-danger cls-error" id="errcurrent_pass"></span>
                                </div>
                            </div>

                            <div class="button-wrap pb-0">
                                <input type="submit" value="Submit" name="addreference_btn" id="addreference_btn"
                                       class="submit-btn text-color-yellow">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        var agent_id = 0;

        $(document).ready(function () {
            var switchStatus = false;
            $("#dealy_time").on('change', function () {
                if ($(this).is(':checked')) {
                    switchStatus = $(this).is(':checked');
                    //alert("bhbjk"+switchStatus);// To verify
                    if (switchStatus == true) {
                        //$('.checked-hide').show();
                        $(".checked-hide").css("display", "block");
                    }
                } else {
                    switchStatus = $(this).is(':checked');
                    //alert("5656"+switchStatus);// To verify
                    if (switchStatus == false) {
                        $('.checked-hide').hide();
                    }
                }
            });
        });
        $(document).ready(function () {
            var switchStatus = false;
            $("#dealy_time1").on('change', function () {
                if ($(this).is(':checked')) {
                    switchStatus = $(this).is(':checked');
                    //alert("bhbjk"+switchStatus);// To verify
                    if (switchStatus == true) {
                        //$('.checked-hide').show();
                        $(".checked-hide1").css("display", "block");
                    }
                } else {
                    switchStatus = $(this).is(':checked');
                    //alert("5656"+switchStatus);// To verify
                    if (switchStatus == false) {
                        $('.checked-hide1').hide();
                    }
                }
            });
        });

        var $rows = $('.search-result tr');
        $('#userSearch').keyup(function () {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            backpagedata(agent_id,1,val);
            subpagedata(agent_id,1,val);
        });

        $('.refreshbtn').click(function () {
            window.location.href = "{{ route('home')}}";
        });

        $('input[name="dealy_time"]').keyup(function () {
            var dval = parseInt($('#dealy_time').val());
            var dbval = parseInt('{{$loginuser->dealy_time}}');

            $('#errdly').html('');
            if (dval < dbval) {
                $('#errdly').html('Enter Delay Time Greater than ' + dbval);
                return false;
            }
        });

        $('body').on('click','.setting',function () {
            var user_id = $(this).attr("data-id");
            var agent = $(this).attr("data-agent");
            var username = $(this).attr("data-username");
            var status = $(this).attr("data-status");

            $(".modal-body #user_id").val(user_id);
            $(".status_id #agent").text(agent);
            $(".status_id #username").text(username);
            $(".status_id #status").text(status);
            if (status == 'active') {
                $(".status_id #status").addClass('text-color-green');
                $(".status_id #status").removeClass('text-color-red');
                $(".status_id #status").removeClass('text-color-darkblue');
            }
            if (status == 'suspend') {
                $(".status_id #status").addClass('text-color-red');
                $(".status_id #status").removeClass('text-color-green');
                $(".status_id #status").removeClass('text-color-darkblue');
            }
            if (status == 'locked') {
                $(".status_id #status").addClass('text-color-darkblue');
                $(".status_id #status").removeClass('text-color-red');
                $(".status_id #status").removeClass('text-color-green');
            }

            //
            $(".but_suspend").addClass(user_id);

            if (status == 'active') {
                $(".active-sts").addClass("disable");
                $(".suspend-sts").removeClass("disable");
                $(".locked-sts").removeClass("disable");
            }

            if (status == 'suspend') {
                $(".suspend-sts").addClass("disable");
                $(".active-sts").removeClass("disable");
                $(".locked-sts").removeClass("disable");
            }

            if (status == 'locked') {
                $(".locked-sts").addClass("disable");
                $(".active-sts").removeClass("disable");
                $(".suspend-sts").removeClass("disable");
            }
        });

        $(".check").click(function () {
            var checkval = $(this).data('check');
            $(".appendVal").attr("id", checkval);
        });

        function chngstusval(val) {
            //alert(val);
            var status = val;
            var password = $('#spassword').val();
            var user_id = $('#user_id').val();
            var _token = $("input[name='_token']").val();

            $.ajax({
                type: "post",
                url: '{{route("suspend_pa")}}',
                data: {"_token": "{{ csrf_token() }}", "password": password, "user_id": user_id, "status": status},
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    if (data.result == 'success') {
                        // window.location.href = "{{ route('home')}}";
                        toastr.success('Status Change Successfully');
                        location.reload();
                    }

                    if (data.result == 'error') {
                        toastr.error('Password is Empty');
                        //$(".alert").append("");
                    }
                }
            });
        }

        getDashboardStatistics();

        function getDashboardStatistics() {
            $.ajax({
                type: "GET",
                url: '{{route("dashboard.statistics")}}',
                beforeSend: function () {
                },
                complete: function () {
                },
                success: function (data) {
                    console.log("data: ", data);

                    $("#remaining-wrap .balance").html(data.balance);
                    $("#remaining-wrap .hirUser_bal").html(data.hirUser_bal);
                    $("#remaining-wrap .remain_bal").html(data.remain_bal);
                    $("#remaining-wrap .totalClientBal").html(data.totalClientBal);
                    $("#remaining-wrap .totalExposure").html(data.totalExposure);
                }
            });
        }

        subpagedata(0,1);
        // backpagedata(0);

        var comunityPL = 0;

        var comuunityPLValues = [];

        function updateCommunityPL() {

            if(comuunityPLValues.length > 1) {

                // console.log("comuunityPLValues: ",comuunityPLValues)

                // var oldVal = $('#ledger_exposure_div').attr('data-old-val');
                // var newVal = parseFloat(oldVal) + parseFloat(total_ref_pl);

                var newVal =  comuunityPLValues.reduce(function(a, b) { return a + b; }, 0);
                // $('#ledger_exposure_div').attr('data-old-val', newVal);

                newVal = newVal.toFixed(2);

                if (newVal <= 0) {
                    $('#ledger_exposure_div').text(newVal * -1);
                    $('.exp_div').addClass('text-color-green');
                } else {
                    $('#ledger_exposure_div').text(newVal);
                    $('.exp_div').addClass('text-color-red');
                }
            }
        }

        function backpagedata(val,page,search='') {
            var user_id = val;

            $.ajax({
                type: "post",
                url: '{{route("agentSubBackDetail")}}',
                data: {"_token": "{{ csrf_token() }}", "user_id": user_id, "page":page,'search':search},

                beforeSend: function () {
                    // $('#site_bet_loading1').show();
                },
                complete: function () {
                    // $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    $("#playerData").html(data.html);
                    $(".player.pagination-wrap").html(data.pagination);

                    comunityPL++;
                    if(comuunityPLValues.length < 2) {
                        comuunityPLValues.push(data.total_ref_pl);
                    }
                    updateCommunityPL();
                }
            });
        }

        function subpagedata(val,page, search='') {
            var user_id = val;
            agent_id = val;
            if(page == 1) {
                backpagedata(val, page,search);
            }
            $.ajax({
                type: "post",
                url: '{{route("agentSubDetail")}}',
                data: {"_token": "{{ csrf_token() }}", "user_id": user_id,"page":page,"search":search},

                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },

                success: function (data) {
                    $('#bodyData').html(data.html);
                    $(".agentlistadmin").html('<a href="{{route('home')}}"><span class="blue-bg text-color-white">{{$getUser->agent_level}}</span><strong id="{{$getUser->id}}" >{{$getUser->user_name}}</strong></a> <img src="/asset/img/arrow-right2.png">');
                    $(".agentlist").show();
                    $(".agentlist").html(data.breadcurm);
                    $(".agent.pagination-wrap").html(data.pagination);
                    if(comuunityPLValues.length < 2) {
                        comuunityPLValues.push(data.total_ref_pl);
                    }
                    updateCommunityPL();

                    // $("#playerData").html(spl[2]);
                }
            });
        }

        $(document).on('click', '.agent.pagination-wrap .pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            subpagedata(agent_id, page);
        });

        $(document).on('click', '.player.pagination-wrap .pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            backpagedata(agent_id, page);
        });

        $('#addreference_btn').click(function () {
            var new_amount = $('#credit').val();
            var current_pass = $('#current_pass').val();
            $('#errnew_amount').html('');
            $('#errcurrent_pass').html('');
            if (new_amount == '') {
                $('#errnew_amount').html('This Field is required');
                return false;
            }

            if (current_pass == '') {
                $('#errcurrent_pass').html('This Field is required');
                return false;
            }
        });

        $(".user_name").keyup(function () {
            $('#errsub').html('');
            var uvalue = this.value;
            $.ajax({
                type: 'get',
                url: "{{route('getusername')}}",
                data: {uvalue: uvalue},

                success: function (data) {
                    $('#errsub').html('');
                    if (data.result != '') {
                        $(".userNm").addClass("text-danger");
                        $('#errsub').html('Username is not available');
                    } else if (uvalue == '') {
                        $('#errsub').html('This Field is required');
                    } else {
                        $(".userNm").removeClass("text-danger");
                        $(".userNm").css("color", "green");
                        $('#errsub').html('Username is available');
                    }
                }
            });
        });


        $('#agentSubmit').click(function () {
            var errsub = $('#errsub').text();
            var errdly = $('#errdly').text();
            var agent_level = $('#agent_level').val();
            var user_name = $('#user_name').val();
            var password = $('#password').val();
            var confirm_password = $('#confirm_password').val();
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var commission = $('#commission').val();
            var partnership_perc = $('#partnership_perc').val();
            var time_zone = $('#time_zone').val();

            $('#errage').html('');
            $('#errdly').html('');
            $('#errsub').html('');
            $('#errpass').html('');
            $('#errcnpass').html('');
            $('#errfn').html('');
            $('#errln').html('');
            $('#errcm').html('');
            $('#errtim').html('');

            if (errsub == 'Username is not available') {
                $('#errsub').html('Username is not available');
                return false;
            }

            if (agent_level == '') {
                $('#errage').html('This Field is required');
                return false;
            }

            if (user_name == '') {
                $('#errsub').html('This Field is required');
                return false;
            }

            if (password == '') {
                $('#errpass').html('This Field is required');
                return false;
            }

            if (password != '') {
                if (password.length < 4) {
                    $('#errpass').html('Password must be atleast 4 char long!');
                    return false;
                }
            }

            if (confirm_password != '') {
                if (password != confirm_password) {
                    $('#errcnpass').html('Confirm password must match with password');
                    return false;
                }
            }

            if (confirm_password == '') {
                $('#errcnpass').html('This Field is required');
                return false;
            }

            if (first_name == '') {
                $('#errfn').html('This Field is required');
                return false;
            }

            if (last_name == '') {
                $('#errln').html('This Field is required');
                return false;
            }

            if (commission == '') {
                $('#errcm').html('This Field is required');
                return false;
            }

            if (errdly != '') {
                $('#errdly').html(errdly);
                return false;
            }

            if (time_zone == '') {
                $('#errtim').html('This Field is required');
                return false;
            }
        });

        $("#puser_name").keyup(function () {
            $('#errplyrusername').html('');
            var uvalue = this.value;
            $.ajax({
                type: 'get',
                url: "{{route('getusername')}}",
                data: {uvalue: uvalue},
                success: function (data) {
                    $('#errplyrusername').html('');
                    if (data.result != '') {
                        $(".pusrnamecls").addClass("text-danger");
                        $('#errplyrusername').html('Username is not available');
                    } else if (uvalue == '') {
                        $('#errplyrusername').html('This Field is required');
                    } else {
                        $(".pusrnamecls").removeClass("text-danger");
                        $(".pusrnamecls").css("color", "green");
                        $('#errplyrusername').html('Username is available');
                    }
                }
            });
        });

        $('#addplayer').click(function () {
            var errsub = $('#errplyrusername').text();
            var user_name = $('#puser_name').val();
            var password = $('#ppassword').val();
            var confirm_password = $('#pcpassword').val();
            var first_name = $('#pfname').val();
            var last_name = $('#planame').val();
            var commission = $('#pcommission').val();
            var time_zone = $('#ptime').val();

            $('#errage').html('');
            $('#errsub').html('');
            $('#errplyrpass').html('');
            $('#errplyrcpass').html('');
            $('#errplyrfname').html('');
            $('#errplyrlname').html('');
            $('#errplyrerrcm').html('');
            $('#errplyrtime').html('');

            if (errsub == 'Username is not available') {
                $('#errsub').html('Username is not available');
                return false;
            }

            if (user_name == '') {
                $('#errsub').html('This Field is required');
                return false;
            }

            if (password == '') {
                $('#errplyrpass').html('This Field is required');
                return false;
            }

            if (password != '') {
                if (password.length < 4) {
                    $('#errplyrpass').html('Password must be atleast 4 char long!');
                    return false;
                }
            }

            if (confirm_password != '') {
                if (password != confirm_password) {
                    $('#errplyrpass').html('Confirm password must match with password');
                    return false;
                }
            }

            if (confirm_password == '') {
                $('#errplyrcpass').html('This Field is required');
                return false;
            }

            if (first_name == '') {
                $('#errplyrfname').html('This Field is required');
                return false;
            }

            if (last_name == '') {
                $('#errplyrlname').html('This Field is required');
                return false;
            }

            if (commission == '') {
                $('#errplyrerrcm').html('This Field is required');
                return false;
            }

            if (time_zone == '') {
                $('#errplyrtime').html('This Field is required');
                return false;
            }
        });

        $("body").on('click','.openCreditpopup',function () {

            console.log("call");

            $('#player_id').val(this.id);
            $('#creditapp').val($(this).attr("data-credit"));
            $('#openCreditpopup').modal('show');
        });
    </script>

@endsection
