<?php    
$url=explode("/",$_SERVER['REQUEST_URI']);
$page1=$url[1];
$page=explode(".php",$page1);
$page2=$page[0];    
use App\User;
$loginuser = Session::get('playerUser'); 
$user = User::where('id',$loginuser->id)->first();
use App\setting;
$getUser = Session::get('playerUser');
$settings =setting::first();
?>
<style type="text/css">
    .close {
        font-size: 15px;
    }
</style>

@if(!empty($getUser))
    @if(!empty($settings->user_msg))
    <div class="news-addvertisment black-gradient-bg text-color-white">
        <h4>News</h4>
        <marquee>
            <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
        </marquee>
    </div>
    @endif
@endif
<div class="dash-menu white-bg">
    <div class="topmenu-left black-bg-2">
        <div class="barsicon text-color-yellow1">
            <a><img src="{{asset('/public/asset/img/leftmenu-arrow1.png')}}"><img class="hover-img" src="{{ URL::to('img/leftmenu-arrow2.png')}}"></a>
        </div>
        <div class="soprts-link text-color-yellow1"><a>My Account</a></div>
    </div>
    <ul>
        <li <?php if($page2=='myprofile') { ?> class="active" <?php } ?>>
            <a href="{{route('myprofile')}}" class="text-color-black2">My Profile</a>
        </li>
        <li <?php if($page2=='balance-overview') { ?> class="active" <?php } ?>>
            <a href="{{route('balance-overview')}}" class="text-color-black2">Balance Overview</a>
        </li>
        <li <?php if($page2=='account-statement') { ?> class="active" <?php } ?>>
            <a href="{{route('account-statement')}}" class="text-color-black2">Account Statement</a>
        </li>
        <li <?php if($page2=='my-bets') { ?> class="active" <?php } ?>>
            <a href="{{route('my-bets')}}" class="text-color-black2">My Bets</a>
        </li>
        <li <?php if($page2=='activity-log') { ?> class="active" <?php } ?>>
            <a href="{{route('activity-log')}}" class="text-color-black2">Activity Log</a>
        </li>
        <li>
            <a data-toggle="modal" data-target="#mypwd" class="text-color-black2">Change Password</a>
        </li>
    </ul>
</div>
<div class="modal credit-modal changepwd-modal" id="mypwd">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Change Password</h4>
                <button type="button" class="close" data-dismiss="modal">X</button>
            </div>
            <form method="post" action="{{route('updateUserPassword',$user->id)}}" id="agentform" autcomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="form-modal">
                        <div>
                            <span>Your Password</span>
                            <span><input id="yourpwd" name="yourpwd" type="password" placeholder="Enter Your Old Password" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="erryourpwd"></span>
                        <div>
                            <span>New Password</span>
                            <span><input type="password" id="newpwd" name="newpwd" placeholder="Enter New Password" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errnewpwd"></span>
                        <div>
                            <span>New Password Confirm</span>
                            <span><input type="password" id="newcpwd" name="newcpwd" placeholder="Enter New Password Confirm" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errnewcpwd"></span>
                    </div>
                    <div class="button-wrap">
                        <button class="submit-btn1 text-color-yellow" name="btnpwd" id="btnpwd"> Change </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
$('#btnpwd').click(function () {
    var newpwd = $('#newpwd').val();
    var newcpwd = $('#newcpwd').val();
    var yourpwd = $('#yourpwd').val();
    $('#errnewpwd').html('');
    $('#errnewcpwd').html('');
    $('#erryourpwd').html('');
    if(yourpwd == ''){
        $('#erryourpwd').html('This Field is required');
        return false;
    }
    if(newpwd == ''){
        $('#errnewpwd').html('This Field is required');
        return false;
    }
    if(newpwd !=''){
        if(newpwd.length < 8){  
            $('#errnewpwd').html('Password must be atleast 8 char long!');
            return false;
        }
    }
    if(newcpwd == ''){
        $('#errnewcpwd').html('This Field is required');
        return false;
    }
    if(newcpwd !=''){           
        if(newpwd != newcpwd){
            $('#errnewcpwd').html('Confirm password must match with password');
            return false;
        }
    }
});
</script>