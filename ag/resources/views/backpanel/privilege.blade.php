@extends('layouts.app')
@section('content')

<style type="text/css">
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

<section class="pb-5">
    <div class="container">
        <div class="block_css alert alert-success alert-dismissible fade show" role="alert" style="display:none">
            Privilage Change Successfully.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="post" action="">
            @csrf
            <div class="inner-title player-right justify-content-between py-2">
                <h2></h2>
                <div class="player-right">
                    <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddAgent">
                        <?php  $url=$_SERVER['REQUEST_URI']; ?>
                        <img src="{{ URL::to('asset/img/user-add.png')}}">ADD PRIVILEGE
                    </a>
                </div>
            </div>
            <div class="list-games-block privilege_table">
                <div class="privileges_wrap mb-2 mt-2">
                    <h5>Privileges</h5>
                    <div class="previllage_content">
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Downline List</span>
                        </div>
                        {{-- <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Manual Match Add</span>
                        </div> --}}
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Sports Main Market</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Manage Fancy</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Fancy History</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Match History</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>My Account</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>My Report</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Bet-List</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Bet-ListLive</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Live Casino</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Risk Management</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Player Banking</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Agent Banking</span>
                        </div>
                        {{-- <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Sports Leage</span>
                        </div> --}}
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Add Balance</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Message</span>
                        </div>
                        <div class="previlage_items white-bg">
                            <i class="fas fa-check"></i>
                            <span>Casino Manage</span>
                        </div>
                    </div>
                </div>
                <table id="example1" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="light-grey-bg">Sr.No.</th>
                            <th class="light-grey-bg">Action</th>
                            <th class="light-grey-bg">User Name</th>
                            <th class="light-grey-bg">Downline List</th>
                            {{-- <th class="light-grey-bg">Manual Match Add</th> --}}
                            <th class="light-grey-bg">Sports Main Market</th>
                            <th class="light-grey-bg">Manage Fancy</th>
                            <th class="light-grey-bg">Fancy History</th>
                            <th class="light-grey-bg">Match History</th>
                            <th class="light-grey-bg">My Account</th>
                            <th class="light-grey-bg">My Report</th>
                            <th class="light-grey-bg">Bet-List</th>
                            <th class="light-grey-bg">Bet-ListLive</th>
                            <th class="light-grey-bg">Live Casino</th>
                            <th class="light-grey-bg">Risk Management</th>
                            <th class="light-grey-bg">Player Banking</th>
                            <th class="light-grey-bg">Agent Banking</th>
                            {{-- <th class="light-grey-bg">Sports Leage</th> --}}
                            <th class="light-grey-bg">Add Balance</th>
                            <th class="light-grey-bg">Message</th>
                            <th class="light-grey-bg">Casino Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count=1; ?>
                        @foreach($users as $user)
                        <tr>
                            <td class="white-bg">{{$count}}</td>
                            <td class="white-bg">
                                <a href="#" class="default_btn green-bg text-color-white" onclick="openpopup('{{$user->id}}');" data-toggle="modal" data-target="#myChangePassword">P</a>

                                <input type="button"class="default_btn red-bg delete-confirm text-color-white" value="D" onclick="deleteprvlg('{{$user->id}}');">
                            </td>
                            <td class="white-bg">{{$user->user_name}}</td>
                            <td class="white-bg"><input type="checkbox" name="list_client" id="list_client{{$user->id}}" onclick="changestatus('{{$user->id}}','list_client');" {{ $user->list_client == '1' ? 'checked' : '' }}></td>
                            {{-- <td class="white-bg"><input type="checkbox" name="main_market" id="main_market{{$user->id}}" onclick="changestatus('{{$user->id}}','main_market');" {{ $user->main_market == '1' ? 'checked' : '' }}></td> --}}
                            <td class="white-bg"><input type="checkbox" name="sports_main_market" id="sports_main_market{{$user->id}}" onclick="changestatus('{{$user->id}}','sports_main_market');" {{ $user->sports_main_market == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="manage_fancy" id="manage_fancy{{$user->id}}" onclick="changestatus('{{$user->id}}','manage_fancy');" {{ $user->manage_fancy == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="fancy_history" id="fancy_history{{$user->id}}" onclick="changestatus('{{$user->id}}','fancy_history');" {{ $user->fancy_history == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="match_history" id="match_history{{$user->id}}" onclick="changestatus('{{$user->id}}','match_history');" {{ $user->match_history == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="my_account" id="my_account{{$user->id}}" onclick="changestatus('{{$user->id}}','my_account');" {{ $user->my_account == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="my_report" id="my_report{{$user->id}}" onclick="changestatus('{{$user->id}}','my_report');" {{ $user->my_report == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="bet_list" id="bet_list{{$user->id}}" onclick="changestatus('{{$user->id}}','bet_list');" {{ $user->bet_list == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="bet_list_live" id="bet_list_live{{$user->id}}" onclick="changestatus('{{$user->id}}','bet_list_live');" {{ $user->bet_list_live == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="live_casino" id="live_casino{{$user->id}}" onclick="changestatus('{{$user->id}}','live_casino');" {{ $user->live_casino == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="risk_management" id="risk_management{{$user->id}}" onclick="changestatus('{{$user->id}}','risk_management');" {{ $user->risk_management == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="player_banking" id="player_banking{{$user->id}}" onclick="changestatus('{{$user->id}}','player_banking');" {{ $user->player_banking == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="agent_banking" id="agent_banking{{$user->id}}" onclick="changestatus('{{$user->id}}','agent_banking');" {{ $user->agent_banking == '1' ? 'checked' : '' }}></td>
                            {{-- <td class="white-bg"><input type="checkbox" name="sports_leage" id="sports_leage{{$user->id}}" onclick="changestatus('{{$user->id}}','sports_leage');" {{ $user->sports_leage == '1' ? 'checked' : '' }}></td> --}}
                            <td class="white-bg"><input type="checkbox" name="add_balance" id="add_balance{{$user->id}}" onclick="changestatus('{{$user->id}}','add_balance');" {{ $user->add_balance == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="message" id="message{{$user->id}}" onclick="changestatus('{{$user->id}}','message');" {{ $user->message == '1' ? 'checked' : '' }}></td>
                            <td class="white-bg"><input type="checkbox" name="casino_manage" id="casino_manage{{$user->id}}" onclick="changestatus('{{$user->id}}','casino_manage');" {{ $user->casino_manage == '1' ? 'checked' : '' }}></td>
                        </tr>
                        <?php $count++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</section>

 <!-- Password Model -->
<div class="modal credit-modal" id="myChangePassword">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Change Password</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>Password</span>
                                <span><input id="passwordprivi" type="password" name="passwordprivi" placeholder="Password" class="form-control white-bg user_name">
                                    <em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errchngpass"></span>
                            <div>
                                <span>Confirm Password</span>
                                <span><input id="passwordcprivi" name="passwordcprivi" type="password" placeholder="Confirm Password" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errchngcpass"></span>
                            <div>
                                <span>Transaction Code</span>
                                <span><input id="transprivi" name="transprivi" type="password" placeholder="Transaction Code" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errchngtrans"></span>
                        </div>
                        <div class="button-wrap pb-0">
                            <input type="button" value="Submit" name="" data-count=""  data-id=""  class="submit-btn text-color-yellow changepassSubmit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal credit-modal" id="myAddAgent">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Add Privilege</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('storeuser')}}" id="agentform">
                    <select class="form-control white-bg" name="agent_level" id="agent_level" style="display:none">
                        <option value="SL">Simple Admin(SL)</option>
                    </select>
                    @csrf
                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>User Name</span>
                                <span><input id="user_name" type="text" name="user_name" placeholder="Enter" class="form-control white-bg user_name"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="userNm text-danger cls-error" id="errsub"></span>
                            <div>
                                <span>Password</span>
                                <span><input id="password" name="password" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errpass"></span>
                            <div>
                                <span>Confirm Password</span>
                                <span><input id="confirm_password" name="confirm_password" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errcpass"></span>
                        </div>
                        <div class="button-wrap pb-0">
                            <input type="submit" value="Crate" name="" id="agentSubmit" class="submit-btn text-color-yellow">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
var _token = $("input[name='_token']").val();
function deleteprvlg(val){
    var txt;
    if (confirm("Are you sure!")) {
        txt = "OK";
    } else {
        txt = "CANCEL";
    }
    if(txt=='OK'){
        $.ajax({
            type: "POST",
            url: '{{route("deleteprvlg")}}',
            data: {
                _token: _token,
                val:val,
            },
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data) {
                if (data.result == 'success'){
                    location.reload();
                    toastr.success('Privilege user deleted successfully!');
                }
            }
        });
    }
}
function openpopup(val){
    $('#user_id').val(val);
}
$('.changepassSubmit').click(function() {
    var _token = $("input[name='_token']").val();
    var userId = $('#user_id').val();
    var passwordprivi = $('#passwordprivi').val();
    var passwordcprivi = $('#passwordcprivi').val();
    var transaction_code = $('#transprivi').val();
    $('#errchngpass').html('');
    $('#errchngcpass').html('');
    $('#transpass').html('');
    var retu = 1;
    if (passwordprivi == '') {
        toastr.error('Password can not be blank!');
        retu = 0;
    }
    if (passwordprivi != '') {
        if (passwordprivi.length < 8) {
            toastr.error('Password must be atleast 8 char long!');
            retu = 0;
        }
    }
    if (passwordcprivi != '') {
        if (passwordprivi != passwordcprivi) {
            toastr.error('Confirm password must match with password!');
            retu = 0;
        }
    }
    if (passwordcprivi == '') {
        toastr.error('Confirm password can not be blank!');
        retu = 0;
    }
    if (transaction_code == '') {
        toastr.error('Transaction Code can not be blank!');
        retu = 0;
    }
    if(retu==1){
        $.ajax({
            type: "POST",
            url: '{{route("changePrivilegePass")}}',
            data: {
                _token: _token,
                passwordprivi: passwordprivi,
                transaction_code: transaction_code,
                userId:userId,
            },
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data) {
                if (data.result == 'error'){
                    toastr.error('Your transaction password is incorrect!');
                }
                if (data.result == 'success'){
                  location.reload();
                  toastr.error('Password changed successfully!');
                }
            }
        });
    }
    if(retu == 1){
        return true;
    }else{
        return false;
    }
});
$(".user_name").keyup(function() {
    $('#errsub').html('');
    var uvalue = this.value;
    $.ajax({
        type: 'get',
        url: "{{route('getusername')}}",
        data: {
            uvalue: uvalue
        },
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data) {
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
$('#agentSubmit').click(function() {
    var errsub = $('#errsub').text();
    var agent_level = $('#agent_level').val();
    var user_name = $('#user_name').val();
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();
    $('#errage').html('');
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
        toastr.error('Username can not be blank!');
        return false;
    }
    if (password == '') {
        toastr.error('Password can not be blank!');
        return false;
    }
    if (password != '') {
        if (password.length < 4) {
            toastr.error('Password must be atleast 4 char long!');
            return false;
        }
    }
    if (confirm_password != '') {
        if (password != confirm_password) {
            toastr.error('Confirm password must match with password!');
            return false;
        }
    }
    if (confirm_password == '') {
        toastr.error('Confirm password can not be blank!');
        return false;
    }
});
function changestatus(id, nameatt) {
    var gstatus = '';
    if ($("#" + nameatt + id).is(':checked')) {
        gstatus = 1;
    } else {
        gstatus = 0;
    }
    var _token = $("input[name='_token']").val();

    $.ajax({
        type: 'POST',
        url: "{{route('changeprivilageuser')}}",
        data: {
            "_token": _token,
            uid: id,
            gstatus: gstatus,
            nameatt: nameatt,
        },
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data) {

            if (data.result = 'success') {
                toastr.success('Privilage change successfully.!');
            }
        }
    });
}
</script>
@endsection
