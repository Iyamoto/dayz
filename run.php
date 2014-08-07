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

showHeader();

$html = '
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