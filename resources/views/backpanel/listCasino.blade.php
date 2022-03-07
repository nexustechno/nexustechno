@extends('layouts.app')
@section('content')

<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('backpanel/leftpanelcasino')
            <div class="middle-section casino_result_section">
             	<div class="middle-wraper">
                	<div class="our_casino_list">
                        @foreach($casino as $casinos)
                        <?php
                           $urlBack=$casinos->casino_name."back";                         
                        ?>
                        <div class="casino_list_items">
                            <a href="{{route($urlBack,$casinos->id)}}">
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