@extends('layouts.app')
@section('content')

    <section>
        <div class="container-fluid">
            <div class="main-wrapper">
                <div class="container">
                    <div class="middle-section m-0">
                        <div class="middle-wraper">
                            <div class="our_casino_list">
                                @foreach($casino as $casinos)
                                    <div class="casino_list_items">
                                        <a href="{{route('casinoDetail',$casinos->casino_name)}}">
                                            <img src="{{ URL::to('asset/upload') }}/{{$casinos->casino_image}}"
                                                 alt="img">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
