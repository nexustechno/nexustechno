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

        /*LIVE TV CSS*/
        .controls,.controls>* {
            padding: 0;
            margin: 0
        }

        .controls {
            overflow: hidden;
            background: dimgray;
            height: 38px;
            position: absolute;
            bottom: 0;
            left: 0
        }

        .controls[data-state=hidden] {
            display: none
        }

        .controls[data-state=visible] {
            display: block
        }

        .controls>* {
            float: left;
            width: 32px;
            height: 100%;
            margin-top: 3px;
            display: block
        }

        .controls>*:first-child {
            margin-left: 2px
        }

        .controls button {
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            border: none;
            cursor: pointer;
            text-indent: -99999px;
            background: transparent;
            background-size: 32px 32px;
            background-repeat: no-repeat
        }

        .controls button:hover,.controls button:focus {
            outline: 0
        }

        .controls button[data-state="play"] {
            background-image: url(data:image/png;base64,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)
        }

        .controls button[data-state="pause"] {
            background-image: url(data:image/png;base64,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)
        }

        .controls button[data-state="stop"] {
            background-image: url(data:image/png;base64,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)
        }

        .controls button[data-state="volume"] {
            background-image: url(data:image/png;base64,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)
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

        $('#liveTvContent').html('<div id="hasTv"> <u1 id="' + program + '"></u1></div>');

        if ( "MediaSource" in window && "WebSocket" in window ){
            RunPlayer( program , "100%", 200, channelIp , "443", true, hdmi , "", true, true, 0.01, "", false );
        } else {
            document.getElementById(' + program + ').innerHTML = "Websockets are not supported in your browser.";
        }

    </script>
<?php
}
?>

</body>
</html>
