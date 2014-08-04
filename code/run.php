<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

require_once dirname(__FILE__) . "/../lib/steam-condenser.php";

//Get players from server
$server = new GoldSrcServer('173.199.67.130', 27017);
$server->initialize();
$players = $server->getPlayers();

//Get data from forum db

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
	$Fraction = getFraction($Name);
	//echo $DayzName . "\t". $Name . "\t". $SteamId . "\t" . $Fraction ."\n";
	$html .= '<tr><td>'.$DayzName.'</td><td>'.$Name.'</td><td>'.$SteamId.'</td><td>'.$Fraction.'</td></tr>';
}

$html .= '
</tbody></table>

</body>
</html>';

//file_put_contents('index.html', $html);
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