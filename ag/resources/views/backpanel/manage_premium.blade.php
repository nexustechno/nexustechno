@extends('layouts.app')
@section('content')
<section>
    <div class="container">
        <div class="inner-title">
            <h2>Manage Premium</h2>
        </div>
        <div class="fancy-history-block">
            <div class="in_play_tabs-2 mb-0">
                <ul class="nav nav-tabs" role="tablist">
                	@foreach($sports as $sport)
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg  @if($sport->sId==4) active @endif " href="#{{$sport->sId}}" data-toggle="tab">{{$sport->sport_name}}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                	 @foreach($sports as $sport)
                        <div role="tabpanel" class="tab-pane @if($sport->sId==4) active show @endif" id="{{$sport->sId}}">
                            <table class="table custom-table white-bg text-color-blue-2">
                                <thead>
                                    <tr>
                                        <th class="light-grey-bg">Match Name</th>
                                        <th class="light-grey-bg text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($type == 'history'){
                                            $match = DB::table('my_bets as b')
                                                  ->join('match as m', 'b.match_id', '=', 'm.event_id')
                                                  ->where('b.team_name','!=','')
                                                  ->where('b.result_declare','=',1)
                                                  ->where('b.bet_type','PREMIUM')
                                                  ->where('m.sports_id',$sport->sId)
                                                  ->groupBy('b.match_id')
                                                  ->orderBy('b.result_declare','ASC')
                                                  ->distinct()
                                                  ->get();
                                        }else{
                                            $match = DB::table('my_bets as b')
                                                ->join('match as m', 'b.match_id', '=', 'm.event_id')
                                                ->where('b.team_name','!=','')
                                                ->where('b.result_declare','=',0)
                                                ->where('b.bet_type','PREMIUM')
                                                ->where('m.sports_id',$sport->sId)
                                                ->groupBy('b.match_id')
                                                ->orderBy('b.result_declare','ASC')
                                                ->distinct()
                                                ->get();
                                        }
                                    ?>
                                    @foreach($match as $matches)
                                        <tr class="white-bg">
                                            <?php
                                            $matchTime = $matches->match_date;
                                            $printDate = date('d/m/Y', strtotime($matchTime));
                                            ?>
                                            <td> <a> {{$matches->match_name}}  {{$printDate}} </a> </td>
                                            <td class="text-left">
                                                @if($type == 'history')
                                                    <a href="{{route('manage.premium.history.detail',$matches->id)}}" class="text-color-blue-light">MANAGE HISTORY</a>
                                                @else
                                                    <a href="{{route('manage.premium.detail',$matches->id)}}" class="text-color-blue-light">MANAGE PREMIUM</a>
                                                @endif
                                            </td>
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
