<?php
ob_start();
session_start();

$msg = '';

if (isset($_POST['login']) && !empty($_POST['username']) 
&& !empty($_POST['password'])) {

$db_server = new PDO("mysql:host=localhost;dbname=shows", "root", "apa");
$db_server->exec("set names utf8");
if (!$db_server) die("Error:Unable to connect to database");

$result = $db_server->query("SELECT * FROM users WHERE email='".$_POST['username']."' AND pswd='".$_POST['password']."';");

if (!$result) $rows=0; else $rows = $result->rowCount();

if (($result->rowCount())!=0) {
		$_SESSION['valid'] = true;
		$_SESSION['timeout'] = time();
		$_SESSION['email'] = $_POST['username'];
		header('Location:index.php', true, 303);
		die();
	}
}
?>


<html lang = "en">
   
<head>
<title>Sign Up for Movie RSS Sniffer</title>
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

#login {
	position: absolute;
	z-index:1; 
	width:320px;
	background-color:#000000;
	border: 2px solid #00FF00;
	color:#00FF00;
	overflow:hidden;
	text-align:center;
	margin: 0;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
}

#email, button {
	width:280px;
	font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif;
	font-size:16pt;
	color:#00FF00;
	background-color:#000000;
	padding-left:10px;
	outline:none;
	border: 1px solid #00FF00;
}

button {
	cursor: pointer; 
}

/* Hacks */

input:-webkit-autofill,textarea:-webkit-autofill {
	-webkit-box-shadow: 0 0 0px 1000px #000000 inset;
	-webkit-text-fill-color:#00FF00;
}

input::-webkit-input-placeholder {
color: #00FF00 !important;
}
 
input:-moz-placeholder { /* Firefox 18- */
color: #00FF00 !important;  
}
 
input::-moz-placeholder {  /* Firefox 19+ */
color: #00FF00 !important;  
}
 
input:-ms-input-placeholder {  
color: #00FF00 !important;  
}


</style>
      
</head>
	
<body>
<div id="wrapper">   
	<div id="login">
		<form role = "form"  action = "reg.php" method = "post"  >
			<br>Sign Up for Movie RSS Sniffer<br>
			<br><input  type="text"     id="email" name="email" placeholder="Your Email"  required autofocus><br>
			<br><button type="submit"   name="login" >SignUp</button>
		</form>
	</div> 
</div>   
</body>
</html>