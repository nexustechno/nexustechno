@extends('layouts.app')
@section('content')
<section>
<?php
$loginuser = Auth::user();  
?>
    @if($errors->any())
    <h4>{{$errors->first()}}</h4>
    @endif
    <div class="container">
        <div class="findmember-section">
            <div class="search-wrap">
                <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30" /></svg>
                <div>
                    <input class="search-input" type="text" name="userId" id="userId" placeholder="Find member...">
                    <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                </div>
            </div>
            <div class="player-right">
                <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddAgent">
                    <?php  $url=$_SERVER['REQUEST_URI']; ?>
                    <img src="{{ URL::to('asset/img/user-add.png')}}">Add Agent
                </a>
                <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddPlayer">
                    <img src="{{ URL::to('asset/img/user-add.png')}}">Add Player
                </a>
                <a class="refreshbtn grey-gradient-bg">
                    <img src="{{ URL::to('asset/img/refresh.png')}}">
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
			    <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
			</div>
			<div class="modal-body">
				<div class="form-modal addform-modal">
					<div class="addform-block">
						<div>
							<span>Username</span>
							<span>
								<input type="text" id="puser_name" name="puser_name" placeholder="Enter" maxlength="16" class="form-control white-bg" >
                                <em class="text-color-red">*</em>
                            </span>
                        </div>
						<span class="text-danger cls-error pusrnamecls" id="errplyrusername"></span>
                        <div>
							<span>Password</span>
                            <span><input id="ppassword" name="ppassword" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                        </div>
                         <span class="text-danger cls-error" id="errplyrpass"></span>
                        <div>
                            <span>Confirm Password</span>
                            <span><input id="pcpassword" name="pcpassword" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                        </div>
						 <span class="text-danger cls-error" id="errplyrcpass"></span>							
                    </div>
					<div class="addform-block">
						<div>
							<span>First Name</span>
							<span>
								<input type="text" id="pfname" name="pfname" placeholder="Enter" maxlength="16" class="form-control white-bg">
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
                            <span>Commission(%)</span>
                            <span><input id="pcommission" name="pcommission" type="text" value="2" readonly placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                        </div>
                        <span class="text-danger cls-error" id="errplyrerrcm"></span>
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
                        <input type="submit" value="Crate" name="addplayer" id="addplayer"  class="submit-btn text-color-yellow">
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
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
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
                                    <option value="SMDL">SUB ADMIN (SMDL)</option>
                                    <option value="MDL">SUPER MASTER (MDL)</option>
                                    <option value="DL">MASTER (DL)</option>
                                    @else
                                    <option value="AD">ADMIN (AD)</option>
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
                        <span class="text-danger cls-error" id="errcnpass"></span>
                    </div>
                    <div class="addform-block">
                        <div>
                            <span>First Name</span>
                            <span>
                                <input type="text" id="first_name" name="first_name" placeholder="Enter" maxlength="16" class="form-control white-bg">
                                <em class="text-color-red">*</em>
                            </span>
                        </div>
                        <span class="text-danger cls-error" id="errfn"></span>
                        <div>
                            <span>Last Name</span>
                            <span><input id="last_name" name="last_name" placeholder="Enter" type="text" class="form-control white-bg"></span>
                        </div>
                        <span class="text-danger cls-error" id="errln"></span>
                        <div>
                            <span>Commission(%)</span>
                            <span><input id="commission" type="text" name="commission" value="2" readonly placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                        </div>
                        <span class="text-danger cls-error" id="errcm"></span>
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
                        <input type="submit" value="Crate" name="" id="agentSubmit" class="submit-btn text-color-yellow">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<section>
    <div class="container">
        <div class="remaining-wrap white-bg text-color-blue-1">
            <div class="block-remain">
                <span class="text-color-lght-grey">Remaining Balance</span>
                <h4>PTH 11128.07</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Total Agent Balance</span>
                <h4>PTH 0.00</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Total Client Balance</span>
                <h4>PTH 1844.87</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Exposure</span>
                <h4>PTH <div class="text-color-red">(0.00)</div>
                </h4>
            </div>

            <div class="block-remain">
                <span class="text-color-lght-grey">Available Balance</span>
                <h4>PTH 12972.94</h4>
            </div>

            <div class="block-remain">

                <span class="text-color-lght-grey">Ledger Exposure</span>

                <h4>PTH <div class="text-color-green">2715.20</div>

                </h4>



            </div>







        </div>



    </div>



</section>







<section>



    <div class="container">



        <table class="table custom-table white-bg text-color-blue-2">



            <tbody>







                <tr>



                    <th class="light-grey-bg">Account(Agent)</th>



                    <th class="light-grey-bg">User Name</th>



                    <th class="light-grey-bg">Commission</th>



                    <th class="light-grey-bg">Time Zone</th>



                    <th class="light-grey-bg">Action</th>



                </tr>







                @foreach($agent as $agentData)



                



                <?php



                if($agentData->agent_level == 'SA'){



                    $color = 'orange-bg';



                }else if($agentData->agent_level == 'AD'){



                    $color = 'pink-bg';



                }else if($agentData->agent_level == 'SMDL'){



                    $color = 'green-bg';



                }else if($agentData->agent_level == 'MDL'){



                    $color = 'yellow-bg';



                }else if($agentData->agent_level == 'DL'){



                    $color = 'blue-bg';



                }else{



                    $color = 'red-bg';



                }



                 ?>



                



               



                <tr>



                    <td class="align-L white-bg"><a class="ico_account text-color-blue-light"><span class="{{$color}} text-color-white">{{$agentData->agent_level}}</span>{{$agentData->first_name}} </a></td>                    



                    <td class="white-bg">{{$agentData->user_name}}</td>



                    <td class="text-color-red white-bg" style="display: table-cell;">{{$agentData->commission}}%</td>



                    <td class="text-color-green white-bg">{{$agentData->time_zone}}</td>



                    <td class="white-bg">



                        <ul class="action-ul">



                            <li><a class="grey-gradient-bg" data-toggle="modal" data-target="#myStatus"><img src="{{ URL::to('asset/img/setting-icon.png')}}"></a></li>



                            <li><a class="grey-gradient-bg" href="{{route('changePass',$agentData->id)}}"><img src="{{ URL::to('asset/img/user-icon.png')}}"></a></li>



                            <li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/updown-arrow-icon.png')}}"></a></li>



                            <li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/history-icon.png')}}"></a></li>



                        </ul>



                    </td>



                </tr>



                @endforeach







            </tbody>



        </table>



    </div>



</section>







<div class="modal credit-modal" id="myModal">



    <div class="modal-dialog">



        <div class="modal-content light-grey-bg-1">







            <div class="modal-header">



                <h4 class="modal-title text-color-blue-1">Credit Reference Edit</h4>



                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>



            </div>







            <div class="modal-body">



                <div class="form-modal">



                    <div>



                        <span>Current</span>



                        <span><strong>0</strong></span>



                    </div>



                    <div>



                        <span>New</span>



                        <span><input type="text" id="" placeholder="Enter" class="form-control white-bg"></span>



                    </div>



                    <div>



                        <span>Password</span>



                        <span><input id="" type="password" placeholder="Enter" class="form-control white-bg"></span>



                    </div>



                </div>



                <div class="button-wrap">



                    <input type="submit" value="Submit" name="" id="" class="submit-btn text-color-yellow">



                </div>



            </div>







        </div>



    </div>



</div>







<div class="modal credit-modal" id="myStatus">



    <div class="modal-dialog">



        <div class="modal-content light-grey-bg-1">







            <div class="modal-header">



                <h4 class="modal-title text-color-blue-1">Change Status</h4>



                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>



            </div>







            <div class="modal-body">



                <div class="status-block">







                    <div class="status_id white-bg">



                        <p><span class="highlight-1 purple-bg text-color-white">CLI</span>Rajucli</p>



                        <p class="status-active text-color-green"><span class="round-circle green-bg"></span>Active</p>



                    </div>







                    <div class="status-button white-bg">



                        <ul>



                            <li>



                                <a class="but_active disable white-bg text-color-grey-1"><img class="" src="/asset/img/active-icon.png">Active</a>



                            </li>



                            <li>



                                <a class="but_suspend text-color-red"><img class="" src="asset/img/disable-icon.png"><img class="white-icon" src="/asset/img/disable-white-icon.png">Suspend</a>



                            </li>



                            <li>



                                <a class="but_locked text-color-1"><img class="" src="/asset/img/lock-icon.png"> <img class="white-icon" src="asset/img/lock-white-icon.png">Locked</a>



                            </li>



                        </ul>



                    </div>







                    <div class="buttton-change">



                        <dl class="form_list">



                            <span>Password</span>



                            <input id="" type="password" placeholder="Enter" class="form-control white-bg">



                        </dl>



                        <a class="submit-btn text-color-yellow">Change</a>



                    </div>







                </div>



            </div>







        </div>



    </div>



</div>







<section>



    <div class="container">



        <div class="pagination-wrap light-grey-bg-1">



            <ul class="pages">



                <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>



                <li id="pageNumber"><a class="active text-color-yellow">1</a></li>



                <li id="next"><a class="">Next</a></li>



                <input type="number" id="goToPageNumber_1" maxlength="6" size="4" class="pageinput white-bg"><a id="goPageBtn_1">GO</a>



            </ul>



        </div>



    </div>



</section>







<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>







<script type="text/javascript">



     $(".user_name").keyup(function(){



        $('#errsub').html('');



        var uvalue =  this.value;

        //alert(uvalue);





        $.ajax({



               type:'get',



               url:"{{url('getusername')}}",



               data:{uvalue:uvalue},



               



               success:function(data) {



                $('#errsub').html('');



                if(data.result != ''){



                    $(".userNm").addClass("text-danger");



                    $('#errsub').html('Username is not available');



                }else if(uvalue == ''){



                    $('#errsub').html('This Field is required');



                }else{



                    $(".userNm").removeClass("text-danger");



                    $(".userNm").css("color","green");



					$('#errsub').html('Username is available');



                }







               }



            });







    });







    $('#agentSubmit').click(function () {



        var errsub = $('#errsub').text();



        var agent_level = $('#agent_level').val();



        var user_name = $('#user_name').val();



        var password = $('#password').val();



        var confirm_password = $('#confirm_password').val();



        var first_name = $('#first_name').val();



        var last_name = $('#last_name').val();



        var commission = $('#commission').val();



        var time_zone = $('#time_zone').val();







        $('#errage').html('');



        $('#errsub').html('');



        $('#errpass').html('');



        $('#errcnpass').html('');



        $('#errfn').html('');



        $('#errln').html('');



        $('#errcm').html('');



        $('#errtim').html('');







        if(errsub == 'Username is not available'){



            $('#errsub').html('Username is not available');



              return false;



        }



        if(agent_level == ''){



            $('#errage').html('This Field is required');



              return false;



        }



        if(user_name == ''){



            $('#errsub').html('This Field is required');



              return false;



        }



        if(password == ''){



            $('#errpass').html('This Field is required');



              return false;



        }



        if(password !=''){



            if(password.length < 4){    



                $('#errpass').html('Password must be atleast 4 char long!');



                return false;



            }







        }



        if(confirm_password !=''){          



            if(password != confirm_password){



                $('#errcnpass').html('Confirm password must match with password');



                return false;



            }







        }



        if(confirm_password == ''){



            $('#errcnpass').html('This Field is required');



              return false;



        }



        if(first_name == ''){



            $('#errfn').html('This Field is required');



              return false;



        }



        if(last_name == ''){



            $('#errln').html('This Field is required');



              return false;



        }



        if(commission == ''){



            $('#errcm').html('This Field is required');



              return false;



        }



        if(time_zone == ''){



            $('#errtim').html('This Field is required');



              return false;



        }

    });

</script>

<!--
<script>
$('#puser_name').keypress(function() {
  var value = document.getElementById('puser_name').value;
  alert("value :"+ value);
});
</script>
-->
<script type="text/javascript">

//$(document).ready(function () {
    //$(document).on('keyup','#puser_name',function(){
    $('#puser_name').keyup(function () {
        //console.log('keyed');
		$('#errplyrusername').html('');

		var uvalue =  this.value;
        //alert(uvalue);
        $.ajax({

               type:'get',

               url:"{{url('getusername')}}",

               data:{uvalue:uvalue},

               success:function(data) {

                $('#errplyrusername').html('');

                if(data.result != ''){

                    $(".pusrnamecls").addClass("text-danger");

                    $('#errplyrusername').html('Username is not available');

                }else if(uvalue == ''){

                    $('#errplyrusername').html('This Field is required');

                }else{

                    $(".pusrnamecls").removeClass("text-danger");

                    $(".pusrnamecls").css("color","green");

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

        if(errsub == 'Username is not available'){

            $('#errsub').html('Username is not available');

              return false;

        }

        if(user_name == ''){

            $('#errsub').html('This Field is required');

              return false;

        }

        if(password == ''){

            $('#errplyrpass').html('This Field is required');

              return false;

        }

        if(password !=''){

            if(password.length < 4){    

                $('#errplyrpass').html('Password must be atleast 4 char long!');

                return false;

            }

        }

        if(confirm_password !=''){          

            if(password != confirm_password){

                $('#errplyrpass').html('Confirm password must match with password');

                return false;

            }

        }

        if(confirm_password == ''){

            $('#errplyrcpass').html('This Field is required');

              return false;

        }

        if(first_name == ''){

            $('#errplyrfname').html('This Field is required');

              return false;

        }

        if(last_name == ''){

            $('#errplyrlname').html('This Field is required');

              return false;

        }

        if(commission == ''){

            $('#errplyrerrcm').html('This Field is required');

              return false;

        }

        if(time_zone == ''){

            $('#errplyrtime').html('This Field is required');

              return false;

        }

    });
//});

/*$(document).on('keyup', '#puser_name', function() {
    alert(hi);
});*/
</script>



@endsection