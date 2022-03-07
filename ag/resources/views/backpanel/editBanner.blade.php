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
                        	<form method="post" action="{{route('updatebanner',$banner->id)}}" id="agentform" autcomplete="off" enctype="multipart/form-data">
                            	@csrf
                                <input type="hidden" name="id" value="{{$banner->id}}">
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Edit Banner </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Banner Name </div>
                                                <div class="headdetail"> <input type="text" name="banner_name" id="banner_name" placeholder="" value="{{$banner->banner_name}}" class="form-control"> </div>
                                            </div>                                          
                                            <div class="profile-main">
                                                <div class="headlabel">Image</div>
                                                <div class="headdetail">
                                                    <input type="file" name="banner_image" id="banner_image" placeholder="" class="form-control"> </br>
                                                    <img src="{{ URL::to('asset/upload/')}}/{{$banner->banner_image}}" width="100px;" height="100px;">
                                                    <input type="hidden" name="old_bannerImage" value="{{$banner->banner_image}}">
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