﻿<?php
//Helpers

function getCounter($players, $bad, $Blacklisted, $max=40){
	if ($bad==0) $styleBad = 'players';
	else $styleBad = 'bad';
	if ($Blacklisted==0) $styleBL = 'players';
	else $styleBL = 'bad';
	$html = '<div class="container counter-wrapper">';
	$html .=  '<div class="well text-center">Online: <span class="players">'.sizeof($players). '</span> / <span class="max">'.$max.',</span> Bad SteamID: <span class="'.$styleBad.'">'.$bad.'</span>, Wanted: <span class="'.$styleBL.'">'.$Blacklisted;
	$html .=  "</span></div></div>";
	return $html;
}

function isOnline($players, $ForumName){
	foreach($players as $player){
		$name = $player->getName();
		$name = clearName($name);
		$ForumName = clearName($ForumName);
		if ($ForumName == $name) {
			return true;
		}
	}
	return false;
}

function showHeader(){

echo '<!DOCTYPE html>
<html>
<head>
    <title>Oplot White List</title>
    <meta charset="utf-8">
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="static/css/style.css">    
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
	      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#">OPLOT</a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
	        <li><a href="run.php">Online</a></li>
	        <li><a href="list.php">SteamID</a></li>
	     	<li><a href="all.php">Forum Users</a></li>
			<li><a href="clones.php">Clones</a></li>
			<li><a href="http://prime.gunlinux.org">BlackList</a></li>
			<li><a href="http://steam.gunlinux.org">Steam Calc</a></li>
			<li><a href="http://forum.oplotdayz.ru/short/">Short Urls</a></li>
			<li><a href="http://forum.oplotdayz.ru/short/code.html">Code</a></li>
	      </ul>
	     
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>';
}	

function clearName($name){
	$name = preg_replace('|\[[^\]]+\]|','',$name);
	$name = preg_replace('|\([^\)]+\)|','',$name);
	$name = preg_replace('|\{[^\}]+\}|','',$name);
	$name = strtolower($name);
	$name = trim($name);
	return $name;
}

function getForumUser($name){
	$name = clearName($name);
	if (strlen($name)>0){
		//SELECT * FROM smf_members WHERE member_name LIKE '%diana%'
		//$result = mysql_query("SELECT * FROM smf_members WHERE member_name LIKE '%" . $name . "%'");
		$result = mysql_query("SELECT * FROM smf_members WHERE real_name LIKE '%" . $name . "%' ORDER BY CHAR_LENGTH(real_name) ASC");
		if($result){
			$cnum=0;
			while ($row = mysql_fetch_assoc($result)) {
				$rows[] = $row;
				$cnum++;
			}
			mysql_free_result($result);
			if ($cnum==0) $rows[0] = NULL;
		} else $rows[0] = NULL;
	} else $rows[0] = NULL;
	return $rows[0];
}

function getForumName($user){
	if ($user!=NULL)
		$name = $user["real_name"];
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

function getBEGUID($id){
	$tmp = 'BE';
    for($i=0;$i<8;$i++){
		$t = gmp_and($id, "0xFF");
        $tmp .= chr(gmp_strval($t));
        $id = gmp_div_q($id, "256");		
	}
    $beguid = md5($tmp);
	return $beguid;
}

function urlid2id($id){
	$url = 'http://steamcommunity.com/id/'.$id.'/?xml=1';
	$html = file_get_contents($url);
	preg_match('|<steamID64>(\d+)<|', $html, $m);
	return $m[1];
}

function id2id($id){
	$steam64 = '0x0110000100000000';
	$t = gmp_mul($id, "2");
	$sum = gmp_add($t, $steam64);
	return gmp_strval($sum);
}

//http://steamcommunity.com/id/25412541/?xml=1
//http://api.steampowered.com/ISteamApps/GetServersAtAddress/v0001?addr=173.199.67.130
//http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?addr=173.199.67.130
//https://community.bistudio.com/wiki/BattlEye

?>
