@extends('layouts.app')

@section('content')

<?php 
use App\User;
$loginuser = Auth::user(); 
$user = User::where('id',$loginuser->id)->first();
?>
<section>
	<div class="container">
    	<div class="breadcrumbs">
        	<ul>
            	<li> <a href="#" class="text-color-black" > <span class="red-bg text-color-white">{{$user->agent_level}}</span> {{$user->user_name}} </a> </li>
            </ul>
    	</div>
    </div>
</section>
<section class="myaccount-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-12 pl-0">
				@include('backpanel.account-menu')
			</div>
			<div class="col-lg-9 col-md-9 col-sm-12">
				<div class="pagetitle text-color-blue-2">
					<h1>Profile </h1>
				</div>
				<div class="row mt-20 profile-wrap">
					<div class="col-lg-6 col-md-6 col-sm-12 pl-0">
						<div class="grey-bg head text-color-white"> About You </div>
						<div class="profile-detail white-bg">
							<div class="profile-main">
								<div class="headlabel"> First Name </div>
								<div class="headdetail"> {{$user->first_name}} </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> Last Name </div>
								<div class="headdetail"> {{$user->last_name}} </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> Birthday </div>
								<div class="headdetail"> - </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> E-mail </div>
								<div class="headdetail"> {{$user->email}} </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> Password </div>
								<div class="headdetail"> ******** <span class="text-color-blue-light "> <a data-toggle="modal" data-target="#mypwd"> Edit <i class="fas fa-pencil-alt"></i>  </a> </span> </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> Time Zone </div>
								<div class="headdetail"> {{$user->time_zone}} </div>
							</div>
							<div class="profile-main">
								<div class="headlabel"> Languages </div>
								<div class="headdetail"> <select id="lang"><option value="en">English</option><option value="cn">中文</option></select> </div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 pr-0">
						<div class="grey-bg head text-color-white"> Contact Details </div>
						<div class="profile-detail white-bg">
							<div class="profile-main">
								<div class="headlabel"> Primary number </div>
								<div class="headdetail"> - </div>
							</div>
						</div>
							
					</div>
				</div>
			</div>
		</div>
    </div>
</section>

<div class="modal credit-modal changepwd-modal" id="mypwd">	
	<div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Change Password</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
			</div>
			<form method="post" action="{{route('updateAccountPassword',$user->id)}}" id="agentform" autcomplete="off">
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
						<button class="submit-btn text-color-yellow" name="btnpwd" id="btnpwd"> Change </button>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

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
@endsection