<?php    
$url=explode("/",$_SERVER['REQUEST_URI']);
$page1=$url[1];
$page=explode(".php",$page1);
$page2=$page[0];    
use App\User;
$chk = User::where('id',$id)->first();
?>

{{--<ul class="left-menu white-bg">
	<li class="darkblue-bg text-color-white head"> Position </li>	
	<li <?php if($page2=='downline-myaccount-summary') { ?> class="active" <?php } ?>>
        <a href="{{route('myaccount-summary')}}" class="text-color-black"> Account Summary </a>
    </li>
    <li <?php if($page2=='myaccount-statement') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-statement')}}" class="text-color-black"> Account Statement </a> </li>
    <li <?php if($page2=='myaccount-trasferred-log') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-trasferred-log')}}" class="text-color-black"> Transferred Log </a> </li>
	<li class="darkblue-bg text-color-white head"> Performance </li>
    <li <?php if($page2=='myaccount-profile') { ?> class="active" <?php } ?>> <a href="{{route('changePass',$id)}}" class="text-color-black"> Profile </a> </li>
    @if($chk->agent_level == 'PL')
	<li <?php if($page2=='betHistoryBack') { ?> class="active" <?php } ?>> <a href="{{route('betHistoryBack',$id)}}" class="text-color-black"> Bet History </a> </li>
	<li <?php if($page2=='betHistoryPLBack') { ?> class="active" <?php } ?>>
        <a href="{{route('betHistoryPLBack',$id)}}" class="text-color-black"> Betting Profit & Loss </a>
    </li>
    @endif
    <li <?php if($page2=='downline-myaccount-transaction') { ?> class="active" <?php } ?>>
        <a href="#" class="text-color-black"> Transaction History </a>
    </li>
	<li <?php if($page2=='downline-myaccount-activity') { ?> class="active" <?php } ?>>
        <a href="#" class="text-color-black"> Activity Log </a>
    </li>
</ul>--}}

<ul class="left-menu white-bg">
    <li class="darkblue-bg text-color-white head"> Position </li>   
   

    <li class="{{ (request()->is('backpanel/changePass*' )) ? 'active' : '' }}"> <a href="{{route('changePass',$id)}}" class="text-color-black"> Profile </a> </li>

    <li class="{{ (request()->is('backpanel/balanceoverview*' )) ? 'active' : '' }}"> <a href="{{route('balanceoverview',$id)}}" class="text-color-black"> Balance Overview </a> </li>



    @if($chk->agent_level == 'PL')
    
    <li class="{{ (request()->is('backpanel/betHistoryPLBack*' )) ? 'active' : '' }}">
        <a href="{{route('betHistoryPLBack',$id)}}" class="text-color-black"> Account Statement </a>
    </li>
    <li class="{{ (request()->is('backpanel/userBet*' )) ? 'active' : '' }}"> <a href="{{route('userBet',$id)}}" class="text-color-black"> My Bet </a> </li>

    <li class="{{ (request()->is('backpanel/betHistoryBack*' )) ? 'active' : '' }}">
        <a href="{{route('betHistoryBack',$id)}}" class="text-color-black">Bet History </a>
    </li>
    @endif
    
    <li class="{{ (request()->is('backpanel/activityLog*' )) ? 'active' : '' }}">
        <a href="{{route('activityLog',$id)}}" class="text-color-black"> Activity Log </a>
    </li>
</ul>