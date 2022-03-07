<?php    
$url=explode("/",$_SERVER['REQUEST_URI']);
$page1=$url[1];
$page=explode(".php",$page1);
$page2=$page[0];    
use App\User;
$chk = User::where('id',$id)->first();
?>


<ul class="left-menu white-bg">
    <li class="darkblue-bg text-color-white head"> Position </li>   
   

    


    
    <li <?php if($page2=='myaccount-statement') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-statement')}}" class="text-color-black"> Account Statement </a> </li>
    <li <?php if($page2=='myaccount-summary') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-summary')}}" class="text-color-black"> Account Summary </a> </li>
    <li <?php if($page2=='myaccount-trasferred-log') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-trasferred-log')}}" class="text-color-black"> Transferred Log </a> </li>

    <li class="darkblue-bg text-color-white head"> Account Details </li>
    <li <?php if($page2=='myaccount-profile') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-profile')}}" class="text-color-black"> Profile </a> </li>
    <li <?php if($page2=='myaccount-active-log') { ?> class="active" <?php } ?>> <a href="{{route('myaccount-active-log')}}" class="text-color-black"> Activity Log </a> </li>
</ul>