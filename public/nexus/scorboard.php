<?php
error_reporting(0);
$eventId = $_GET['event_id'];
$match_type = $_GET['match_type'];
$height = '110px;';
if($match_type == 'cricket') {
    $matchdate = $_GET['date'];
    $iFrameUrl = "https://www.shivexch.com/cricket_scoree/index.html?matchDate=".$matchdate."&mtid=".$eventId;
}
elseif($match_type == 'tennis'){
    $iFrameUrl = "http://shivexch.com/tennis_score3/index.html?eventid=" . $eventId;
}
elseif($match_type == 'soccer'){
    $iFrameUrl = "https://www.shivexch.com/soccer_scoree/index.html?eventid=" . $eventId;
}
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
