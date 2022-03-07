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
                <form method="post" action="{{route('addSport')}}" id="agentform" autcomplete="off">
                  @csrf
                  <div class="row mt-20 match_form profile-wrap">
                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                      <div class="grey-bg head text-color-white"> Add Sports </div>
                      <div class="profile-detail white-bg">
                        <div class="profile-main">
                          <div class="headlabel">Sport Name </div>
                          <div class="headdetail"> <input type="text" name="sport_name" id="sport_name" placeholder="" value="" class="form-control">
                          </div>
                          <span class="text-danger cls-error" id="errsport_name"></span>
                        </div>
                        <div class="profile-main">
                          <div class="headlabel">Status</div>
                          <div class="headdetail">
                             <select name="status" class="form-control">
                              <option value="active">Active</option>
                              <option value="inactive">Inactive</option>
                            </select>
                          </div>
                          <span class="text-danger cls-error" id="errstatus"></span>
                        </div>         
                        <div class="profile-main">
                          <div class="headlabel"><input id="btnsport" type="submit" class="submit-btn text-color-yellow" value="Submit" style="width:200px"> </div>
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
$('#btnsport').click(function () {        
  var sport_name = $('#sport_name').val();
  var status = $('#status').val();      
  $('#errsport_name').html('');
  $('#errstatus').html('');                
  if(sport_name == ''){
    $('#errsport_name').html('This Field is required');
    return false;
  }
  if(status == ''){
    $('#errstatus').html('This Field is required');
    return false;
  }
});
</script>
@endsection