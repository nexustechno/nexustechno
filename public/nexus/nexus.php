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

if (isset($_GET['debug'])) {
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
        $stream_url = $match_data['result'][0]['link'];
    }

    if (isset($match_data['result'][0]['link2']) && !empty($match_data['result'][0]['link2'])) {
        $tvLink1 = $match_data['result'][0]['link2'];
        $stream_url2 = $match_data['result'][0]['link2'];
    }

    if (isset($match_data['result'][0]['channelIp']) && !empty($match_data['result'][0]['channelIp']) && isset($match_data['result'][0]['hdmi']) && !empty($match_data['result'][0]['hdmi'])) {
        $iframe = 'hdmi';
        $channelIp = $match_data['result'][0]['channelIp'];
        $hdmi = $match_data['result'][0]['hdmi'];
        $stream_url2 = "wss://" . $match_data['result'][0]['channelIp'] . "/" . $match_data['result'][0]['hdmi'];
    }
}
?>
<html>
<head>
    <title>LIVE TV</title>
    <link href="https://vjs.zencdn.net/7.18.1/video-js.css" rel="stylesheet"/>

    <script src="./js/hls.js@1.0.4"></script>
    <script src="./js/hls.js@latest"></script>
    <?php
    if ($iframe == 'video') {
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/hls.js/1.0.3/hls.js"></script>
        <link href="https://unpkg.com/video.js@7.13.3/dist/video-js.css" rel="stylesheet">
        <!--<script src=""></script> -->
        <script src="https://cdn.jsdelivr.net/npm/hls.js@canary"></script>

        <style>

            body {
                background-color: black;
                margin: 0px;
                padding: 0px;
            }

            .zoomed_mode {
                position: relative;
                top: 0px;
                right: 0px;
                bottom: 0px;
                left: 0px;
                margin: auto;
                max-height: 100%;
                width: 100%;
            }

            video::-webkit-media-controls-timeline {
                display: none;
            }

            video::-webkit-media-controls-fullscreen-button, video::-webkit-media-controls-picture-in-picture-button {
                display: none;
            }

            video::-webkit-media-controls-current-time-display, video::-webkit-media-controls-time-remaining-display, video::-webkit-media-controls-play-button, video::-webkit-media-controls-big-play-button {
                display: none;
            }

            audio::-webkit-media-controls-mute-button, video::-webkit-media-controls-mute-button {
                -webkit-appearance: media-mute-button;
                display: flex;
                width: 16px;
                height: 16px;
                background-color: none;
                border: initial;
                color: inherit;
            }

            video::-webkit-media-controls-panel {

                background-image: linear-gradient(transparent, transparent) !important;

            }

            video::-webkit-media-controls {
                visibility: hidden;
            }

            video::-webkit-media-controls-enclosure {
                visibility: visible;
            }

            video::-webkit-media-controls-overlay-play-button {
                display: none;
            }

            .hide {
                display: none;
            }

            .bgimage {
                width: 100%;
                height: 100%;
                position: fixed;
                left: 0px;
                top: 0px;
                z-index: -1;
            }

            .bgimage img {
                width: 100%;
                height: 100%;
            }

        </style>
        <?php
    }
    ?>

    <style>
        iframe, video {
            width: 480px;
            height: 270px !important;
        }

        @media only screen and (max-width: 480px) {
            iframe, video {
                width: 100%;
                height: 270px !important;
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body
    style="margin: 0;width: 100%;height: 100%;display: block;position: relative;margin: 0 auto;text-align: center;background: black;">
<?php
if ($iframe == 'iframe') {
    ?>
<iframe src="<?php echo $tvLink1; ?>" frameborder="0" style="padding: 0;"></iframe><?php
}
else if ($iframe == 'video') {
?>

    <div class="bgimage" id="poster">
        <img src="./GIF2022.gif"/>
    </div>
    <video id="video" class="zoomed_mode video-js vjs hide" controls muted autoplay playsinline disablepictureinpicture
           controlslist="nodownload"></video>

    <!--    <video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls autoplay loop muted data-setup='{"fluid": true, "autoplay": true}' style="padding: 0;">-->
    <!--        <source src="--><?php //echo $tvLink1; ?><!--" type="application/x-mpegURL">-->
    <!--    </video>-->

    <script src="https://vjs.zencdn.net/7.18.1/video.min.js"></script>

    <script>
        var config = {
            autoStartLoad: true,
            startPosition: -1,
            liveDurationInfinity: true,
            liveSyncDuration: 9,
            liveMaxLatencyDuration: 10
        };
    </script>
    <script>
        setTimeout(function () {
            $('#poster').addClass('hide');
            $('#video').removeClass('hide');
        }, 5000);
        var video = document.getElementById('video');

        var stream_url = '<?php echo $stream_url;?>';

        var stream_url2 = '<?php echo $stream_url2;?>';


        var ctry = 0;
        var maxtry = 3;

        if (Hls.isSupported()) {
            var hls = new Hls(config);

            hls.on(Hls.Events.ERROR, function (event, data) {
                var msg = "Player error: Type:" + data.type + " - Details:" + data.details + " - Fatal: " + data.fatal + " - Count: " + ctry;
                console.error(msg);

                if (data.details == 'bufferAddCodecError') {
                    location.reload();
                    return;
                }

                if (data.fatal) {
                    switch (data.type) {
                        case Hls.ErrorTypes.MEDIA_ERROR:
                            handleMediaError(hls);
                            break;
                        case Hls.ErrorTypes.NETWORK_ERROR:
                            console.error("network error ... 404 ctr: " + ctry);
                            if (ctry < maxtry) {
                                ctry++
                                setTimeout(() => hlsTryLoad(), 1000);
                            } else {
                                //window.top.location.reload();
                                location.reload();
                                return;
                            }
                            break;
                        default:
                            console.error("unrecoverable error");
                            hls.destroy();
                            location.reload();
                            return;
                            break;
                    }
                }
            });

            hls.loadSource(stream_url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                //video.play();
            });
        }
            // hls.js is not supported on platforms that do not have Media Source Extensions (MSE) enabled.
            // When the browser has built-in HLS support (check using `canPlayType`), we can provide an HLS manifest (i.e. .m3u8 URL) directly to the video element through the `src` property.
            // This is using the built-in support of the plain video element, without using hls.js.
            // Note: it would be more normal to wait on the 'canplay' event below however on Safari (where you are most likely to find built-in HLS support) the video.src URL must be on the user-driven
        // white-list before a 'canplay' event will be emitted; the last video event that can be reliably listened-for when the URL is not on the white-list is 'loadedmetadata'.
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = stream_url2;

            video.addEventListener('loadedmetadata', function () {
                video.play();
            });
        }

        function hlsTryLoad() {
            console.error("re-connecting....");
            hls.loadSource(stream_url);
            hls.startLoad();
        }
    </script>

<?php
}
else if ($iframe == 'hdmi'){
?>
    <div id="liveTvContent"></div>

    <script src="./pollyfill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script type="text/javascript">

        var channelIp = '<?php echo $channelIp; ?>';
        var hdmi = '<?php echo $hdmi; ?>';
        var program = "p1"

        $('#liveTvContent').html('<div id="hasTv"> <u1 id="' + program + '"></u1> <script type="text/javascript"> if ( "MediaSource" in window && "WebSocket" in window ){RunPlayer( \'' + program + '\', "100%", 200, \'' + channelIp + '\', "443", true, \'' + hdmi + '\', "", true, true, 0.01, "", false ); } else {document.getElementById(' + program + ').innerHTML = "Websockets are not supported in your browser."; } </script></div>');
    </script>
<?php
}
?>

</body>
</html>
