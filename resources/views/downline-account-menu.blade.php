

<?php    
    $url=explode("/",$_SERVER['REQUEST_URI']);
$page1=$url[1];
$page=explode(".php",$page1);
$page2=$page[0];    
    ?>

<ul class="left-menu white-bg">
	<li class="darkblue-bg text-color-white head"> Position </li>	
    
	<li <?php if($page2=='downline-myaccount-summary') { ?> class="active" <?php } ?>>
        <a href="downline-myaccount-summary.php" class="text-color-black"> Account Summary </a>
    </li>
    
	<li class="darkblue-bg text-color-white head"> Performance </li>
    
	<li <?php if($page2=='downline-myaccount-history') { ?> class="active" <?php } ?>>
        <a href="downline-myaccount-history.php" class="text-color-black"> Betting History </a>
    </li>
    
	<li <?php if($page2=='downline-myaccount-profitloss') { ?> class="active" <?php } ?>>
        <a href="downline-myaccount-profitloss.php" class="text-color-black"> Betting Profit & Loss </a>
    </li>
    
    <li <?php if($page2=='downline-myaccount-transaction') { ?> class="active" <?php } ?>>
        <a href="downline-myaccount-transaction.php" class="text-color-black"> Transaction History </a>
    </li>
    
	<li <?php if($page2=='downline-myaccount-activity') { ?> class="active" <?php } ?>>
        <a href="#" class="text-color-black"> Activity Log </a>
    </li>
    
</ul>