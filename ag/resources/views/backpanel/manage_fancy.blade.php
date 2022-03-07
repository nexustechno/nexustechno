@extends('layouts.app')
@section('content')
<?php
use App\Match;
use App\MyBets;
use App\FancyResult;
?>
<section>
    <div class="container">
        <div class="inner-title">
            <h2>Manage Fancy</h2>
        </div>
        <div class="fancy-history-block">
            <div class="in_play_tabs-2 mb-0">
                <ul class="nav nav-tabs" role="tablist">
                	<?php $i=0; ?>
                	@foreach($sports as $sport)
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg  @if($i==0) active @endif " href="#{{$sport}}" data-toggle="tab">{{$sport->sport_name}}</a>
                        </li>
                        <?php $i++; ?>
                    @endforeach
                </ul>
                <div class="tab-content">
                	 @foreach($sports as $sport)
                        <div role="tabpanel" class="tab-pane active show" id="{{$sport}}">
                            <table class="table custom-table white-bg text-color-blue-2">
                                <thead>
                                    <tr>
                                        <th class="light-grey-bg">Match Name</th>
                                        <th class="light-grey-bg text-left">Manage Fancy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $match=DB::table('my_bets as b')
                                              ->join('match as m', 'b.match_id', '=', 'm.event_id')
                                              ->where('b.team_name','!=','')
                                              ->where('b.result_declare','=',0)
                                              ->where('b.bet_type','=','SESSION')
                                              ->groupBy('b.match_id')
                                              ->orderBy('b.result_declare','ASC')
                                              ->distinct()
                                              ->get();
                                    ?>
                                    @foreach($match as $matches)
                                        <tr class="white-bg">
                                            <?php
                                            $matchTime = $matches->match_date;
                                            $printDate = date('d/m/Y', strtotime($matchTime));
                                            ?>
                                            <td> <a> {{$matches->match_name}}  {{$printDate}} </a> </td>
                                            <td class="text-left"> <a href="{{route('manageFancyDetail',$matches->id)}}" class="text-color-blue-light">MANAGE FANCY</a> </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
