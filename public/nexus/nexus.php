<?php

$eventid = $_GET['eventid'];
$sports_id = isset($_GET['sports_id']) ? $_GET['sports_id'] : 0;

if (empty($eventid)) {
    echo "<center><h1>OOPS!<br/><br/>Invalid Event ID provided</h1></center>";
}

$url = 'http://194.233.65.10/LiveTV/TVApi.svc/GetLiveTV?eventid=' . $eventid;

$headers = array('Content-Type: application/json');
$process = curl_init();
curl_setopt($process, CURLOPT_URL, $url);
curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_HTTPGET, 1);
curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$return = curl_exec($process);
curl_close($process);

$match_data = json_decode($return, true);

//echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($match_data);die();

if(isset($match_data['result']) && count($match_data['result']) > 0 && isset($match_data['result'][0])) {
    if(isset($match_data['result'][0]['link']) && !empty($match_data['result'][0]['link'])) {
        $tvLink = $match_data['result'][0]['link'];
    }else if(isset($match_data['result'][0]['link2']) && !empty($match_data['result'][0]['link2'])) {
        $tvLink = $match_data['result'][0]['link2'];
    }
}

if(!isset($tvLink)){
    echo "<center><h1>TV not available right now</h1></center>";die();
}
//if($sports_id == 4) {
//    $tvLink = "https://orangeexch999.com/jmp/nm3/all.php?eventId=" . $eventid;
//}else {
//    $tvLink = "http://194.233.65.10/LiveTV/TVApi.svc/GetLiveTV?eventid=" . $eventid;
//}

//die($tvLink);
?>
<html>
<head>
    <title>LIVE TV</title>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>-->
<!--    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!--    <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">-->
<!--    <script src="https://unpkg.com/video.js/dist/video.js"></script>-->
<!--    <script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>-->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.0.0/video-js.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.0.0/video.min.js"></script>
</head>
<body style="margin: 0;width: 100%;height: 100%;display: block;position: relative;">
<!--<iframe src="--><?php //echo $tvLink; ?><!--" frameborder="0" style="height: 100%;padding: 0;width: 100%"></iframe>-->
<video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls autoplay loop muted data-setup='{"fluid": true, "autoplay": true}' style="height: 270px;padding: 0;">
    <source src="<?php echo $tvLink; ?>" type="application/x-mpegURL">
</video>
</body>
</html>
