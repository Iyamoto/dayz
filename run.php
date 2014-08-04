<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Get players from dayz server
require_once dirname(__FILE__) . "/lib/steam-condenser.php";
$server = new GoldSrcServer('173.199.67.130', 27017);
$server->initialize();
$players = $server->getPlayers();

//Get data from forum db
require_once dirname(__FILE__) . "/conf.php";
$con = mysql_connect('localhost', $db, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
        mysql_select_db($db, $con);
        $result = mysql_query("set names 'utf8'");
		$name = 'diana';
        $result = mysql_query("SELECT * FROM smf_members WHERE member_name LIKE '%" . $name . "%'");
		//SELECT * FROM smf_members WHERE member_name LIKE '%diana%'
		var_dump($result);
        $cnum=0;
        while ($row = mysql_fetch_assoc($result)) {
                $rows[] = $row;
                $cnum++;
        }
		var_dump($rows);
        mysql_free_result($result);
        mysql_close($con);
}
		
//Form table
$html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>blabla</title>
</head>
<body>
<table border="1" cellpadding="5" cellspacing="5" bordercolor="black" frame="box" rules="all"><tbody>
<tr><th>DayzName</th><th>ForumName</th><th>SteamID</th><th>Fraction</th></tr>
';

foreach($players as $player){
	//var_dump($player);
	$DayzName = $player->getName();
	$Name = getForumName($DayzName);
	$SteamId = getSteamId($Name);
	$Fraction = getFraction($DayzName);
	//echo $DayzName . "\t". $Name . "\t". $SteamId . "\t" . $Fraction ."\n";
	$html .= '<tr><td>'.$DayzName.'</td><td>'.$Name.'</td><td>'.$SteamId.'</td><td>'.$Fraction.'</td></tr>';
}

$html .= '
</tbody></table>
</body>
</html>';

echo $html;

//Helpers

function getForumName($name){
	$name = preg_replace('|\[[^\]]+\]|','',$name);
	$name = strtolower($name);
	$name = trim($name);
	return $name;
}

function getSteamId($name){
	$id = '1111';
	return $id;
}

function getFraction($name){
	$f = '1';
	return $f;
}

//http://api.steampowered.com/ISteamApps/GetServersAtAddress/v0001?addr=173.199.67.130
//http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?addr=173.199.67.130
//https://community.bistudio.com/wiki/BattlEye