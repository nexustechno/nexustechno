@extends('layouts.app')

@section('content')

@if($errors->any())
<h4>{{$errors->first()}}</h4>
@endif

<section class="myaccount-section">
    <div class="container">
        <div class="row">

            <div class="col-md-12 pl-0">
                <div class="downline-block">

                    <div class="search-wrap">
                        <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30"></path>
                        </svg>
                        <div>
                            <input class="search-input navy-light-bg" type="text" name="userId" id="userId" placeholder="Find member...">
                            <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                        </div>
                    </div>

                    <ul class="agentlist">
                        <li class="firstli"><a><span class="blue-bg text-color-white">MA</span><strong>Raju</strong></a> <img src="/asset/img/arrow-right2.png"> </li>
                        <li class="lastli"><a><span class="orange-bg text-color-white">PL</span><strong>Rajucli</strong></a></li>                        
                    </ul>

                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
               @include('downline-account-menu')
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12">

                <div class="pagetitle text-color-blue-2">
                    <h1>Account Summary</h1>
                </div>

                <p class="d-flex algin-item-center mt-2 mb-0"> <img src="/asset/img/user-icon-blue.png" class="mr-1"> Rajucli</p>

                <div class="white-bg mt-2 mb-3 acc-statement">
                    <table class="table custom-table white-bg text-color-blue-2">
                        <tbody>
                            <tr>
                                <th class="light-grey-bg">Wallet</th>
                                <th class="light-grey-bg">Available to Bet</th>
                                <th class="light-grey-bg">Funds available to withdraw</th>
                                <th class="light-grey-bg">Current exposure</th>
                            </tr>
                            <tr>
                                <td> Main wallet </td>
                                <td> 376.46 </td>
                                <td> 376.46 </td>
                                <td> 0.00 </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="pagetitle text-color-blue-2">
                    <h1>Profile </h1>
                </div>

                <div class="row mt-20 profile-wrap">
                    <div class="col-lg-6 col-md-6 col-sm-12 pl-0">
                        <div class="grey-bg head text-color-white"> About You </div>
                        <div class="profile-detail white-bg">
                            <div class="profile-main">
                                <div class="headlabel"> First Name </div>
                                <div class="headdetail"> Raju </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> Last Name </div>
                                <div class="headdetail"> - </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> Birthday </div>
                                <div class="headdetail"> - </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> E-mail </div>
                                <div class="headdetail"> - </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> Password </div>
                                <div class="headdetail"> ******* <span class="text-color-blue-light "> <a data-toggle="modal" data-target="#mypwd"> Edit <i class="fas fa-pencil-alt"></i> </a> </span> </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> Time Zone </div>
                                <div class="headdetail"> IST </div>
                            </div>
                        </div>
                        <br>
                        <div class="grey-bg head text-color-white"> Contact Details </div>
                        <div class="profile-detail white-bg">
                            <div class="profile-main">
                                <div class="headlabel"> Primary number </div>
                                <div class="headdetail"> </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 pr-0">
                        <div class="grey-bg head text-color-white"> Limits & Commission </div>
                        <div class="profile-detail white-bg">
                            <div class="profile-main">
                                <div class="headlabel"> Exposure Limit </div>
                                <div class="headdetail"> 10,000.00 </div>
                            </div>
                            <div class="profile-main">
                                <div class="headlabel"> Commission </div>
                                <div class="headdetail"> 2.0% </div>
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
            <form method="post" action="{{route('updatePassword',$id)}}" id="agentform">
            	@csrf
            <div class="modal-body">
                <div class="form-modal">
                    <div>
                        <span>Your Password</span>
                        <span><input id="yourpwd" name="yourpwd" type="password" placeholder="Enter" class="form-control white-bg">  </span>
                    </div>
                    <span class="text-danger cls-error" id="erryourpwd"></span>
                    <div>
                        <span>New Password</span>
                        <span><input type="password" id="newpwd" name="newpwd" placeholder="Enter" class="form-control white-bg"> </span>

                    </div>
                     <span class="text-danger cls-error" id="errnewpwd"></span>
                    <div>
                        <span>New Password Confirm</span>
                        <span><input type="password" id="newcpwd" name="newcpwd" placeholder="Enter" class="form-control white-bg"> </span>
                    </div>
                    <span class="text-danger cls-error" id="errnewcpwd"></span>
                   
                </div>
                <div class="button-wrap">
                    <input type="submit" value="Change" name="btnpwd" id="btnpwd" class="submit-btn text-color-yellow">
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
@endsection