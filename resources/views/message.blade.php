@extends('layouts.app')

@section('content')
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
                        	<form method="post" action="{{route('storeMessage')}}" id="agentform" autcomplete="off">
                            	@csrf
                                
                                <div class="row mt-20 profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Message </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Agent Message </div>
                                                <div class="headdetail"> <input type="text" name="agent_msg" id="agent_msg" placeholder="" value="{{$setting->agent_msg}}"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">User Message</div>
                                                <div class="headdetail"><input type="text" name="user_msg" id="user_msg" placeholder="" value="{{$setting->user_msg}}"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Maintanence </div>
                                                <div class="headdetail"><?php
                                                $checked = '';
                                                if($setting->maintanence_msg != ''){
                                                    $checked = 'checked';
                                                }
                                                 ?>
                                                
                                                <input type="checkbox" name="main_check" id="main_check"  placeholder="" {{$checked}}> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Maintanence Message</div>
                                                <div class="headdetail">  <input type="text" name="maintanence_msg" id="maintanence_msg" placeholder="" value="{{$setting->maintanence_msg}}"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Master Password</div>
                                                <div class="headdetail"><input type="password" name="master_password" id="master_password" placeholder="" required></div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel"><input type="submit" class="submit-btn text-color-yellow" value="Submit" style="width:200px"> </div>
                                                <div class="headdetail"> </div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                
                                <!--<div class="timeblock-box">
                                    
                                    <div class="datebox">
                                        <span>Agent Message</span>
                                        <div class="datediv1">
                                            <div class="datediv">
                                                <input type="text" name="agent_msg" id="agent_msg" class="form-control " placeholder="" value="{{$setting->agent_msg}}">
                                            </div>                                         
                                            
                                        </div>
                                    </div>
                    
                                    <div class="datebox">
                                        <span>User Message</span>
                                        <div class="datediv1">
                                            <div class="datediv">
                                                <input type="text" name="user_msg" id="user_msg" class="form-control " placeholder="" value="{{$setting->user_msg}}">
                                            </div>                                         
                                            
                                        </div>
                                    </div>
                    
                                    <div class="datebox">
                                        <span>Maintanence</span>
                                        <div class="datediv1">
                                            <div class="datediv">
                                                <?php
                                                $checked = '';
                                                if($setting->maintanence_msg != ''){
                                                    $checked = 'checked';
                                                }
                                                 ?>
                                                
                                                <input type="checkbox" name="main_check" id="main_check" class="form-control " placeholder="" {{$checked}}>
                                            </div>                                         
                                            
                                        </div>
                                    </div>
                    
                                    <div class="datebox main_msg" style="display: none">
                                        <span>Maintanence Message</span>
                                        <div class="datediv1">
                                                <input type="text" name="maintanence_msg" id="maintanence_msg" class="form-control " placeholder="" value="{{$setting->maintanence_msg}}">
                                            
                                        </div>
                                    </div>
                    
                                    <div class="datebox">
                                        <span>Master Password</span>
                                        <div class="datediv1">
                                                <input type="password" name="master_password" id="master_password" class="form-control " placeholder="" required>
                                            
                                        </div>
                                    </div>
                    
                                    <div class="datebox">
                                        <div class="datediv1">
                                                <input type="submit" class="form-control " value="Submit">
                                            
                                        </div>
                                    </div>
                    
                                </div>-->
                    
                    </form>
                            </div>
                            
                            
                            
                   	</div>
              	</div>
          	</div>
       </div>

     
    </div>

</section>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
    $('input[type="checkbox"]').click(function() {
      if($(this).prop("checked") == true) {
        $('.main_msg').css("display", "block");
      }
      else if($(this).prop("checked") == false) {
        $('.main_msg').css("display", "none");
      }
    });

    $(document).ready(function(){
        if($('#main_check').is(':checked')){ // check if checkbox checked
       $('.main_msg').css("display", "block");
    }
    });
</script>
@endsection