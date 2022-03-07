@extends('layouts.app')
@section('content')
<?php
use App\User;
?>
<section>
    <div class="container">
        <div class="inner-title player-right justify-content-between py-2">
            <h2>Match History</h2>
        </div>
        <div class="list-games-block match_history_table">
            <table id="example2" class="display nowrap" style="width:100%">
                <thead>
                    <tr class="light-grey-bg">
                        <th>Sr.No.</th>
                        <th>User Name</th>
                        <th>Bet Type</th>
                        <th>Bet Side</th>
                        <th>Team Name</th>
                        <th>Bet Odds</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count=1; ?>
                        @foreach($betdata as $match)
                        <?php
                          $uname = User::where('id',$match->user_id)->first(); 
                        ?>
                        
                    <tr class="white-bg">
                        <td>{{$count}}</td>
                        <td>{{$uname->user_name}}</td>
                        <td>{{$match->bet_type}}</td>
                        <td style="text-transform: uppercase;">{{$match->bet_side}}</td>
                        <td>{{$match->team_name}}</td>
                        <td>{{$match->bet_odds}}</td>
                        <td style="font-weight: bold;">{{$match->bet_amount}}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
