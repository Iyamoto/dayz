<?php

error_reporting(E_ALL & ~E_USER_NOTICE | E_STRICT);

//Read config
require_once dirname(__FILE__) . "/conf.php";

//Connect to forum db
$con = mysql_connect('localhost', $db, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
        mysql_select_db($db, $con);
        $result = mysql_query("set names 'utf8'");
}
		
//Form table
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
	        <li ><a href="run.php">Players</a></li>
	        <li><a href="list.php">With steamid</a></li>
	     	<li class="active"><a href="all.php">All users</a></li>
			<li><a href="http://prime.gunlinux.org">blacklist</a></li>
			<li><a href="http://steam.gunlinux.org">steam calc</a></li>
	      </ul>
	     
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	<div class="container">
<table class="table table-bordered">
<thead>
	<tr><th>ForumName</th><th>Realname</th><th>SteamID</th></tr>
</thead>
<tbody>
';
$result = mysql_query("SELECT t1.id_member as id_member , t1.member_name as member_name ,t1.real_name  as real_name , t2.value as value FROM smf_members t1 left join smf_themes t2 on t1.id_member = t2.id_member and t2.variable='cust_steam-'");

while ($row = mysql_fetch_assoc($result)) {
echo '<tr>';
	
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$row["id_member"].'">'.$row["member_name"].'</a></td>';
	echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$row["id_member"].'">'.$row["real_name"].'</a></td>';
	echo '<td><a href="http://steamcommunity.com/profiles/'.$row["value"].'">'.$row["value"].'</a></td>';
echo '</tr>';
    };
echo  '
</tbody></table>
</div>
</body>
</html>';

mysql_close($con);
