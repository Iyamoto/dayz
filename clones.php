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
		
//Form table

showHeader();

echo '
	<div class="container">
	<h2>Clones by SteamID</h2>
<table class="table table-bordered">
<thead>
	<tr><th>ForumName</th><th>Realname</th><th>SteamID</th></tr>
</thead>
<tbody>
';
$result = mysql_query("SELECT t1.id_member as id_member , t1.member_name as member_name ,t1.real_name  as real_name , t2.value as value FROM smf_members t1 left join smf_themes t2 on t1.id_member = t2.id_member and t2.variable='cust_steam-' ORDER by t1.id_member DESC");

while ($row = mysql_fetch_assoc($result)) {
	$real_name = clearName($row["real_name"]);
	$member_name = clearName($row["member_name"]);
	$names[$real_name][$row["id_member"]]["real_name"] = $row["real_name"];
	$names[$real_name][$row["id_member"]]["member_name"] = $row["member_name"];
	$members[$member_name][$row["id_member"]]["real_name"] = $row["real_name"];
	$members[$member_name][$row["id_member"]]["member_name"] = $row["member_name"];
	if (strlen($row["value"])==17) {
	$steams[$row["value"]][$row["id_member"]]["real_name"] = $row["real_name"];
	$steams[$row["value"]][$row["id_member"]]["member_name"] = $row["member_name"];
	}
}
mysql_close($con);

foreach($steams as $SteamId=>$data) {
	if(sizeof($data)>1) {
		foreach($data as $id=>$row){
			echo '<tr>';	
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["member_name"].'</a></td>';
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["real_name"].'</a></td>';
	$SteamText = '<a target="_blank" href="http://steamcommunity.com/profiles/'.$SteamId.'">'.$SteamId.'</a>';
			echo '<td>'.$SteamText.'</td>';
			echo '</tr>';
		}
	}
}

echo  '
</tbody></table>
<h2>Clones by Real_Name</h2>
<table class="table table-bordered">
<thead>
	<tr><th>RealName</th><th>MemberName</th></tr>
</thead>
<tbody>
';

foreach($names as $name=>$data){
	if(sizeof($data)>1) {
		foreach($data as $id=>$row){
			echo '<tr>';	
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["real_name"].'</a></td>';
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["member_name"].'</a></td>';
			echo '</tr>';
		}
	}
}

echo  '
</tbody></table>
<h2>Clones by Member_Name</h2>
<table class="table table-bordered">
<thead>
	<tr><th>MemberName</th><th>RealName</th></tr>
</thead>
<tbody>';

foreach($members as $name=>$data){
	if(sizeof($data)>1) {
		foreach($data as $id=>$row){
			echo '<tr>';	
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["member_name"].'</a></td>';
			echo '<td><a href="http://forum.oplotdayz.ru/index.php?action=profile;u='.$id.'">'.$row["real_name"].'</a></td>';
			echo '</tr>';
		}
	}
}

echo  '
</tbody></table>
</div>
</body>
</html>';

?>