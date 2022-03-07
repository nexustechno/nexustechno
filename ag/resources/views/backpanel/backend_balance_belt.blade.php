<?php

use App\setting;
use App\CreditReference;
use App\User;
use App\Http\Controllers\AgentController;

$settings = "";
$balance = 0;
$auth_id = Auth::user()->id;
$auth_type = Auth::user()->agent_level;
if ($auth_type == 'COM') {
    $settings = setting::latest('id')->first();
    $balance = $settings->balance;
    $remain_bal = $settings->balance;;
} else {
    $settings = CreditReference::where('player_id', $auth_id)->first();
    $balance = $settings['available_balance_for_D_W'];
    $remain_bal = $settings['remain_bal'];
}

$depParent = AgentController::userBalance($auth_id);
$calData = explode("~", $depParent);

?>
<style type="text/css">
    .text-color-lght-grey {
        color: #000;
        font-weight: bold;
    }
</style>
<section>
    <div class="container">
        <div class="remaining-wrap white-bg text-color-blue-1">
            <div class="block-remain">
                <span class="text-color-lght-grey">Remaining Balance</span>
                <h4>{{ $website->currency }} {{number_format($balance,2, '.', '')}}  </h4>
            </div>
            @if($loginuser->agent_level != 'DL')
                <div class="block-remain">
                    <span class="text-color-lght-grey">Total Agent Balance</span>
                    <h4>{{ $website->currency }} {{number_format($calData[0],2, '.', '')}}</h4>
                </div>
            @endif
            <div class="block-remain">
                <span class="text-color-lght-grey">Total Client Balance</span>
                <h4>{{ $website->currency }} {{number_format($calData[1],2, '.', '')}}</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Exposure</span>
                <h4>{{ $website->currency }}
                    <div class="text-color-red">({{number_format(abs($calData[2]),2, '.', '')}})</div>
                </h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Available Balance</span>
                <h4>{{ $website->currency }} {{number_format($remain_bal,2, '.', '')}}</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">MY P/L</span>
                <h4>{{ $website->currency }}
                    <div class="exp_div" id="ledger_exposure_div">0.00</div>
                </h4>
            </div>
        </div>
    </div>
</section>
