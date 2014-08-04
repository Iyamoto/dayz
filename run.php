<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Get players from dayz server
require_once dirname(__FILE__) . "/lib/steam-condenser.php";
$server = new GoldSrcServer('173.199.67.130', 27017);
$server->initialize();
$players = $server->getPlayers();

//Connect to forum db
require_once dirname(__FILE__) . "/conf.php";
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
       
    <script src="static/js/jquery.js"></script>
    <script src="static/js/bootstrap.min.js"></script>

</head>
<body>
	<div class="container">
<table class="table table-bordered">
<thead>
	<tr><th>DayzName</th><th>ForumName</th><th>SteamID</th><th>Group</th></tr>

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
	if (strlen($SteamId)==17) $SteamText = '<a target="_blank" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
	else $SteamText = $SteamId;
	$Fraction = getFraction($User);
	$html .= '<tr><td>'.$DayzName.'</td><td>'.$NameText.'</td><td>'.$SteamText.'</td><td>'.$Fraction.'</td></tr>';
}

$html .= '
</tbody></table>
</body>
	</div>

</html>';

echo $html;
mysql_close($con);

//Helpers

function getForumUser($name){
	$name = preg_replace('|\[[^\]]+\]|','',$name);
	$name = strtolower($name);
	$name = trim($name);
	if (strlen($name)>0){
		//SELECT * FROM smf_members WHERE member_name LIKE '%diana%'
		$result = mysql_query("SELECT * FROM smf_members WHERE member_name LIKE '%" . $name . "%'");
		if($result){
			$cnum=0;
			while ($row = mysql_fetch_assoc($result)) {
				$rows[] = $row;
				$cnum++;
			}
			mysql_free_result($result);
			if ($cnum==0) $rows[0] = NULL;
			//var_dump($rows);
		} else $rows[0] = NULL;
	}
	return $rows[0];
}

function getForumName($user){
	if ($user!=NULL)
		$name = $user["member_name"];
	else $name = 'NA';
	return $name;
}

function getSteamId($user){
	$SteamId = 'NA';
	if ($user!=NULL){
		$id = $user["id_member"];
		//SELECT * FROM smf_themes WHERE id_member = 'id' AND variable LIKE '%cust_steam_%'
		$result = mysql_query("SELECT * FROM smf_themes WHERE id_member = '" . $id . "' AND variable LIKE '%cust_steam_%'");
		if($result){
			$cnum=0;
			while ($row = mysql_fetch_assoc($result)) {
                $rows[] = $row;
                $cnum++;
			}
			mysql_free_result($result);
			if ($cnum>0) {
				$SteamId = $rows[0]["value"];
			}
		} 
	} 
	return $SteamId;
}

function getFraction($user){
	$f = 'NA';
	if ($user!=NULL){
		$id = $user["id_group"];
		//SELECT * FROM smf_membergroups WHERE id_group = 'id'
		$result = mysql_query("SELECT * FROM smf_membergroups WHERE id_group = '" . $id . "'");
		if($result){
			$cnum=0;
			while ($row = mysql_fetch_assoc($result)) {
                $rows[] = $row;
                $cnum++;
			}
			mysql_free_result($result);
			if ($cnum>0) {
				$f = $rows[0]["group_name"];
			}
		} 
	}
	return $f;
}

//http://api.steampowered.com/ISteamApps/GetServersAtAddress/v0001?addr=173.199.67.130
//http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?addr=173.199.67.130
//https://community.bistudio.com/wiki/BattlEye