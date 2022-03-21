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

if(isset($_GET['debug'])) {
    echo __FILE__ . " at line " . __LINE__ . "<br>";
    echo "<pre>";
    print_r($match_data);
}

$iframe = '';
if (isset($match_data['result']) && count($match_data['result']) > 0 && isset($match_data['result'][0])) {

    if ($match_data['result'][0]['type'] == 6) {
        $iframe = 'iframe';
    } else if ($match_data['result'][0]['type'] == 3) {
        $iframe = 'video';
    }

    if (isset($match_data['result'][0]['link']) && !empty($match_data['result'][0]['link'])) {
        $tvLink1 = $match_data['result'][0]['link'];
    }

    if (isset($match_data['result'][0]['link2']) && !empty($match_data['result'][0]['link2'])) {
        $tvLink1 = $match_data['result'][0]['link2'];
    }
}
?>
<html>
<head>
    <title>LIVE TV</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.0.0/video-js.css" rel="stylesheet">

</head>
<body style="margin: 0;width: 100%;height: 100%;display: block;position: relative;margin: 0 auto;text-align: center;background: black;">
<?php
if ($iframe == 'iframe') {
    ?>
    <iframe src="<?php echo $tvLink1; ?>" frameborder="0" style="height: 270px;padding: 0;width: 480px;"></iframe><?php
} else if ($iframe == 'video') {
    ?>
    <video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls autoplay loop muted data-setup='{"fluid": true, "autoplay": true}' style="height: 270px;padding: 0;width: 480px;">
        <source src="<?php echo $tvLink1; ?>" type="application/x-mpegURL">
    </video>

    <?php
}
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.0.0/video.min.js"></script>
<!--<script>-->
<!--    (function (window, videojs) {-->
<!--        var player = window.player = videojs('my_video_1');-->
<!--    }(window, window.videojs));-->
<!--</script>-->

</body>
</html>
