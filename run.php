<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Read config
require_once "conf.php";
require_once "func.php";

showHeader();

try {
	//Get players from dayz server
	require_once dirname(__FILE__) . "/lib/steam-condenser.php";
	$server = new GoldSrcServer($ip, $port);
	$server->initialize();
	$players = $server->getPlayers();
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
	echo '</body></html>';
	exit;
}

//Get blacklist db

if ($db1 = new SQLite3("base.db")) { 
    $results = $db1->query('select id, steamid, status from users');
    while ($row = $results->fetchArray()) {
		$blacklist[$row["steamid"]] = $row["id"];
		$blacklist[$row["steamid"]."s"]= $row["status"];
	}
} else {
    die('black list not found');
}

//Connect to forum db
$con = mysql_connect('localhost', $db, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
        mysql_select_db($db, $con);
        $result = mysql_query("set names 'utf8'");
}
		
//Form table



$html = '
<div class="container">
	<table class="table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>DayzName</th>
			<th>ForumName</th>
			<th>Group</th>
			<th>SteamID</th>
';
if ($calcbeguid) {
				$html .= '<th>BEGUID</th>';
}
$html .='
</thead>
<tbody>
';

$BadSteam = 0;
$Blacklisted = 0;

//$players = array_reverse($players);

foreach($players as $player){
	$DayzName = $player->getName();
	$User = getForumUser($DayzName);
	$ForumId = $User["id_member"];
	$Name = getForumName($User);
	if ($Name!='NA') $NameText = '<a title="Открыть профиль на форуме" target="_blank" href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$ForumId.'">'.$Name.'</a>';
	else $NameText = $Name;
	$SteamId = getSteamId($User);
	$BEGUID = 'NA';
	$SteamText = $SteamId;
	
	if (strlen($SteamId)==8) {
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/id/'.$SteamId.'">'.$SteamId.'</a>';
	}
	
	if (strlen($SteamId)==17) {
		$SteamText = '<a target="_blank" title="Открыть steam профиль" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
		if ($calcbeguid==true) $BEGUID = getBEGUID($SteamId);
	} else 
		$BadSteam++;
	
	$Fraction = getFraction($User);
	if (array_key_exists($SteamId, $blacklist)) {
		$Blacklisted++;
		$BeguidText ='';
		if ($calcbeguid) {
			$BeguidText = $BEGUID;
		}
	$BeguidText.=' <a target="_blank" title="Посмотреть причину нахождения в черном списке" href="http://prime.gunlinux.org/user/'.$blacklist[$SteamId].'">';		
	if ($blacklist[$steamId."s"]==0) {
		$BeguidText .= "На заметке";
	}
	if ($blacklist[$steamId."s"]==1) {
		$BeguidText .= "Предупреждение";
	}
	if ($blacklist[$steamId."s"]==2) {
		$BeguidText .= "Банить";
	}
	if ($blacklist[$steamId."s"]==3) {
		$BeguidText .= "Забанен";
	}
		$html .= '<tr class="inblacklist inblacklist'.$blacklist[$SteamId."s"].'">';
	} else {
		$html .= '<tr>';
		$BeguidText = $BEGUID;
	}
	$html .= '<td>
	<a href="http://prime.gunlinux.org/user/add?steamid='.$SteamId.'&aliases='.$Name.'" title="В черный список"><span class="glyphicon glyphicon-exclamation-sign"></span></a></td>'.'<td>'.$DayzName.'</td><td>'.$NameText.'</td><td>'.$Fraction.'</td><td>'.$SteamText.'</td><td>'.$BeguidText.'</td></tr>';
}

$html .= '
</tbody></table>
</div>
</body>
</html>';

$Counter = getCounter($players, $BadSteam, $Blacklisted);
$html = $Counter .' '. $html;

mysql_close($con);
echo $html;

?>
