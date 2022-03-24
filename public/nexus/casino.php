<?php
$iframe = 'video';
$tvLink1 = "http://35.200.179.192/video/teen20";
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
    <iframe src="<?php echo $tvLink1; ?>" frameborder="0" style="padding: 0;"></iframe><?php
} else if ($iframe == 'video') {
    ?>
    <video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls autoplay loop muted data-setup='{"fluid": true, "autoplay": true}' style="padding: 0;">
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
