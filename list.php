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

		
//Form table

showHeader();

echo '
	<div class="container">
		<table class="table table-bordered">
			<thead>
				<tr><th>ForumName</th><th>Realname</th><th>SteamID</th><th>Blacklist</th></tr>
			</thead>
	<tbody>
';
$result = mysql_query("SELECT t1.id_member as id_member , t1.member_name as member_name ,t1.real_name  as real_name , t2.value as value FROM smf_members t1 inner join  smf_themes t2 on t1.id_member = t2.id_member and t2.variable='cust_steam-'");

while ($row = mysql_fetch_assoc($result)) {
	echo '<tr>';
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$row["id_member"].'">'.$row["member_name"].'</a></td>';
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$row["id_member"].'">'.$row["real_name"].'</a></td>';
	
	$SteamId = $row["value"];
	$SteamText = $SteamId;
	
	if (strlen($SteamId)==8) 
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/id/'.$SteamId.'">'.$SteamId.'</a>';
	
	if (strlen($SteamId)==17) 
		$SteamText = '<a target="_blank" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
	
	echo '<td>'.$SteamText.'</td>';
	
	if (array_key_exists($SteamId, $blacklist)) 
		echo '<a target="_blank" href="http://prime.gunlinux.org/user/'.$blacklist[$SteamId].'">'.$blacklist[$SteamId].'</a>';;
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
