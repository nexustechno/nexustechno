<?php
error_reporting(0);
$eventId = $_GET['event_id'];
$match_type = $_GET['match_type'];

if($match_type == 'cricket') {
    $url = "http://marketsarket.in:3002/scoreurl/".$eventId;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);

    $arr = json_decode($res,true);


    if(isset($arr['score'])){
        $explode = explode("/",$arr['score']);
        $eventId2 = end($explode);
    }else{
        echo "<center><h1>OOPS!<br>Scoreboard not available right now</h1></center>";die();
    }
    $iFrameUrl = "https://central.satsport247.com/score_widget/" . $eventId2;
    $height = '180px;';
}else{
    $height = '107px;';
    $iFrameUrl = "https://bfscore.onlyscore.live/?id=" . $eventId;
}

?>
<html>
<head>
    <title>LIVE SCOREBOARD</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<iframe style="width: 100%; height: <?php echo $height; ?>" src="<?php echo $iFrameUrl; ?>" title="Iframe Example"></iframe>
</body>
</html>
