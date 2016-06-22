<?php
//mysql://bdd58b875d80b0:85d85ba1@eu-cdbr-west-01.cleardb.com/heroku_b43fd19e2d8e228?reconnect=true
if(getenv("CLEARDB_DATABASE_URL")!=""){
	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

	$server = $url["host"];
	$username = $url["user"];
	$password = $url["pass"];
	$db = substr($url["path"], 1);

	/*echo $server."<br/>\n";
	echo $username."<br/>\n";
	echo $username."<br/>\n";
	echo $db."<br/>\n";*/
}else{
	$server = "127.0.0.1";
	$username = "testm2x";
	$password = "testm2x";
	$db = "testm2x";
}

$connection = new mysqli($server, $username, $password, $db);
?>