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
        $stream_url = $match_data['result'][0]['link'];
    }

    if (isset($match_data['result'][0]['link2']) && !empty($match_data['result'][0]['link2'])) {
        $tvLink1 = $match_data['result'][0]['link2'];
        $stream_url2 = $match_data['result'][0]['link2'];
    }

    if(isset($match_data['result'][0]['channelIp']) && !empty($match_data['result'][0]['channelIp']) && isset($match_data['result'][0]['hdmi']) && !empty($match_data['result'][0]['hdmi'])){
        $iframe = 'hdmi';
        $stream_url2 = "wss://".$match_data['result'][0]['channelIp']."/".$match_data['result'][0]['hdmi'];
    }
}
?>
<html>
<head>
    <title>LIVE TV</title>
    <link href="https://vjs.zencdn.net/7.18.1/video-js.css" rel="stylesheet" />

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

                body{
                    background-color:black;
                    margin: 0px;
                    padding: 0px;
                }

                .zoomed_mode{
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
                video::-webkit-media-controls-fullscreen-button , video::-webkit-media-controls-picture-in-picture-button {
                    display: none;
                }

                video::-webkit-media-controls-current-time-display, video::-webkit-media-controls-time-remaining-display, video::-webkit-media-controls-play-button, video::-webkit-media-controls-big-play-button{
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
                .hide{
                    display: none;
                }
                .bgimage{
                    width: 100%;
                    height: 100%;
                    position: fixed;
                    left: 0px;
                    top: 0px;
                    z-index: -1;
                }
                .bgimage img{
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
<body style="margin: 0;width: 100%;height: 100%;display: block;position: relative;margin: 0 auto;text-align: center;background: black;">
<?php
if ($iframe == 'iframe') {
    ?>
    <iframe src="<?php echo $tvLink1; ?>" frameborder="0" style="padding: 0;"></iframe><?php
}
else if ($iframe == 'video') {
    ?>

    <div class="bgimage"  id="poster">
        <img src="./GIF2022.gif"/>
    </div>
    <video id="video" class="zoomed_mode video-js vjs hide" controls muted autoplay playsinline disablepictureinpicture controlslist="nodownload" ></video>

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
        setTimeout(function(){
            $('#poster').addClass('hide');
            $('#video').removeClass('hide');
        }, 5000);
        var video = document.getElementById('video');

        var stream_url = '<?php echo $stream_url;?>';

        var stream_url2 = '<?php echo $stream_url2;?>';


        var ctry = 0;
        var maxtry = 3;

        if(Hls.isSupported()) {
            var hls = new Hls(config);

            hls.on(Hls.Events.ERROR, function(event,data) {
                var  msg = "Player error: Type:" + data.type + " - Details:" + data.details + " - Fatal: " + data.fatal + " - Count: " +ctry;
                console.error(msg);

                if( data.details == 'bufferAddCodecError') {
                    location.reload();
                    return;
                }

                if(data.fatal) {
                    switch(data.type) {
                        case Hls.ErrorTypes.MEDIA_ERROR:
                            handleMediaError(hls);
                            break;
                        case Hls.ErrorTypes.NETWORK_ERROR:
                            console.error("network error ... 404 ctr: "+ctry);
                            if(ctry < maxtry) {
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
            hls.on(Hls.Events.MANIFEST_PARSED,function() {
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

            video.addEventListener('loadedmetadata',function() {
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
else if($iframe == 'hdmi'){
    ?>
        <!DOCTYPE html><html lang="en"><head>
        <script type="text/javascript">
            var LIVE_URL = "<?php echo $stream_url2; ?>";
            var WebSocketOriginal = WebSocket;
            WebSocket = function(url, protocols) {
                return new WebSocketOriginal(LIVE_URL, protocols);
            }
            WebSocket.prototype = WebSocketOriginal.prototype;
        </script>
        <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Player</title><style>body{
                padding: 0;
                margin: 0;
            }
        </style></head><body><script type="4e8a21deb402ee3041730b62-text/javascript">document.addEventListener('gesturestart', function (e) {
      e.preventDefault();
    });
</script><script src="./nanoplayer.min.js" type="4e8a21deb402ee3041730b62-text/javascript"></script><script type="4e8a21deb402ee3041730b62-text/javascript">function _0xbbf1(){var _0xa08a29=['\x67\x65\x74\x43\x6f\x6e\x74\x65\x78\x74','\x72\x65\x73\x74\x6f\x72\x65','\x63\x45\x69\x4a\x46','\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79','\x65\x6f\x53\x69\x79','\x36\x39\x36\x31\x34\x34\x35\x77\x47\x6f\x4b\x6f\x57','\x63\x61\x6e\x76\x61\x73','\x68\x69\x64\x65\x49\x6e\x63','\x63\x78\x58\x78\x46','\x73\x65\x61\x72\x63\x68','\x6e\x6f\x72\x6d\x61\x6c','\x6d\x65\x61\x73\x75\x72\x65\x54\x65\x78\x74','\x73\x61\x76\x65','\x33\x36\x34\x36\x33\x32\x31\x4f\x6a\x64\x4e\x62\x78','\x73\x74\x79\x6c\x65','\x71\x63\x73\x48\x57','\x62\x65\x67\x69\x6e\x50\x61\x74\x68','\x63\x6c\x65\x61\x72\x52\x65\x63\x74','\x6d\x65\x73\x73\x61\x67\x65','\x61\x70\x70\x65\x6e\x64\x43\x68\x69\x6c\x64','\x74\x6f\x53\x74\x72\x69\x6e\x67','\x73\x74\x72\x6f\x6b\x65\x53\x74\x79\x6c\x65','\x64\x65\x73\x74\x72\x6f\x79','\x64\x69\x76','\x7b\x77\x69\x64\x74\x68\x3a\x31\x30\x30\x76\x77\x20\x21\x69\x6d\x70\x6f\x72\x74\x61\x6e\x74\x3b\x20\x68\x65\x69\x67\x68\x74\x3a\x31\x30\x30\x76\x68\x20\x21\x69\x6d\x70\x6f\x72\x74\x61\x6e\x74\x3b\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79\x3a\x20\x68\x69\x64\x64\x65\x6e\x3b\x7d','\x73\x74\x6f\x70','\x52\x61\x61\x6b\x50','\x68\x69\x64\x64\x65\x6e','\x2f\x2f\x64\x65\x6d\x6f\x2e\x6e\x61\x6e\x6f\x63\x6f\x73\x6d\x6f\x73\x2e\x64\x65\x2f\x6e\x61\x6e\x6f\x70\x6c\x61\x79\x65\x72\x2f\x6e\x61\x6e\x6f\x2e\x70\x6c\x61\x79\x65\x72\x2e\x73\x77\x66','\x6e\x6f\x6e\x65','\x61\x62\x73\x6f\x6c\x75\x74\x65','\x63\x72\x65\x61\x74\x65\x45\x6c\x65\x6d\x65\x6e\x74','\x45\x72\x72\x6f\x72\x20\x3d\x20','\x41\x76\x37\x67\x66\x2d\x38\x56\x46\x4b\x64','\x73\x65\x74\x75\x70','\x34\x4d\x61\x59\x62\x66\x6f','\x77\x69\x64\x74\x68','\x76\x69\x73\x69\x62\x6c\x65','\x33\x32\x37\x33\x32\x33\x34\x67\x6d\x77\x5a\x75\x66','\x54\x68\x6d\x6a\x66','\x28\x28\x28\x2e\x2b\x29\x2b\x29\x2b\x29\x2b\x24','\x6d\x6f\x76\x65\x54\x6f','\x66\x69\x6c\x6c\x53\x74\x79\x6c\x65','\x56\x62\x76\x48\x65','\x66\x69\x6c\x6c\x54\x65\x78\x74','\x72\x65\x6d\x6f\x76\x65\x43\x68\x69\x6c\x64','\x68\x65\x69\x67\x68\x74','\x64\x61\x74\x61','\x69\x6e\x6e\x65\x72\x48\x65\x69\x67\x68\x74','\x6c\x79\x58\x5a\x46','\x4e\x61\x6e\x6f\x50\x6c\x61\x79\x65\x72','\x33\x39\x36\x30\x31\x35\x44\x64\x71\x6b\x42\x73','\x5a\x45\x4c\x78\x56','\x64\x69\x73\x70\x6f\x73\x65','\x74\x79\x70\x65','\x31\x36\x34\x38\x31\x38\x30\x38\x34\x37','\x76\x69\x64\x65\x6f\x70\x6c\x61\x79\x65\x72\x2f\x6e\x70\x6c\x61\x79\x65\x72\x2f\x6a\x73\x2f\x76\x69\x64\x65\x6f\x62\x67\x2e\x6a\x70\x67','\x72\x65\x74\x75\x72\x6e\x56\x61\x6c\x75\x65','\x74\x65\x78\x74\x2f\x63\x73\x73','\x33\x38\x33\x31\x33\x33\x36\x72\x73\x6e\x52\x43\x75','\x75\x70\x64\x61\x74\x65\x54\x65\x78\x74','\x72\x65\x61\x73\x6f\x6e','\x73\x74\x72\x69\x6e\x67\x69\x66\x79','\x64\x65\x76\x69\x63\x65\x50\x69\x78\x65\x6c\x52\x61\x74\x69\x6f','\x64\x69\x73\x70\x6c\x61\x79','\x67\x65\x74\x45\x6c\x65\x6d\x65\x6e\x74\x73\x42\x79\x54\x61\x67\x4e\x61\x6d\x65','\x63\x72\x65\x61\x74\x65\x54\x65\x78\x74\x4e\x6f\x64\x65','\x35\x37\x30\x30\x33\x37\x30\x51\x53\x51\x48\x4c\x66','\x77\x73\x73\x3a\x2f\x2f\x62\x69\x6e\x74\x75\x2d\x68\x35\x6c\x69\x76\x65\x2d\x73\x65\x63\x75\x72\x65\x2e\x6e\x61\x6e\x6f\x63\x6f\x73\x6d\x6f\x73\x2e\x64\x65\x3a\x34\x34\x33\x2f\x68\x35\x6c\x69\x76\x65\x2f\x61\x75\x74\x68\x73\x74\x72\x65\x61\x6d\x2f\x73\x74\x72\x65\x61\x6d\x2e\x6d\x70\x34','\x41\x48\x41\x4c\x76','\x61\x64\x64\x45\x76\x65\x6e\x74\x4c\x69\x73\x74\x65\x6e\x65\x72','\x47\x58\x6c\x6b\x4f','\x31\x36\x2f\x39','\x51\x52\x75\x74\x75','\x72\x6f\x74\x61\x74\x65','\x61\x70\x70\x6c\x79','\x61\x62\x73','\x74\x72\x61\x6e\x73\x6c\x61\x74\x65','\x73\x68\x6f\x77\x49\x6e\x63','\x68\x66\x66\x69\x65','\x41\x42\x4e\x4d\x4f','\x6c\x6f\x67','\x47\x57\x7a\x6f\x5a','\x76\x69\x64\x65\x6f','\x62\x6f\x64\x79','\x61\x6f\x6f\x6a\x51','\x68\x74\x74\x70\x73\x3a\x2f\x2f\x73\x69\x74\x65\x74\x68\x65\x6d\x65\x64\x61\x74\x61\x2e\x63\x6f\x6d\x2f\x76\x35\x30\x2f\x73\x74\x61\x74\x69\x63\x2f','\x20\x28\x25\x72\x65\x61\x73\x6f\x6e\x25\x29','\x72\x6f\x75\x6e\x64','\x44\x4f\x4d\x43\x6f\x6e\x74\x65\x6e\x74\x4c\x6f\x61\x64\x65\x64','\x31\x30\x30\x25','\x72\x74\x6d\x70\x3a\x2f\x2f\x62\x69\x6e\x74\x75\x2d\x73\x70\x6c\x61\x79\x2e\x6e\x61\x6e\x6f\x63\x6f\x73\x6d\x6f\x73\x2e\x64\x65\x3a\x31\x39\x33\x35\x2f\x73\x70\x6c\x61\x79','\x6c\x69\x6e\x65\x54\x6f','\x74\x6f\x70','\x73\x74\x61\x72\x74','\x6c\x69\x6e\x65\x43\x61\x70','\x70\x6f\x73\x69\x74\x69\x6f\x6e','\x32\x35\x34\x39\x37\x37\x38\x6d\x5a\x47\x43\x67\x61','\x68\x42\x5a\x4e\x42','\x31\x36\x4f\x64\x59\x52\x47\x42','\x73\x74\x72\x6f\x6b\x65','\x69\x6e\x69\x74','\x68\x65\x61\x64','\x6c\x69\x6e\x65\x57\x69\x64\x74\x68','\x36\x4e\x71\x62\x42\x66\x6a','\x72\x67\x62\x61\x28\x32\x30\x30\x2c\x20\x32\x30\x30\x2c\x20\x32\x30\x30\x2c','\x69\x6e\x6e\x65\x72\x57\x69\x64\x74\x68','\x77\x67\x4b\x6f\x42\x49\x55\x70\x68\x6d','\x70\x6f\x73\x74\x65\x72','\x72\x65\x70\x6c\x61\x63\x65'];_0xbbf1=function(){return _0xa08a29;};return _0xbbf1();}function _0x555c(_0x5395d6,_0xbd2b85){var _0xd70b91=_0xbbf1();return _0x555c=function(_0x396c7d,_0x2e286e){_0x396c7d=_0x396c7d-0x12c;var _0xbbf190=_0xd70b91[_0x396c7d];return _0xbbf190;},_0x555c(_0x5395d6,_0xbd2b85);}(function(_0x9a2055,_0xe6b8dc){var _0x4f3d63=_0x555c,_0x99612b=_0x9a2055();while(!![]){try{var _0x38fb29=parseInt(_0x4f3d63(0x199))/0x1*(-parseInt(_0x4f3d63(0x160))/0x2)+-parseInt(_0x4f3d63(0x159))/0x3+-parseInt(_0x4f3d63(0x189))/0x4*(-parseInt(_0x4f3d63(0x16b))/0x5)+-parseInt(_0x4f3d63(0x18c))/0x6+parseInt(_0x4f3d63(0x173))/0x7*(parseInt(_0x4f3d63(0x15b))/0x8)+parseInt(_0x4f3d63(0x133))/0x9+parseInt(_0x4f3d63(0x13b))/0xa;if(_0x38fb29===_0xe6b8dc)break;else _0x99612b['push'](_0x99612b['shift']());}catch(_0x2be1fa){_0x99612b['push'](_0x99612b['shift']());}}}(_0xbbf1,0xce9f6),(function(){var _0x5add1e=_0x555c,_0x69ed6c=(function(){var _0x4512b7=_0x555c;if(_0x4512b7(0x16a)==='\x67\x47\x62\x62\x52')_0x3ed243[_0x4512b7(0x17c)]();else{var _0x1214d5=!![];return function(_0xc1a974,_0x135a25){var _0x1ca4e1=_0x1214d5?function(){var _0xb0c162=_0x555c;if(_0xb0c162(0x197)!==_0xb0c162(0x180)){if(_0x135a25){if(_0xb0c162(0x148)!==_0xb0c162(0x148))_0x1dcd7a[_0xb0c162(0x174)]['\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79']=_0xb0c162(0x181);else{var _0x21b8d9=_0x135a25['\x61\x70\x70\x6c\x79'](_0xc1a974,arguments);return _0x135a25=null,_0x21b8d9;}}}else{var _0x546f6d='\x72\x67\x62\x61\x28\x30\x2c\x20\x30\x2c\x20\x30\x2c\x20\x31\x2e\x30\x29';_0x230a1a[_0xb0c162(0x17b)]=_0x546f6d,_0x52b732[_0xb0c162(0x190)]=_0x546f6d,_0x50f894[_0xb0c162(0x15f)]=0x1;var _0x56d0b0=0xd;_0x116270['\x66\x6f\x6e\x74']=_0x56d0b0+'\x70\x78\x20\x41\x72\x69\x61\x6c';var _0x2b27a9=_0x50cbd8[_0xb0c162(0x171)](_0x3ddf63)[_0xb0c162(0x18a)];_0x31272c[_0xb0c162(0x145)](_0x3f9dbd/0x2-_0x2b27a9/0x2,_0x56d0b0*0x2),_0x1369f3[_0xb0c162(0x192)](_0x37533b,0x0,0x0);}}:function(){};return _0x1214d5=![],_0x1ca4e1;};}}()),_0x96d1f7=_0x69ed6c(this,function(){var _0x29e067=_0x555c;if(_0x29e067(0x168)===_0x29e067(0x13f)){_0x26685e=_0x395cf8['\x63\x61\x6e\x76\x61\x73']=_0x157018||_0x4a49b8[_0x29e067(0x185)](_0x29e067(0x16c));var _0x1fd38a={'\x77\x69\x64\x74\x68':_0x2e3d71[_0x29e067(0x162)],'\x68\x65\x69\x67\x68\x74':_0x419418[_0x29e067(0x196)]},_0x5dd9fc=_0x17baa1['\x6d\x61\x78'](_0x1fd38a[_0x29e067(0x18a)],_0x1fd38a[_0x29e067(0x194)]);_0x28e6e6['\x77\x69\x64\x74\x68']=_0x5dd9fc/_0xb13a33,_0x8a3e28[_0x29e067(0x194)]=_0x5dd9fc/_0x358a75,_0x7ed31e[_0x29e067(0x174)][_0x29e067(0x158)]=_0x29e067(0x184);var _0x34f468=0x0,_0x354501=0x0;return _0x59c33e[_0x29e067(0x174)][_0x29e067(0x155)]=_0x261264[_0x29e067(0x150)](_0x34f468+_0x1fd38a[_0x29e067(0x194)]/0x2-_0x7a189b[_0x29e067(0x194)]/0x2)+'\x70\x78',_0x20d9e7[_0x29e067(0x174)]['\x6c\x65\x66\x74']=_0x5f85f5[_0x29e067(0x150)](_0x354501+_0x1fd38a[_0x29e067(0x18a)]/0x2-_0x26cfa8[_0x29e067(0x18a)]/0x2)+'\x70\x78',_0x421fac;}else return _0x96d1f7[_0x29e067(0x17a)]()[_0x29e067(0x16f)](_0x29e067(0x18e))[_0x29e067(0x17a)]()['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f\x72'](_0x96d1f7)[_0x29e067(0x16f)](_0x29e067(0x18e));});_0x96d1f7();var _0x388300=window[_0x5add1e(0x198)];delete window[_0x5add1e(0x198)];var _0x17aef8=_0x5add1e(0x14e),_0x606e0=_0x5add1e(0x163),_0x63314e=_0x5add1e(0x187),_0x24032f='\x63\x37\x35\x61\x66\x66\x35\x31\x64\x38\x35\x33\x64\x62\x61\x65\x61\x62\x35\x31\x61\x34\x37\x34\x34\x61\x34\x39\x39\x37\x38\x32\x2b\x64\x32\x32\x64\x61\x31\x32\x31\x33\x36\x34\x64\x31\x63\x66\x32\x61\x31\x66\x66\x63\x37\x61\x66\x39\x63\x61\x63\x64\x37\x36\x32',_0x1c344e=_0x5add1e(0x12f),_0x56138b='\x31\x33',_0x57d070='\x67\x6f\x6c\x64\x76\x73\x37',_0x26336b,_0x25bc8e,_0x38bf01,_0x302d9e={},_0x1956ec=0x0,_0xc16ae3={'\x73\x6f\x75\x72\x63\x65':{'\x68\x35\x6c\x69\x76\x65':{'\x73\x65\x72\x76\x65\x72':{'\x77\x65\x62\x73\x6f\x63\x6b\x65\x74':_0x5add1e(0x13c),'\x68\x6c\x73':'\x68\x74\x74\x70\x73\x3a\x2f\x2f\x62\x69\x6e\x74\x75\x2d\x68\x35\x6c\x69\x76\x65\x2d\x73\x65\x63\x75\x72\x65\x2e\x6e\x61\x6e\x6f\x63\x6f\x73\x6d\x6f\x73\x2e\x64\x65\x3a\x34\x34\x33\x2f\x68\x35\x6c\x69\x76\x65\x2f\x61\x75\x74\x68\x68\x74\x74\x70\x2f\x70\x6c\x61\x79\x6c\x69\x73\x74\x2e\x6d\x33\x75\x38'},'\x72\x74\x6d\x70':{'\x75\x72\x6c':_0x5add1e(0x153),'\x73\x74\x72\x65\x61\x6d\x6e\x61\x6d\x65':_0x63314e},'\x73\x65\x63\x75\x72\x69\x74\x79':{'\x74\x6f\x6b\x65\x6e':_0x24032f,'\x65\x78\x70\x69\x72\x65\x73':_0x1c344e,'\x6f\x70\x74\x69\x6f\x6e\x73':_0x56138b,'\x74\x61\x67':_0x57d070}}},'\x70\x6c\x61\x79\x62\x61\x63\x6b':{'\x61\x75\x74\x6f\x70\x6c\x61\x79':!![],'\x61\x75\x74\x6f\x6d\x75\x74\x65':!![],'\x6d\x75\x74\x65\x64':![],'\x66\x6c\x61\x73\x68\x70\x6c\x61\x79\x65\x72':_0x5add1e(0x182)},'\x73\x74\x79\x6c\x65':{'\x77\x69\x64\x74\x68':_0x5add1e(0x152),'\x68\x65\x69\x67\x68\x74':'\x31\x30\x30\x25','\x61\x73\x70\x65\x63\x74\x72\x61\x74\x69\x6f':_0x5add1e(0x140),'\x63\x6f\x6e\x74\x72\x6f\x6c\x73':![],'\x73\x63\x61\x6c\x69\x6e\x67':'\x6c\x65\x74\x74\x65\x72\x62\x6f\x78'},'\x65\x76\x65\x6e\x74\x73':{'\x6f\x6e\x52\x65\x61\x64\x79':function(_0x3bb6b4){},'\x6f\x6e\x50\x6c\x61\x79':function(_0x4e595d){var _0x5f59e1=_0x5add1e;_0x38bf01&&('\x68\x42\x5a\x4e\x42'!==_0x5f59e1(0x15a)?(_0x36023f['\x73\x74\x79\x6c\x65']['\x64\x69\x73\x70\x6c\x61\x79']=_0x5f59e1(0x183),_0x4f8c33&&(_0x25823c(_0x1dbd4a),_0x536b60=0x0)):(_0x38bf01[_0x5f59e1(0x16d)](),_0x38bf01['\x64\x69\x73\x70\x6f\x73\x65'](),document['\x62\x6f\x64\x79'][_0x5f59e1(0x193)](_0x38bf01['\x63\x61\x6e\x76\x61\x73']))),clearInterval(_0x1956ec),_0x1956ec=0x0,_0x26336b[_0x5f59e1(0x174)]['\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79']=_0x5f59e1(0x18b);},'\x6f\x6e\x50\x61\x75\x73\x65':function(_0x2cf565){var _0x374d9e=_0x5add1e,_0x27f511=_0x2cf565[_0x374d9e(0x195)][_0x374d9e(0x135)]!==_0x374d9e(0x170)?_0x374d9e(0x14f)[_0x374d9e(0x165)]('\x25\x72\x65\x61\x73\x6f\x6e\x25',_0x2cf565['\x64\x61\x74\x61'][_0x374d9e(0x135)]):'';},'\x6f\x6e\x4c\x6f\x61\x64\x69\x6e\x67':function(_0x23f6e3){},'\x6f\x6e\x53\x74\x61\x72\x74\x42\x75\x66\x66\x65\x72\x69\x6e\x67':function(_0x259089){var _0x32ee59=_0x5add1e;if(_0x32ee59(0x14a)!=='\x6a\x78\x49\x56\x68')_0x302d9e['\x73\x74\x61\x72\x74']=new Date(),setTimeout(function(){var _0x5527f3=_0x32ee59;if(_0x302d9e[_0x5527f3(0x156)]){}},0x7d0);else{var _0x1dc3bf=_0x3be967['\x73\x74\x72\x69\x6e\x67\x69\x66\x79'](_0x28be68);_0x1dc3bf==='\x7b\x7d'&&(_0x1dc3bf=_0x185302[_0x32ee59(0x178)]),_0x1ce6ec=_0x1dc3bf;}},'\x6f\x6e\x53\x74\x6f\x70\x42\x75\x66\x66\x65\x72\x69\x6e\x67':function(_0x31e095){var _0x2259e4=_0x5add1e;if(_0x2259e4(0x13d)!==_0x2259e4(0x175)){_0x302d9e[_0x2259e4(0x17f)]=new Date();if(_0x302d9e[_0x2259e4(0x156)]){if(_0x2259e4(0x16e)!==_0x2259e4(0x141)){var _0x4845fa=Math[_0x2259e4(0x144)](_0x302d9e[_0x2259e4(0x17f)]-_0x302d9e[_0x2259e4(0x156)]);if(_0x4845fa>0x3e8){}_0x302d9e[_0x2259e4(0x17f)]=_0x302d9e[_0x2259e4(0x156)]=0x0;}else _0x1ef099['\x73\x74\x79\x6c\x65'][_0x2259e4(0x169)]=_0x2259e4(0x181);}}else var _0x3d7a6d=_0x772720[_0x2259e4(0x195)][_0x2259e4(0x135)]!=='\x6e\x6f\x72\x6d\x61\x6c'?_0x2259e4(0x14f)[_0x2259e4(0x165)]('\x25\x72\x65\x61\x73\x6f\x6e\x25',_0x52f245[_0x2259e4(0x195)][_0x2259e4(0x135)]):'';},'\x6f\x6e\x57\x61\x72\x6e\x69\x6e\x67':function(_0x4966f7){},'\x6f\x6e\x45\x72\x72\x6f\x72':function(_0x549faf){var _0x449f79=_0x5add1e;try{var _0xdb5985=JSON[_0x449f79(0x136)](_0x549faf);_0xdb5985==='\x7b\x7d'&&(_0xdb5985=_0x549faf['\x6d\x65\x73\x73\x61\x67\x65']),_0x549faf=_0xdb5985;}catch(_0x112d3c){}console[_0x449f79(0x149)](_0x449f79(0x186)+_0x549faf),setTimeout(function(){_0x1e6dbf();},0x7d0);},'\x6f\x6e\x44\x65\x73\x74\x72\x6f\x79':function(_0x573d2f){}}};document[_0x5add1e(0x13e)](_0x5add1e(0x151),function(){var _0xa301e7=_0x5add1e;if(_0xa301e7(0x191)===_0xa301e7(0x14d))_0x2455e8(_0x4c7d20);else{var _0x53847a='\x23'+_0x606e0+_0xa301e7(0x17e),_0x9a7cb8=document[_0xa301e7(0x15e)]||document[_0xa301e7(0x139)]('\x68\x65\x61\x64')[0x0],_0x3bcb82=document[_0xa301e7(0x185)](_0xa301e7(0x174));_0x9a7cb8[_0xa301e7(0x179)](_0x3bcb82),_0x3bcb82[_0xa301e7(0x12e)]=_0xa301e7(0x132),_0x3bcb82[_0xa301e7(0x179)](document[_0xa301e7(0x13a)](_0x53847a)),_0x26336b=document[_0xa301e7(0x185)](_0xa301e7(0x17d)),_0x26336b['\x69\x64']=_0x606e0,document[_0xa301e7(0x14c)][_0xa301e7(0x179)](_0x26336b),_0x1956ec=setInterval(function(){var _0x5c19ea=_0xa301e7;_0x26336b[_0x5c19ea(0x174)]['\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79']=_0x5c19ea(0x181);},0x5),_0x25bc8e=new _0x388300(_0x606e0),_0x1e6dbf(),setTimeout(function(){var _0x10b706=_0xa301e7;_0x38bf01=new _0x580f6f(null,0xa),_0x38bf01['\x69\x6e\x69\x74'](),_0x38bf01[_0x10b706(0x146)](),document['\x62\x6f\x64\x79'][_0x10b706(0x179)](_0x38bf01['\x63\x61\x6e\x76\x61\x73']);},0x1);}});function _0x1e6dbf(){var _0x451f56=_0x5add1e;_0x25bc8e[_0x451f56(0x188)](_0xc16ae3)['\x74\x68\x65\x6e'](function(_0x4c9e07){var _0x2e4bf6=_0x451f56;if(_0x2e4bf6(0x147)!=='\x7a\x47\x5a\x48\x4e'){var _0x5bdf60=_0x26336b['\x71\x75\x65\x72\x79\x53\x65\x6c\x65\x63\x74\x6f\x72'](_0x2e4bf6(0x14b));if(_0x5bdf60)_0x5bdf60['\x73\x65\x74\x41\x74\x74\x72\x69\x62\x75\x74\x65'](_0x2e4bf6(0x164),_0x17aef8+_0x2e4bf6(0x130));}else _0x2432ac(_0x55d955),_0x13666c=0x0;},function(_0x2c22f3){});}function _0x2344c7(){var _0x22483e=_0x5add1e;_0x25bc8e&&_0x25bc8e[_0x22483e(0x17c)]();}window['\x6f\x6e\x62\x65\x66\x6f\x72\x65\x75\x6e\x6c\x6f\x61\x64']=function(_0x3884b6){var _0x4d31d7=_0x5add1e;_0x2344c7(),setInterval(function(){var _0x1ab76f=_0x555c;_0x26336b&&(_0x26336b[_0x1ab76f(0x174)][_0x1ab76f(0x169)]='\x68\x69\x64\x64\x65\x6e');},0x5),_0x26336b&&(_0x26336b[_0x4d31d7(0x174)]['\x76\x69\x73\x69\x62\x69\x6c\x69\x74\x79']=_0x4d31d7(0x181)),delete _0x3884b6[_0x4d31d7(0x131)];};function _0x580f6f(_0x1ea6d3,_0x583a15){var _0x24a6de=_0x5add1e,_0x2b22e2=this;_0x2b22e2[_0x24a6de(0x15d)]=function(){var _0x2d2987=_0x24a6de;_0x1ea6d3=_0x2b22e2['\x63\x61\x6e\x76\x61\x73']=_0x1ea6d3||document['\x63\x72\x65\x61\x74\x65\x45\x6c\x65\x6d\x65\x6e\x74']('\x63\x61\x6e\x76\x61\x73');var _0x57bf0f={'\x77\x69\x64\x74\x68':window[_0x2d2987(0x162)],'\x68\x65\x69\x67\x68\x74':window['\x69\x6e\x6e\x65\x72\x48\x65\x69\x67\x68\x74']},_0x1640f5=Math['\x6d\x61\x78'](_0x57bf0f['\x77\x69\x64\x74\x68'],_0x57bf0f[_0x2d2987(0x194)]);_0x1ea6d3[_0x2d2987(0x18a)]=_0x1640f5/_0x583a15,_0x1ea6d3[_0x2d2987(0x194)]=_0x1640f5/_0x583a15,_0x1ea6d3[_0x2d2987(0x174)]['\x70\x6f\x73\x69\x74\x69\x6f\x6e']='\x61\x62\x73\x6f\x6c\x75\x74\x65';var _0x10172d=0x0,_0x36906e=0x0;return _0x1ea6d3[_0x2d2987(0x174)][_0x2d2987(0x155)]=Math[_0x2d2987(0x150)](_0x10172d+_0x57bf0f[_0x2d2987(0x194)]/0x2-_0x1ea6d3[_0x2d2987(0x194)]/0x2)+'\x70\x78',_0x1ea6d3['\x73\x74\x79\x6c\x65']['\x6c\x65\x66\x74']=Math[_0x2d2987(0x150)](_0x36906e+_0x57bf0f[_0x2d2987(0x18a)]/0x2-_0x1ea6d3[_0x2d2987(0x18a)]/0x2)+'\x70\x78',_0x2b22e2;},_0x2b22e2[_0x24a6de(0x146)]=function(){var _0xb9b2b6=_0x24a6de;if(_0xb9b2b6(0x18d)!=='\x6d\x6a\x62\x73\x64'){_0x2f7781++,_0x1ea6d3[_0xb9b2b6(0x174)][_0xb9b2b6(0x138)]='';if(!_0x5830a0){if('\x50\x4d\x51\x73\x61'!=='\x50\x4d\x51\x73\x61'){var _0x30c617=_0x4875cb[_0xb9b2b6(0x143)](_0x4c5444,arguments);return _0x2ad2b7=null,_0x30c617;}else _0x471bd6=new Date(),_0x5830a0=setInterval(_0x490a3d,0x3e8/0x1e),_0x490a3d();}return _0x2b22e2;}else _0x39127d['\x68\x69\x64\x65\x49\x6e\x63'](),_0x4a9e96['\x64\x69\x73\x70\x6f\x73\x65'](),_0x671f03[_0xb9b2b6(0x14c)]['\x72\x65\x6d\x6f\x76\x65\x43\x68\x69\x6c\x64'](_0x75e24d['\x63\x61\x6e\x76\x61\x73']);},_0x2b22e2[_0x24a6de(0x16d)]=function(){var _0x406e4a=_0x24a6de;_0x2f7781--;if(_0x2f7781<0x0)_0x2f7781=0x0;return _0x2f7781===0x0&&(_0x1ea6d3[_0x406e4a(0x174)][_0x406e4a(0x138)]='\x6e\x6f\x6e\x65',_0x5830a0&&(clearInterval(_0x5830a0),_0x5830a0=0x0)),_0x2b22e2;},_0x2b22e2[_0x24a6de(0x12d)]=function(){var _0xff945e=_0x24a6de;_0x5830a0&&clearInterval(_0x5830a0),_0x1ea6d3[_0xff945e(0x174)][_0xff945e(0x138)]=_0xff945e(0x183),_0x5830a0=0x0;},_0x2b22e2[_0x24a6de(0x134)]=function(_0x3bd97f){var _0x223297=_0x24a6de;if(_0x223297(0x12c)!==_0x223297(0x12c))_0x585f7e=new _0x3d978c(null,0xa),_0x5bcf60[_0x223297(0x15d)](),_0x5d32a3[_0x223297(0x146)](),_0x22439f['\x62\x6f\x64\x79'][_0x223297(0x179)](_0x4a984f['\x63\x61\x6e\x76\x61\x73']);else return _0xbadd6=_0x3bd97f,_0x5830a0&&('\x66\x49\x52\x6d\x50'==='\x66\x49\x52\x6d\x50'?_0x490a3d():(_0x3e1219[_0x223297(0x176)](),_0x36bd8a[_0x223297(0x142)](_0x410ba7['\x50\x49']*0x2/_0x57359f),_0x4f404c[_0x223297(0x18f)](_0x4f5a77/0xa,0x0),_0x2e0790['\x6c\x69\x6e\x65\x54\x6f'](_0x5672bd/0x4,0x0),_0x3671d5['\x6c\x69\x6e\x65\x57\x69\x64\x74\x68']=_0x2e7a38/0x1e,_0xe44bf9[_0x223297(0x17b)]=_0x223297(0x161)+_0x47fff7/_0x526534+'\x29',_0x1c124b[_0x223297(0x15c)]())),_0x2b22e2;};var _0x5830a0=0x0,_0x471bd6=new Date(),_0x48ae67=0x10,_0xbadd6='',_0x2f7781=0x0;function _0x490a3d(){var _0x495e9b=_0x24a6de,_0x3a0f51={'\x77\x69\x64\x74\x68':window['\x69\x6e\x6e\x65\x72\x57\x69\x64\x74\x68'],'\x68\x65\x69\x67\x68\x74':window['\x69\x6e\x6e\x65\x72\x48\x65\x69\x67\x68\x74']},_0x4faa34=Math['\x6d\x61\x78'](_0x3a0f51[_0x495e9b(0x18a)],_0x3a0f51[_0x495e9b(0x194)]);_0x1ea6d3['\x77\x69\x64\x74\x68']=window[_0x495e9b(0x137)]*_0x4faa34/_0x583a15,_0x1ea6d3[_0x495e9b(0x194)]=window['\x64\x65\x76\x69\x63\x65\x50\x69\x78\x65\x6c\x52\x61\x74\x69\x6f']*_0x4faa34/_0x583a15,_0x1ea6d3[_0x495e9b(0x174)][_0x495e9b(0x18a)]=_0x4faa34/_0x583a15+'\x70\x78',_0x1ea6d3[_0x495e9b(0x174)][_0x495e9b(0x194)]=_0x4faa34/_0x583a15+'\x70\x78';var _0x772431=0x0,_0x40f863=0x0;_0x1ea6d3[_0x495e9b(0x174)]['\x74\x6f\x70']=Math[_0x495e9b(0x150)](_0x772431+_0x3a0f51[_0x495e9b(0x194)]/0x2-_0x4faa34/_0x583a15/0x2)+'\x70\x78',_0x1ea6d3[_0x495e9b(0x174)]['\x6c\x65\x66\x74']=Math[_0x495e9b(0x150)](_0x40f863+_0x3a0f51[_0x495e9b(0x18a)]/0x2-_0x4faa34/_0x583a15/0x2)+'\x70\x78';var _0x14f669=_0x1ea6d3[_0x495e9b(0x166)]('\x32\x64'),_0x3af38a=_0x14f669[_0x495e9b(0x16c)]['\x77\x69\x64\x74\x68'],_0x4aad66=_0x14f669[_0x495e9b(0x16c)][_0x495e9b(0x194)],_0x28e754=parseInt((new Date()-_0x471bd6)/0x3e8*_0x48ae67)/_0x48ae67,_0x30d342=_0x3af38a;_0x14f669[_0x495e9b(0x177)](0x0,0x0,_0x3af38a,_0x4aad66),_0x14f669[_0x495e9b(0x172)](),_0x14f669[_0x495e9b(0x157)]=_0x495e9b(0x150),_0x14f669[_0x495e9b(0x145)](_0x3af38a/0x2,_0x4aad66/0x2),_0x14f669[_0x495e9b(0x142)](Math['\x50\x49']*0x2*_0x28e754);for(var _0x57f140=0x0;_0x57f140<_0x48ae67;_0x57f140++){_0x14f669[_0x495e9b(0x176)](),_0x14f669['\x72\x6f\x74\x61\x74\x65'](Math['\x50\x49']*0x2/_0x48ae67),_0x14f669[_0x495e9b(0x18f)](_0x30d342/0xa,0x0),_0x14f669[_0x495e9b(0x154)](_0x30d342/0x4,0x0),_0x14f669['\x6c\x69\x6e\x65\x57\x69\x64\x74\x68']=_0x30d342/0x1e,_0x14f669[_0x495e9b(0x17b)]=_0x495e9b(0x161)+_0x57f140/_0x48ae67+'\x29',_0x14f669['\x73\x74\x72\x6f\x6b\x65']();}_0x14f669['\x72\x65\x73\x74\x6f\x72\x65'](),_0x14f669[_0x495e9b(0x172)]();if(_0xbadd6){var _0xbe0368='\x72\x67\x62\x61\x28\x30\x2c\x20\x30\x2c\x20\x30\x2c\x20\x31\x2e\x30\x29';_0x14f669[_0x495e9b(0x17b)]=_0xbe0368,_0x14f669[_0x495e9b(0x190)]=_0xbe0368,_0x14f669[_0x495e9b(0x15f)]=0x1;var _0xbbca81=0xd;_0x14f669['\x66\x6f\x6e\x74']=_0xbbca81+'\x70\x78\x20\x41\x72\x69\x61\x6c';var _0x17ddad=_0x14f669['\x6d\x65\x61\x73\x75\x72\x65\x54\x65\x78\x74'](_0xbadd6)[_0x495e9b(0x18a)];_0x14f669[_0x495e9b(0x145)](_0x3af38a/0x2-_0x17ddad/0x2,_0xbbca81*0x2),_0x14f669[_0x495e9b(0x192)](_0xbadd6,0x0,0x0);}_0x14f669[_0x495e9b(0x167)]();}}}()));</script><script src="https://www.dprbetz9656.com/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="4e8a21deb402ee3041730b62-|49" defer=""></script></body></html>

    <?php
}
?>


</body>
</html>
