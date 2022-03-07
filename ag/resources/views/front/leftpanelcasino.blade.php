<?php
use App\Casino;
$casino = Casino::get();
$activesc='';
if(Request::path() == 'casino'){
    $activesc='active';
}
?>
<div class="left-menuga white-bg">
    <div class="casino_leftpanel black-bg1">
        <ul>
            <li class="{{$activesc}}"><a href="{{route('casino')}}" class="text-color-white">All Casino</a></li>
           <?php $count=0;?>
            @foreach($casino as $casinos)
            <?php          
            if($casinos->casino_name=='teen20'){
                $casinoName='20-20 Teenpatti';
            }elseif($casinos->casino_name=='baccarat'){
                $casinoName='Baccarat';
            }elseif($casinos->casino_name=='dt202'){
                $casinoName='20-20 Dragon Tiger 2';
            }elseif($casinos->casino_name=='ab20'){
                $casinoName='Andar Bahar';
            }elseif($casinos->casino_name=='32card'){
                $casinoName='32 Cards-B';
            }
            $url=$casinos->casino_name;
            $rou = $url."/".$casinos->id; 
            if(Request::path() == $rou){
                $actives='active';
            }else{
                $actives='';
            }
            $class="disabled-link";
            if($casinos->status==1){
                $class="";
            }
            ?>
            <li class="{{$actives}}"><a href="{{route($casinos->casino_name,$casinos->id)}}" class="text-color-white {{$class}}">{{$casinoName}}</a></li>
            <?php $count++; ?>
            @endforeach
        </ul>
    </div>
</div>