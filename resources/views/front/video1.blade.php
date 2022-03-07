<?php 
use App\ManageTv;
$managetv = ManageTv::latest()->first();
?>
{{$managetv->channel1}}