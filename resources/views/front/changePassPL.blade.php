<!doctype html>
<?php
$main_url=explode(".",$_SERVER['HTTP_HOST']);
use App\Website;
$website = Website::where('title',$main_url[0])->first();
?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="{{ asset('asset/front/img')}}/{{$website->favicon}}" type="image/x-icon">
    <title>{{$website->title}}</title>
    <link href="{{ asset('asset/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/color-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/jquery-ui.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/responsive.css') }}" rel="stylesheet">
    <!-- Styles -->
    <style>
    .add_balance
    {
        color: #000 !important;
        background: none;
        font-weight: bold;
    }
    </style>
</head>
<?php
$url=explode("/",$_SERVER['REQUEST_URI']);
$page1=$url[1];
$page=explode(".php",$page1);
$page2=$page[0];
$loguser = Auth::user();
use App\setting;
$settings = setting::latest('id')->first();
?>
<body class="white-bg text-color-black1">
    <div class="chpassword_wrapper" style="background-image: url(../public/asset/img/bg-login.jpg);">
        <div class="chpassword-block yellow-bg">
            <div class="chpasscontent-block">
                <div>
                    <ul>
                        <li>Password must have 8 to 15 alphanumeric without white space</li>
                        <li>Password cannot be the same as username/nickname</li>
                        <li>Must contain at least 1 capital letter, 1 samll letter and 1 number</li>
                        <li>Password must not contain any special characters (!,@,#,etc..)</li>
                    </ul>
                </div>
                <div>
                    @if($errors->any())
                        <h4>{{$errors->first()}}</h4>
                    @endif
                    @if(session()->has('success'))
                        <div class="alert alert-success fade in alert-dismissible show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="line-height:23px">
                                <span aria-hidden="true" style="font-size:20px">×</span>
                            </button> {{ session()->get('success') }}
                        </div>
                    @elseif(session()->has('error'))
                        <div class="alert alert-danger fade in alert-dismissible show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="line-height:23px">
                                <span aria-hidden="true" style="font-size:20px">×</span>
                            </button> {{ session()->get('error') }}
                        </div>
                    @endif
                    <h3> Change Password </h3>
                    <form method="post" action="{{route('updatePasswordPL',$id)}}" id="agentform" autcomplete="off">
                        @csrf
                        <input type="hidden" name="username" id="username" value="{{$username}}">
                        <div class="form-group">
                            <input type="password" id="newpwd" name="newpwd" placeholder="New Password" class="form-control">
                        </div>
                        <span class="text-danger cls-error" id="errnewpwd"></span>
                        <div class="form-group">
                            <input type="password" id="newcpwd" name="newcpwd" placeholder="New Password Confirm" class="form-control">
                        </div>
                        <span class="text-danger cls-error" id="errnewcpwd"></span>
                        <div class="form-group mb-2">
                            <input type="password" id="yourpwd" name="yourpwd" placeholder="Old Password" class="form-control">
                        </div>
                        <span class="text-danger cls-error" id="erryourpwd"></span>
                        <button class="login-btn text-color-yellow" name="btnpwd" id="btnpwd"> Change </button>
                    </form>
                </div>
            </div>
            <div class="logo_block black-gradient-bg1">
                <img src="{{ URL::to('/ag/asset/front/img')}}/{{$website->logo}}" alt="Logo">
            </div>
        </div>
    </div>
@include('footer')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$('#btnpwd').click(function () {
    var newpwd = $('#newpwd').val();
    var newcpwd = $('#newcpwd').val();
    var yourpwd = $('#yourpwd').val();
    $('#errnewpwd').html('');
    $('#errnewcpwd').html('');
    $('#erryourpwd').html('');

    var pattern = new RegExp(/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/);
    var newpass = pattern.test(newpwd);
    var newcpass = pattern.test(newcpwd);
    var special_pattern = new RegExp(/^(?=.*?[#?!@$%^&*-]).{8,}/);
    var snewpass = special_pattern.test(newpwd);
    var snewcpass = special_pattern.test(newcpwd);

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
        if(username == newpwd){
            $('#errnewpwd').html('Password cannot be the same as Username');
            return false;
        }
        if(snewpass == true){
            $('#errnewpwd').html('Password not contain any special characters (!,@,#,etc..)');
            return false;
        }
        if(newpass == false){
            $('#errnewpwd').html('Password must contain at least 1 capital letter, 1 samll letter and 1 number');
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
        if(username == newcpwd){
            $('#errnewcpwd').html('Password cannot be the same as Username');
            return false;
        }
        if(snewcpass == true){
            $('#errnewcpwd').html('Password not contain any special characters (!,@,#,etc..)');
            return false;
        }
        if(newcpass == false){
            $('#errnewcpwd').html('Password must contain at least 1 capital letter, 1 samll letter and 1 number');
            return false;
        }
    }
});
</script>
</body>
</html>
