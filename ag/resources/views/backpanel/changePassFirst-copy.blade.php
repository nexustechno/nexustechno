<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="img/fevicon.ico" type="image/x-icon">
    <title>BETEXCHANGE - Agent</title>

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
<body class="white-bg text-color-black1 chreme-bg">
    <div class="page-wrapper">
        <header class="main-header">
            <div class="top_header">
                <div class="container">
                    <div class="row">
                        <div class="logo">
                            <a href="index.php"><img src="{{ URL::to('asset/img/logo2.png')}}"></a>
                        </div>
                        <ul class="account-wrap">
                            <li class="text-color-yellow1">
                                <span class="black-bg text-color-white">{{$loguser->agent_level}}</span>
                                <strong>{{$loguser->user_name}}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
    <section class="profit-section section-mlr">
        <div class="container">
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
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

                            <div class="timeblock light-grey-bg-2">
                                <form method="post" action="{{route('updatePassword',$id)}}" id="agentform" autcomplete="off">
                                    @csrf
                                    <div class="row mt-20 profile-wrap">
                                        <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                            <div class="grey-bg head text-color-white"> Change Password </div>
                                            <div class="profile-detail white-bg">
                                                <div class="profile-main">
                                                    <div class="headlabel">Your Password</div>
                                                    <div class="headdetail"><input id="yourpwd" name="yourpwd" type="password" placeholder="Enter" class="form-control white-bg"> <label class="text-color-red"> * </label>
                                                        <span class="text-danger cls-error" id="erryourpwd"></span>
                                                    </div>
                                                </div>
                                                <div class="profile-main">
                                                    <div class="headlabel">New Password</div>
                                                    <div class="headdetail"><input type="password" id="newpwd" name="newpwd" placeholder="Enter" class="form-control white-bg"> <label class="text-color-red"> * </label>
                                                        <span class="text-danger cls-error" id="errnewpwd"></span>
                                                    </div>
                                                </div>                                           
                                                <div class="profile-main">
                                                    <div class="headlabel">New Password Confirm</div>
                                                    <div class="headdetail"> <input type="password" id="newcpwd" name="newcpwd" placeholder="Enter" class="form-control white-bg"> <label class="text-color-red"> * </label> 
                                                    <span class="text-danger cls-error" id="errnewcpwd"></span>
                                                    </div>
                                                </div>
                                                <div class="profile-main">
                                                    <div class="headlabel"><input name="btnpwd" id="btnpwd" type="submit" class="submit-btn text-color-yellow" value="Change" style="width:200px"> </div>
                                                    <div class="headdetail"> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('footer')
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
        if(newpwd.length < 4){  
            $('#errnewpwd').html('Password must be atleast 4 char long!');
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
</body>
</html>