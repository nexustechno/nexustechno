@extends('layouts.app')
@section('content')
<?php
use App\User;
?>
<section>
    <div class="container">
        <div class="inner-title player-right justify-content-between py-2">
            <h2>Fancy History</h2>
        </div>
        <div class="list-games-block match_history_table">
            <table id="example2" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                      <th>Sr.No.</th>
                      <th>User Name</th>
                      <th>Bet Side</th>
                      <th>Fancy Name</th>
                      <th>Bet Odds</th>
                      <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="appendBF">
                  <?php $count=1; ?>
                  @foreach($betdata as $fancyResults)
                    <?php
                      $uname = User::where('id',$fancyResults->user_id)->first(); 
                    ?>
                  <tr class="white-bg">
                    <td>{{$count}}</td>
                    <td>{{$uname->user_name}} </td>
                    <td style="text-transform: uppercase;">
                      @if($fancyResults->bet_side == 'lay')
                        No
                      @else
                        YES
                      @endif
                    </td>
                    <td>{{$fancyResults->team_name}}</td>
                    <td style="font-weight: bold;">{{$fancyResults->bet_odds}}</td>
                    <td style="font-weight: bold;">{{$fancyResults->bet_amount}}</td>
                  </tr> 
                    <?php $count++; ?>
                  @endforeach  
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection