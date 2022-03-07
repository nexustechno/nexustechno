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
                        	<form method="post" action="{{route('addMatch',$id)}}" id="agentform" autcomplete="off">
                            	@csrf
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Add Match </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Match Name </div>
                                                <div class="headdetail"> <input type="text" name="match_name" id="match_name" placeholder="" value="" class="form-control">
                                                </div>
                                                <span class="text-danger cls-error" id="errmatch_name"></span>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Match Date Time</div>
                                                <div class="headdetail"><input type="datetime-local" name="match_date" id="match_date" placeholder="" value="" class="form-control"> 
                                                </div>
                                                <span class="text-danger cls-error" id="errmatch_date"></span>
                                            </div>         
                                            <div class="profile-main">
                                                <div class="headlabel">Match Id</div>
                                                <div class="headdetail">  <input type="text" name="match_id" id="match_id" placeholder="" value="" class="form-control"> 
                                                </div>
                                                <span class="text-danger cls-error" id="errmatch_id"></span> 
                                            </div>

                                            <div class="profile-main">
                                                <div class="headlabel">Event Id</div>
                                                <div class="headdetail">  <input type="text" name="event_id" id="event_id" placeholder="" value="" class="form-control"> 
                                                </div>
                                                <span class="text-danger cls-error" id="errevent_id"></span> 
                                            </div>                                            
                                            <div class="profile-main">
                                                <div class="headlabel"><input id="btnmatch" type="submit" class="submit-btn text-color-yellow" value="Submit" style="width:200px"> </div>
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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
$('#btnmatch').click(function () {        
    var match_name = $('#match_name').val();
    var match_date = $('#match_date').val();
    var match_id = $('#match_id').val();
    var event_id = $('#event_id').val();
    
    $('#errmatch_name').html('');
    $('#errmatch_date').html('');
    $('#errmatch_id').html('');
    $('#errscoreurl').html('');
    $('#errevent_id').html('');          
    
    if(match_name == ''){
        toastr.error('Matchname can not be blank!');
          return false;
    }
    if(match_date == ''){
        toastr.error('Match date can not be blank!');
          return false;
    }
    
    if(match_id == ''){
        toastr.error('Match id can not be blank!');
          return false;
    }
    if(event_id == ''){
        toastr.error('Event id can not be blank!');
         return false;
    }
});
</script>
@endsection