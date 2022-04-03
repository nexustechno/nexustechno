<?php

$iframe = $_GET['iframe'];
$game = $_GET['game'];

$baseLink = 'http://64.227.160.16/player.php?game='.$game;


?>
<html>
<head>
    <title>LIVE TV</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body style="margin: 0;width: 100%;height: 100%;display: block;position: relative;margin: 0 auto;text-align: center;background: black;">
    <iframe id="dnn_ctr560_ProductDetails_ctl02_iframe1" src="./redirect.php?url=<?php echo $baseLink; ?>" frameborder="0" style="padding: 0;"></iframe>
</body>
<script type="text/javascript">
    //$(document).ready(function () {
    //    function myFunction() {
    //        document.getElementById("dnn_ctr560_ProductDetails_ctl02_iframe1").src = "<?php //echo $baseLink; ?>//";
    //    }
    //    myFunction();
    //});
</script>
</html>
