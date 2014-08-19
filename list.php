<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Read config
require_once dirname(__FILE__) . "/conf.php";
require_once dirname(__FILE__) . "/func.php";

//Connect to forum db
$con = mysql_connect('localhost', $db, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
        mysql_select_db($db, $con);
        $result = mysql_query("set names 'utf8'");
}

//Get blacklist db

if ($db = new SQLite3("base.db")) { 
    $results = $db->query('select id, steamid, status from users');
    while ($row = $results->fetchArray()) {
		$blacklist[$row["steamid"]] = $row["id"];
	}
} else {
    die('black list not found');
}

//Get players from dayz server
require_once dirname(__FILE__) . "/lib/steam-condenser.php";
$server = new GoldSrcServer($ip, $port);
$server->initialize();
$players = $server->getPlayers();
		
//Form table

showHeader();

echo '
	<div class="container">
		<table class="table table-bordered">
			<thead>
				<tr><th>#</th><th>ForumName</th><th>Realname</th><th>SteamID</th><th>Blacklist</th></tr>
			</thead>
	<tbody>
';
$result = mysql_query("SELECT t1.id_member as id_member , t1.member_name as member_name ,t1.real_name  as real_name , t2.value as value FROM smf_members t1 inner join  smf_themes t2 on t1.id_member = t2.id_member and t2.variable='cust_steam-' ORDER by t1.id_member DESC");

while ($row = mysql_fetch_assoc($result)) {
	if (isOnline($players, $row["real_name"]))
		echo '<tr class="online">';
	else 
		echo '<tr>';
	echo '<td><a href="http://prime.gunlinux.org/user/add?steamid='.$row["value"].'&aliases='.$row["real_name"].'" title="В черный список"><span class="glyphicon glyphicon-exclamation-sign"></span></a></td>';
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$row["id_member"].'">'.$row["member_name"].'</a></td>';
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;area=showposts;u='.$row["id_member"].'">'.$row["real_name"].'</a></td>';
	
	$SteamId = $row["value"];
	$SteamText = $SteamId;
	
	if (strlen($SteamId)==8) 
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/id/'.$SteamId.'">'.$SteamId.'</a>';
	
	if (strlen($SteamId)==17) 
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
	
	echo '<td>'.$SteamText.'</td>';
	
	if (array_key_exists($SteamId, $blacklist)) 
		echo '<td class="inblacklist"><a target="_blank" href="http://prime.gunlinux.org/user/'.$blacklist[$SteamId].'">'.$blacklist[$SteamId].'</a></td>';
	else
		echo '<td>NA</td>';
	
	echo '</tr>';
    };
echo  '
	</tbody>
	</table>
</div>
</body>
</html>';
mysql_close($con);
?>
