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
						<div class="timeblock light-grey-bg-2">
                        	<form method="post" action="{{route('storeMessage')}}" id="agentform" autcomplete="off">
                            	@csrf
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Message </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Agent Message </div>
                                                <div class="headdetail"> <input type="text" name="agent_msg" id="agent_msg" placeholder="" value="{{$setting->agent_msg}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">User Message</div>
                                                <div class="headdetail"><input type="text" name="user_msg" id="user_msg" placeholder="" value="{{$setting->user_msg}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Maintanence </div>
                                                <div class="headdetail"><?php
                                                $checked = '';
                                                if($setting->maintanence_msg != ''){
                                                    $checked = 'checked';
                                                }
                                                ?>
                                                <input type="checkbox" name="main_check" id="main_check"  placeholder=""  onclick="msgshow();" {{$checked}}> </div>
                                            </div>
                                            <div class="profile-main" id="mntmsg" @if($checked) style="display:block" @else style="display:none" @endif>
                                                <div class="headlabel">Maintanence Message</div>
                                                <div class="headdetail">  <input type="text" name="maintanence_msg" id="maintanence_msg" placeholder="" value="{{$setting->maintanence_msg}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Master Password</div>
                                                <div class="headdetail"><input type="password" name="master_password" id="master_password" placeholder="" class="form-control" required></div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel"><input type="submit" class="submit-btn text-color-yellow" value="Submit" style="width:200px"> </div>
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


<script type="text/javascript">
    function msgshow(){
        var checkBox = document.getElementById("main_check");
        var text = document.getElementById("mntmsg");
        if (checkBox.checked == true){
            mntmsg.style.display = "block";
        } else {
            mntmsg.style.display = "none";
        }
    }
</script>

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
        if($('#main_check').is(':checked')){ 
           $('.main_msg').css("display", "block");
        }
    });
</script>
@endsection