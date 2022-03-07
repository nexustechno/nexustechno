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
                        	<form method="post" action="{{route('updateWebsettingData')}}" id="agentform" autcomplete="off" enctype="multipart/form-data">
                            	@csrf
                                <input type="hidden" name="id" value="{{$list->id}}">
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Edit Web Setting </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Site Title </div>
                                                <div class="headdetail"> <input type="text" name="title" id="title" placeholder="" value="{{$list->title}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Domain</div>
                                                <div class="headdetail"><input type="text" name="domain" id="domain" placeholder="" value="{{$list->domain}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Logo</div>
                                                <div class="headdetail">
                                                    <input type="file" name="logo" id="logo" placeholder="" class="form-control"> 
                                                    <img src="{{ URL::to('asset/front/img/')}}/{{$list->logo}}" width="100px;" height="100px;">
                                                </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Favicon</div>
                                                <div class="headdetail">
                                                    <input type="file" name="favicon" id="favicon" placeholder="" class="form-control"> 
                                                    <img src="{{ URL::to('asset/front/img/')}}/{{$list->favicon}}" width="100px;" height="100px;">
                                                </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Login Image</div>
                                                <div class="headdetail">
                                                    <input type="file" name="login_image" id="login_image" placeholder="" class="form-control"> 
                                                    <img src="{{ URL::to('asset/front/img/')}}/{{$list->login_image}}" width="100px;" height="100px;">
                                                </div>
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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
@endsection