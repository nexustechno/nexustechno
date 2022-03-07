@extends('layouts.app')
@section('content')

<!-- banner -->
<section>
    <div class="container">
        <div class="inner-title player-right justify-content-between py-2">
            <div class="row w-100">
                <div class="col-lg-6 col-md-6 col-sm-12 pl-0">
                    <h2></h2>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 pr-0 text-right">
                    <a data-toggle="modal" data-target="#myweb" class="submit-btn text-color-yellow" >
                    Add Image </a>
                </div>
            </div>
        </div>
        <div class="list-games-block match_history_table">
            <table id="example1" class="display nowrap" style="width:100%">
                <thead>
                    <tr class="light-grey-bg">
                        <th style="width:80px;">Sr. No.</th>
                        <th style="width:300px;">Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                     @if(!empty($banner))
                        @php $i=1; @endphp
                       @foreach($banner as $banners)
                            <tr class="white-bg">
                                <td>{{$i}}</td>
                                <td>{{$banners->banner_name}}</td>
                                @if($banners->banner_image != '')
                                    <td><img src="{{ URL::to('asset/upload')}}/{{$banners->banner_image}}" height="100px;" width="100px;"></td>
                                @else
                                    <td>--</td>
                                @endif

                                <td>
                            <a href="{{route('editBanner',$banners->id)}}" class="btn-list black-bg2 text-color-white">Edit</a>
                            <a href="{{route('delBanner',$banners->id)}}" class="btn-list black-bg2 text-color-white">Delete</a>
                                </td>


                            </tr>
                        @php $i++; @endphp
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal credit-modal changepwd-modal" id="myweb">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Add Image</h4>
                <button type="button" class="close" data-dismiss="modal">x</button>
            </div>
            <form method="post" action="{{route('addBanner')}}" id="form" autcomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-modal">
                        <div>
                            <span>Banner Name</span>
                            <span><input id="banner_name" name="banner_name" type="text" placeholder="Banner Name" class="form-control white-bg"> <label class="text-color-red" required> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errtitle"></span>

                        <div>
                            <span>Image</span>
                            <span><input type="file" id="banner_image" name="banner_image" class="form-control white-bg" required> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errnewpwd"></span>
                    </div>
                    <div class="button-wrap">

                        <button class="submit-btn text-color-yellow" name="btnpwd" id="btnpwd" value="save"> Submit </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal credit-modal changepwd-modal" id="mywebedit">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Add New Website</h4>
                <button type="button" class="close" data-dismiss="modal">x</button>
            </div>
            <form method="post" action="{{route('addWebsite')}}" id="form" autcomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-modal">
                        <div>
                            <span>Site Title</span>
                            <span><input id="title" name="title" type="text" placeholder="Site Title" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errtitle"></span>

                        <div>
                            <span>Domain</span>
                            <span><input type="text" id="domain" name="domain" placeholder="Domain" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                         <div>
                            <span>Favicon Icon</span>
                            <span><input type="file" id="favicon" name="favicon" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <div>
                            <span>Logo</span>
                            <span><input type="file" id="logo" name="logo" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <div>
                            <span>Login Image</span>
                            <span><input type="file" id="login_image" name="login_image" class="form-control white-bg"> <label class="text-color-red"> * </label> </span>
                        </div>
                        <span class="text-danger cls-error" id="errnewpwd"></span>

                    </div>
                    <div class="button-wrap">

                        <button class="submit-btn text-color-yellow" name="btnpwd" id="btnpwd" value="save"> Save </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
