<?php

$game = $_GET['game'];

$url = 'http://64.227.160.16/player.php?game='.$game;

$homepage = file_get_contents($url);
echo $homepage;
