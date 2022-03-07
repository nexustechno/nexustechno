@extends('layouts.app')
@section('content')


    <style>
        body {
            overflow: hidden;
        }
        .betloaderimage1{
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

        .loading1 li{
            list-style: none;
            text-align: center;
            font-size: 11px;
        }
    </style>
    <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none" >
        <ul class="loading1">
            <li>
                <img src="/asset/front/img/loaderajaxbet.gif">
            </li>
            <li>Loading...</li>
        </ul>
    </div>
    <?php
    $loginuser = Auth::user();
    use App\CreditReference;
    use App\Http\Controllers\AgentController;
    ?>
    <section class="balance-section">
        <div class="container">
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
            <div class="inner-title-2 text-color-blue-2">
                <h2>Agent Banking</h2>
            </div>
            @if (session('alert'))
                <div class="alert alert-danger">
                    {{ session('alert') }}
                </div>
            @endif

            <div class="mainbalance-section">
                <div class="balance-block light-grey-bg-2">
                    <span class="smtxt-balance text-color-blue-1"> Your Balance </span>
                    <span class="lgtxt-balance">
                	<span class="text-color-blue-1">{{ $website->currency }}</span> {{number_format($balance,2, '.', '')}}
                </span>
                    <div class="search-wrap">
                        <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z"
                                fill="rgb(30,30,30"></path>
                        </svg>
                        <div>
                            <input class="search-input navy-light-bg" type="text" name="userId" id="userSearch"
                                   placeholder="Find member...">
                            <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                        </div>
                    </div>
                </div>
                <form action="{{route('addAgentBanking')}}" method="post" name="frmbanking" id="frmbanking">
                    @csrf
                    <table
                        class="table custom-table balance-table white-bg text-color-blue-2 search-result dataTable no-footer"
                        id="pager1">
                        <thead>
                        <tr>
                            <th class="light-grey-bg">AgentID <i class="fas fa-sort-down"></i></th>
                            <th class="light-grey-bg">Balance</th>
                            <th class="light-grey-bg">Available D/W</th>
                            <th class="light-grey-bg">Exposure</th>
                            <th class="light-grey-bg">Deposit / Withdraw</th>
                            <th class="light-grey-bg">Credit Reference</th>
                            <th class="light-grey-bg">Reference P/L</th>
                            <th class="light-grey-bg">Remark</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; $i = 0; $totBal = 0; $totAvl = 0; $totExp = 0; $totcRef = 0; $totrPL = 0;$classref = '';?>
                        @foreach($agent as $players)
                            <?php
                            $credit_data = CreditReference::where('player_id', $players->id)->select('*')->first();
                            $credit = 0;
                            if (@$credit_data['credit'] != '') {
                                $credit = @$credit_data['credit'];
                            }
                            $depParent = AgentController::userBalance($players->id);
                            $calData = explode("~", $depParent);

                            //$balance=@$credit_data['remain_bal'];
                            $balance = $credit_data['available_balance_for_D_W'] + $calData[0] + $calData[1];
                            $available_balance = @$credit_data['available_balance_for_D_W'] - @$credit_data['exposure'];
                            $exposer = @$credit_data['exposure'];
                            ?>
                            <tr>
                                <td class="align-L white-bg">
                                    <span>{{$no}}</span>
                                    <div class="ico_account">
                                        <span
                                            class="orange-bg text-color-white">{{$players->agent_level}}</span>{{$players->user_name}}
                                        [{{$players->first_name}} {{$players->last_name}} ]
                                    </div>
                                </td>
                                <td class="white-bg">{{number_format($balance,2, '.', '')}}</td>
                                <td class="white-bg"
                                    id="available_balance{{$i}}">{{number_format($available_balance,2, '.', '')}}</td>
                                <td class="text-color-red white-bg">({{number_format(abs($exposer),2, '.', '')}})</td>
                                <td class="white-bg amount-deposit">
                                    <ul class="deposit-btn">
                                        <li>
                                            <a id="dBtn" data-pid="{{$i}}"
                                               class="logbtn grey-gradient-bg depositebtn selectD">D</a>
                                            <input type="hidden" name="player_deposite[{{$i}}]"
                                                   id="player_deposite{{$i}}" value="D"/>
                                        </li>
                                        <li>
                                            <a id="wBtn" data-pid="{{$i}}"
                                               class="logbtn grey-gradient-bg withdrawbtn">W</a>
                                            <input type="hidden" name="player_withdraw[{{$i}}]"
                                                   id="player_withdraw{{$i}}" value=""/>
                                        </li>
                                    </ul>
                                    <input type="hidden" id="creditref{{$i}}" name="creditref[{{$i}}]"
                                           value="{{@$credit_data['id']}}"/>
                                    <input type="text" name="txtamount[{{$i}}]" id="txtamount{{$i}}"
                                           class="form-control allowNumeric" placeholder="0">
                                    <a class="fullbtncls logbtn grey-gradient-bg" data-fid="{{$i}}" id="fullBtn{{$i}}">Full</a>
                                </td>
                                <td class="white-bg">
                                    <div class="credit-amount" id="credit_amount_div{{$i}}">
                                        <input type="hidden" name="creditvalue[{{$i}}]" id="creditvalue{{$i}}"
                                               value=""/>

                                        <input type="text" name="creditamount[{{$i}}]" id="creditamount{{$i}}"
                                               class="form-control " placeholder="0" style="display:none"
                                               value="{{$credit}}">
                                        <a style="margin-right: 10px;" id="{{$players->id}}"
                                           class="text-color-blue-light ">{{$credit}}</a>
                                        <a id="btnEdit{{$i}}" data-credit="{{$i}}"
                                           class="logbtn grey-gradient-bg creditEdit creditbtn">Edit</a>
                                    </div>
                                </td>
                                <?php
                                /*$refPL = (int)$remain_bal-$credit;*/
                                $refPL = $balance - (int)$credit;
                                $redR = 0;
                                $redG = 0;
                                $totRefC = 0;
                                if ($refPL < 0) {
                                    $redR += $refPL;
                                    $class = "text-color-red";
                                } else {
                                    $redG += $refPL;
                                    $class = "text-color-green";
                                }
                                // $totRefC = $redG-$redR;

                                ?>
                                <td class="{{$class}} white-bg">({{number_format(abs($refPL),2, '.', '')}})</td>
                                <td class="white-bg"><input type="text" name="remark[{{$i}}]" id="remark{{$i}}"
                                                            class="form-control" value="Deposit" placeholder="Remark">
                                </td>
                            </tr>
                            <?php $no++; $i++; ?>
                            <?php
                            $totBal += $balance;
                            $totAvl += $available_balance;
                            $totExp += $exposer;
                            $totcRef += $credit;
                            $totrPL += $redG - abs($redR);

                            if ($totrPL < 0) {
                                $classref = "text-color-red";
                            } else {
                                $classref = "text-color-green";
                            }

                            ?>
                        @endforeach
                        <tr>
                            <td><b>Total</b></td>
                            <td><b>{{number_format($totBal,2, '.', '')}}</b></td>
                            <td><b>{{number_format($totAvl,2, '.', '')}}</b></td>
                            <td><span class="text-color-red"><b>({{number_format(abs($totExp),2, '.', '')}})</b></span>
                            </td>
                            <td></td>
                            <td>
                                <b style="justify-content: center;display: flex;">{{number_format($totcRef,2, '.', '')}}</b>
                            </td>
                            <td class="{{$classref}}"><b>({{number_format(abs($totrPL),2, '.', '')}})</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="clear-block">
                        <a class="logbtn grey-gradient-bg"
                           onclick="document.getElementById('adminpassword').value = ''">Clear All</a>
                        <div class="clearinput dark-grey-bg-1">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <input type="password" name="adminpassword" id="adminpassword" class="form-control"
                                   placeholder="Password">
                            <input type="submit" onclick="return submitPayment()" class="submit-btn text-color-yellow pay-btn" value="Submit Payment">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="modal downlind-modal player-banking-modal" id="mylog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header dark-blue-bg-2">
                    <h4 class="modal-title text-color-white">Banking Logs</h4>
                    <div class="header-raj text-color-blue-2 white-bg">{{$loginuser->user_name}}</div>
                    <button type="button" class="close text-color-white" data-dismiss="modal"><img
                            src="{{ URL::to('asset/img/close-icon.png')}}">Close
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table risk-table-1 white-bg">
                        <tr>
                            <th class="light-grey-bg">Date/Time</th>
                            <th class="light-grey-bg">Deposit</th>
                            <th class="light-grey-bg">Withdraw</th>
                            <th class="light-grey-bg">Balance</th>
                            <th class="light-grey-bg">Remark</th>
                            <th class="light-grey-bg">From/To</th>
                        </tr>
                        <tbody>
                        <tr>
                            <td class="white-bg">2021-03-21 11:35:20</td>
                            <td class="white-bg text-color-green">-</td>
                            <td class="white-bg text-color-red">(4500)</td>
                            <td class="white-bg">600.31</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        <tr>
                            <td class="white-bg">2021-03-20 18:50:52</td>
                            <td class="white-bg text-color-green">5000</td>
                            <td class="white-bg text-color-red">-</td>
                            <td class="white-bg">5200.31</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        <tr>
                            <td class="white-bg">2021-03-02 13:30:56</td>
                            <td class="white-bg text-color-green">-</td>
                            <td class="white-bg text-color-red">(88)</td>
                            <td class="white-bg">500.21</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        <tr>
                            <td class="white-bg">2021-03-02 13:30:39</td>
                            <td class="white-bg text-color-green">500</td>
                            <td class="white-bg text-color-red">-</td>
                            <td class="white-bg">588.21</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        <tr>
                            <td class="white-bg">2021-03-02 13:30:10</td>
                            <td class="white-bg text-color-green">-</td>
                            <td class="white-bg text-color-red">(100)</td>
                            <td class="white-bg">88.21</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        <tr>
                            <td class="white-bg">2021-02-09 15:01:35</td>
                            <td class="white-bg text-color-green">200</td>
                            <td class="white-bg text-color-red">-</td>
                            <td class="white-bg">200.00</td>
                            <td class="white-bg"></td>
                            <td class="white-bg">raju <i class="fas fa-caret-right text-color-grey"></i> Lalu</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="pagination-wrap mb-4">
                        <ul class="pages">
                            <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>
                            <li id="pageNumber"><a class="active text-color-yellow">1</a></li>
                            <li id="next"><a class="">Next</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        function submitPayment() {
            $("#site_bet_loading1").show();
        }

        var $rows = $('.search-result tr');
        $('#userSearch').keyup(function () {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function () {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });

        $(".fullbtncls").click(function () {
            var fid = $(this).data("fid");
            console.log($('#available_balance' + fid).text());
            $('#txtamount' + fid).val($('#available_balance' + fid).text());
        });
        $(".creditbtn").click(function () {
            var fid = $(this).data("credit");
            $('#creditvalue' + fid).val('');
            $(this).html($(this).html() == 'Edit' ? 'Cancel' : 'Edit');
            var id = '#credit_amount_div' + fid
            $(id + " .text-color-blue-light").toggle();
            $(id + " input").toggle();
        });
        $(".depositebtn").click(function () {
            var fid = $(this).data("pid");
            $('#remark' + fid).val('Deposit');
            if ($('#player_deposite' + fid).val() == "") {
                $('#player_withdraw' + fid).val('');
                $('#player_deposite' + fid).val('D');
                $('#btnEdit' + fid).addClass("activeFull");
                $('#fullBtn' + fid).addClass("activeFull");
            } else {
                $('#player_deposite' + fid).val("");
            }
        });
        $(".withdrawbtn").click(function () {
            var fid = $(this).data("pid");
            $('#remark' + fid).val('Withdraw');
            if ($('#player_withdraw' + fid).val() == "") {
                $('#player_deposite' + fid).val("");
                $('#player_withdraw' + fid).val('W');
                $('#btnEdit' + fid).addClass("activeFull");
                $('#fullBtn' + fid).addClass("activeFull");
            } else {
                $('#player_withdraw' + fid).val("");
            }
        });
        $(document).ready(function () {
            $('form').each(function () {
                this.reset()
            });
        });
        $(".allowNumeric").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 45 || e.which > 57)) {
                $("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });
    </script>
@endsection
