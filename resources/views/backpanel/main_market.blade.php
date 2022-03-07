@extends('layouts.app')
@section('content')

<section>
    <div class="container">
        <div class="inner-title player-right justify-content-between py-2">
            <h2>Manual Match Add</h2>
            <div class="btn-wrapadd">
                <a href="{{route('sports')}}" class="add_player grey-gradient-bg text-color-black">Add Sport</a>
            </div>
        </div>
        <div class="list-games-block">
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="light-grey-bg">Sr.No.</th>
                        <th class="light-grey-bg">Sport Name</th>
                        <th class="light-grey-bg">Status</th>
                        <th class="light-grey-bg" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                	<?php $count = 1; ?>
                	@foreach($sports as $sport)
                    <tr>
                        <td class="white-bg">{{$count}}</td>
                        <td class="white-bg">{{$sport->sport_name}}</td>
                        <td class="text-color-green white-bg">{{strtoupper($sport->status)}}</td>
                        <td class="white-bg">
                            <a href="{{route('match',$sport->id)}}" class="btn-list black-bg2 text-color-white">ADD MATCH</a>                           
                        </td>
                    </tr>
                    <?php $count++; ?>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection