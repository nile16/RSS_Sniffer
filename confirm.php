<html lang = "en">
   
<head>
<title>Confirmation</title>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0,  minimum-scale=1.0"> 
<style>
#wrapper {
	position: absolute;
	z-index:0;
	top:0; 
	bottom:0; 
	right:0; 
	left:0; 
	overflow:hidden; 
	background-color:#000000;
}	
#main {
	position: absolute;
	z-index:1; 
	font-family: Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace;
	font-size: 18px;
	font-style: normal;
	font-variant: normal;
	font-weight: 400;
	line-height: 30px;	
	text-align:center;
	color:#00FF00;
	background-color:#000000;
	margin: 0;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
}
a:link    {color:#00FF00;text-decoration:none;}  /* unvisited link */
a:visited {color:#00FF00;text-decoration:none;}  /* visited link */
a:hover   {color:#00FF00;text-decoration:none;}  /* mouse over link */
a:active  {color:#00FF00;text-decoration:none;}  /* selected link */ 
</style>
</head>

<body>
	<div id="wrapper">
		<div id="main">
			<?php
			$db_server = new PDO("mysql:host=localhost;dbname=shows", "root", "apa");
			$db_server->exec("set names utf8");
			if (!$db_server) echo "Error:Unable to connect to database"; 
			$result = $db_server->query("SELECT * FROM users WHERE confirm='".$_GET['confirm']."';");
			if ($result->rowCount()!=0) { 
				$db_server->query("UPDATE users SET confirm='0' WHERE confirm='".$_GET['confirm']."';");
				echo "Account activated.<br><br><a href='http://nile16.vabynas.se/login.php'>Click here to login.</a>";}
			else
				echo "Invalid or expired link."
			?>
		</div>
	</div>   
</body>

</html>

