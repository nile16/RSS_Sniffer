<html lang = "en">
   
<head>
<title>Registration Result</title>
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
			if (!$db_server) 
				echo "Error:Unable to connect to database.\n";
			else {
				$result = $db_server->query("SELECT * FROM users WHERE email='".$_POST['email']."';");
				
				if ($result->rowCount()!=0) 
					echo "User exist already.\n";
				else {
					$random = bin2hex(openssl_random_pseudo_bytes(10));
					$rpassword = bin2hex(openssl_random_pseudo_bytes(2));
					
					$result = $db_server->query("INSERT INTO users (email,pswd,list,confirm,regtime) VALUES ('".$_POST['email']."','".$rpassword."','','".$random."',".time().");");
					
					$message = "
					You have received this email because this email address was used during registration for Movie RSS Sniffer.<br>
					Your password is: ".$rpassword."<br>
					<a href='http://nile16.vabynas.se/confirm.php?confirm=".$random."'>Click here to activate your account</a> The link is valid for one hour.<br><br>
					If you did not register, please disregard this email. You do not need to unsubscribe or take any further action.
					";
					
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <nils.leandersson@gmail.com>' . "\r\n";
					
					mail($_POST['email'],"Sign up for Movie RSS Sniffer",$message,$headers);
					echo "A Confirmation Email has been sent.\n";
				}
			}
			?>
		</div>
	</div>   
</body>

</html>

