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
                            <form method="post" action="{{route('dashboard.images.store')}}" id="agentform" autcomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Dashboard Image </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Title </div>
                                                <div class="headdetail"> <input type="text" name="title" id="title" placeholder="" value="@if(isset($image) && !empty($image)){{$image->title}}@endif" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Image Size </div>
                                                <div class="headdetail">
                                                    <select name="width_type" class="form-control">
                                                        <option @if(isset($image) && !empty($image) && $image->width_type == 'column12'){{"selected"}}@endif value="column12">Full Width</option>
                                                        <option @if(isset($image) && !empty($image) && $image->width_type == 'column6'){{"selected"}}@endif value="column6">Half Width</option>
                                                        <option @if(isset($image) && !empty($image) && $image->width_type == 'column3'){{"selected"}}@endif value="column3">Square Width</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Image</div>
                                                <div class="headdetail">
                                                    <input type="file" name="file_name" id="file_name" placeholder="" value="" class="form-control">
                                                    @if(isset($image) && !empty($image->file_name))
                                                        <br/>
                                                        <img style="height: 100px;" src="{{asset('asset/upload')."/".$image->file_name}}" alt="">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Link</div>
                                                <div class="headdetail">  <input type="text" name="link" id="link" placeholder="" value="@if(isset($image) && !empty($image)){{$image->link}}@else{{"#"}}@endif" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel"></div>
                                                <div class="headlabel">
                                                    @if(isset($image) && !empty($image))
                                                        <input type="hidden" name="id" value="{{ $image->id }}">
                                                        <input type="submit" class="btn submit-btn text-color-yellow" value="Update" style="width:200px">
                                                    @else
                                                        <input type="submit" class="btn submit-btn text-color-yellow" value="Submit" style="width:200px">
                                                    @endif
                                                    <a class="btn btn-info" href="{{route('dashboard.images')}}">Cancel</a>
                                                </div>
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
@endsection
