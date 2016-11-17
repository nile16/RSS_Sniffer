<?php
ob_start();
session_start();
if ($_SESSION['valid']!=true) die("Not logged in");

$db_server = new PDO("mysql:host=localhost;dbname=shows", "root", "apa");
$db_server->exec("set names utf8");
if (!$db_server) die("Error:Unable to connect to database");

$result = $db_server->query("SELECT * FROM users WHERE email='".$_SESSION['email']."';");

if (!$result) $rows=0; else $rows = $result->rowCount();

for ($j = 0 ; $j < $rows ; ++$j) {
	$row = $result->fetch(PDO::FETCH_ASSOC);
	echo $row['list'];
}

?>
