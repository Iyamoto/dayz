<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

require_once dirname(__FILE__) . "/../lib/steam-condenser.php";

function getFixture($fileName) {
    return file_get_contents(dirname(__FILE__) . "/fixtures/$fileName");
}

$server = new GoldSrcServer('173.199.67.130', 27017);
$server->initialize();
var_dump($server->getPlayers());


//http://api.steampowered.com/ISteamApps/GetServersAtAddress/v0001?addr=173.199.67.130
//http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?addr=173.199.67.130
//https://community.bistudio.com/wiki/BattlEye