<?php
error_reporting(0);
$eventId = $_GET['event_id'];
$match_type = $_GET['match_type'];
//$height = '110px;';
//if($match_type == 'cricket') {
//    $matchdate = $_GET['date'];
//    $time = $_GET['time'];
//    $iFrameUrl = "https://www.shivexch.com/cricket_scoree/index.html?matchDate=".$matchdate." ".$time."&mtid=".$eventId;
//}
//elseif($match_type == 'tennis'){
//    $iFrameUrl = "https://shivexch.com/tennis_score3/index.html?eventid=" . $eventId;
//}
//elseif($match_type == 'soccer'){
//    $iFrameUrl = "https://www.shivexch.com/soccer_scoree/index.html?eventid=" . $eventId;
//}
$eventId2 = '';
if($match_type == 'cricket' || $match_type == 'tennis' || $match_type == 'soccer') {
    $url = "http://marketsarket.in:3002/scoreurl/".$eventId;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);

    $arr = json_decode($res,true);

    if(isset($_GET['debug'])){
        echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($arr);die();
    }
    if(isset($arr['score'])){
        $explode = explode("/",$arr['score']);
        $eventId2 = end($explode);
    }else{
        echo "<center><h1>OOPS!<br>Scoreboard not available right now</h1></center>";die();
    }
    $iFrameUrl = "https://central.satsport247.com/score_widget/" . $eventId2;
    $height = '180px;';
}else{
    $height = '110px;';
    $iFrameUrl = "https://bfscore.onlyscore.live/?id=" . $eventId;
}
if(!empty($eventId2)){
?>
<html>
<head>
    <title>LIVE SCOREBOARD</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body style="margin: 0;overflow: hidden;">
<iframe style="width: 100%; height: <?php echo $height; ?>" src="<?php echo $iFrameUrl; ?>" title="Iframe Example"></iframe>
</body>
</html>
<?php
}
?>
