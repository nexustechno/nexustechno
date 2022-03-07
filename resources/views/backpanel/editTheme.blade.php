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
                        	<form method="post" action="{{route('updatetheme',$theme->id)}}" id="agentform" autcomplete="off" enctype="multipart/form-data">
                            	@csrf
                                <input type="hidden" name="id" value="{{$theme->id}}">
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Edit Theme </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Theme Name </div>
                                                <div class="headdetail"> <input type="text" name="theme_name" id="theme_name" placeholder="" value="{{$theme->theme_name}}" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Main Theme</div>
                                                <div class="headdetail"> <input type="text" name="main_theme" id="main_theme" placeholder="" value="{{$theme->main_theme}}" class="form-control"> </div>
                                            </div>   
                                            <div class="profile-main">
                                                <div class="headlabel">Remark</div>
                                                <div class="headdetail"> <input type="text" name="header_theme" id="header_theme" placeholder="" value="{{$theme->header_theme}}" class="form-control"> </div>
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