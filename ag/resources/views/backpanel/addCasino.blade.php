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
                            <form method="post" action="{{route('insertCasino')}}" id="agentform" autcomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-20 match_form profile-wrap">
                                    <div class="col-lg-12 col-md-12col-sm-12 pl-0">
                                        <div class="grey-bg head text-color-white"> Casino </div>
                                        <div class="profile-detail white-bg">
                                            <div class="profile-main">
                                                <div class="headlabel">Title </div>
                                                <div class="headdetail"> <input type="text" name="casino_title" id="casino_title" placeholder="" value="@if(isset($casino) && !empty($casino)){{$casino->casino_title}}@endif" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Name </div>
                                                <div class="headdetail"> <input type="text" name="casino_name" id="casino_name" placeholder="" value="@if(isset($casino) && !empty($casino)){{$casino->casino_name}}@endif" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Image</div>
                                                <div class="headdetail"><input type="file" name="casino_image" id="casino_image" placeholder="" value="" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel">Link</div>
                                                <div class="headdetail">  <input type="text" name="casino_link" id="casino_link" placeholder="" value="@if(isset($casino) && !empty($casino)){{$casino->casino_link}}@endif" class="form-control"> </div>
                                            </div>
                                            <div class="profile-main">
                                                <div class="headlabel"></div>
                                                <div class="headlabel">
                                                    @if(isset($casino) && !empty($casino))
                                                        <input type="hidden" name="id" value="{{ $casino->id }}">
                                                        <input type="submit" class="btn submit-btn text-color-yellow" value="Update" style="width:200px">
                                                    @else
                                                        <input type="submit" class="btn submit-btn text-color-yellow" value="Submit" style="width:200px">
                                                    @endif
                                                    <a class="btn btn-info" href="{{route('casinoAll')}}">Cancel</a>
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
