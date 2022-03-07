@extends('layouts.front_layout')
<style>
.disabled-link {
  pointer-events: none;
}
</style>
@section('content')
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('front/leftpanelcasino')
            <div class="middle-section casino_result_section">
                <div class="middle-wraper">
                    <div class="our_casino_list">
                        @foreach($casino as $casinos)
                        <?php
                        $class="disabled-link";
                        if($casinos->status==1){
                            $class="";
                        }
                         ?>
                        
                        <div class="casino_list_items">
                        <a class="{{$class}}" href="{{route($casinos->casino_name,$casinos->id)}}">
                            <img src="{{ URL::to('asset/upload') }}/{{$casinos->casino_image}}" alt="img">
                        </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
@endsection