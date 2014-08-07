<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Read config
require_once dirname(__FILE__) . "/conf.php";
require_once dirname(__FILE__) . "/func.php";

//Get players from dayz server
require_once dirname(__FILE__) . "/lib/steam-condenser.php";
$server = new GoldSrcServer($ip, $port);
$server->initialize();
$players = $server->getPlayers();

//Connect to forum db
$con = mysql_connect('localhost', $db, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
        mysql_select_db($db, $con);
        $result = mysql_query("set names 'utf8'");
}
		
//Form table
$html = '<!DOCTYPE html>
<html>
<head>
    <title>Oplot White List</title>
    <meta charset="utf-8">
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="static/css/style.css">    
</head>
<body>
	<nav class="navbar navbar-default" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#">Brand</a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
	        <li class="active"><a href="run.php">Players</a></li>
	        <li><a href="list.php">With steamid</a></li>
	     	<li><a href="all.php">All users</a></li>
			<li><a href="http://prime.gunlinux.org">blacklist</a></li>
			<li><a href="http://steam.gunlinux.org">steam calc</a></li>
	      </ul>
	     
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	<div class="container">
<table class="table table-bordered">
<thead>
	<tr><th>DayzName</th><th>ForumName</th><th>Group</th><th>SteamID</th><th>BEGUID</th></tr>
</thead>
<tbody>
';

foreach($players as $player){
	$DayzName = $player->getName();
	$User = getForumUser($DayzName);
	$ForumId = $User["id_member"];
	$Name = getForumName($User);
	if ($Name!='NA') $NameText = '<a target="_blank" href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$ForumId.'">'.$Name.'</a>';
	else $NameText = $Name;
	$SteamId = getSteamId($User);
	$BEGUID = 'NA';
	$SteamText = $SteamId;
	
	if (strlen($SteamId)==8) {
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/id/'.$SteamId.'">'.$SteamId.'</a>';
	}
	
	if (strlen($SteamId)==17) {
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
		if ($calcbeguid==true) $BEGUID = getBEGUID($SteamId);
	}
	
	$Fraction = getFraction($User);
	
	$html .= '<tr><td>'.$DayzName.'</td><td>'.$NameText.'</td><td>'.$Fraction.'</td><td>'.$SteamText.'</td><td>'.$BEGUID. '</td></tr>';
}

$html .= '
</tbody></table>
</div>
</body>
</html>';

mysql_close($con);
echo $html;

?>