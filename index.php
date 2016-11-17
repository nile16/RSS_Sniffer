<?php
ob_start();
session_start();

if 	($_SESSION['valid']!=true) {
		header('Location:login.php', true, 303);
		die();
}
?>

<html lang = "en">
   
<head>
<title>Movie RSS Sniffer</title>
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
	width:360px;
	height:512px;
	font-family: Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace;
	font-size: 18px;
	font-style: normal;
	font-variant: normal;
	font-weight: 400;
	line-height: 30px;	
	color:#00FF00;
	background-color:#000000;
	margin: 0;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
}

#popUp {
	position:absolute;
	z-index:5; 
	width:260px;
	padding:10px;
	color:#00FF00;
	background-color:#000000;
	border: 2px solid #00FF00;
	margin: 0;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
	display:none;
}

#dimmer {
	position:absolute;
	z-index:4; 
	width:100%;
	height:100%;
	display:none;
	opacity:0.6;
	background-color:#000000;
}

#head {
	position: absolute;
	top:0px;
	z-index:2;
	width:100%;
	height:32px;
}

#head img {
	height:100%;
	float:left;
}

#headtext {
	display:flex;
	height:32;
	justify-content:center;
	align-items:center;
}

#text {
	position: absolute;
	z-index:2; 
	top:32px;
	bottom:32px;
	width:100%;
	border: 2px solid #00FF00;
	overflow:hidden;
}

#textArea {
	position: absolute;
	width:100%;
	height:100%; 
	z-index:3; 
	font-family: Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace;
	font-size: 14px;
	font-style: normal;
	font-variant: normal;
	font-weight: 400;
	line-height: 20px;	
	color:#00FF00;
	background-color:#000000;
	overflow:hidden;
	resize:none;
	border:none;
	outline:none;
	overflow-y: auto;
}

#button1, #button2 {
	position: absolute;
	z-index:2;
	width:50%;
	height:32px;
	bottom:0px;
	display:flex;
	justify-content:center;
	align-items:center;
}

a {
	cursor: pointer; 
}

#button1 {
	left:0px;
}

#button2 {
	right:0px;
}

#newpswd {
	width:100%;
	font-family: Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace;
	font-size: 18px;
	font-style: normal;
	font-variant: normal;
	font-weight: 400;
	line-height: 30px;	
	color:#00FF00;
	background-color:#000000;
	padding-left:10px;
	outline:none;
	border: 1px solid #00FF00;
}

@media (orientation:portrait) and (max-height:512px),(orientation:portrait) and (max-width:360px),(orientation:landscape) and (max-height:512px) {
	#main {
		width:100%;
		height:100%;
	}
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

<script>


function getList(){
	var wordlist = new XMLHttpRequest();
	wordlist.open("GET", "read.php", true);
	wordlist.onreadystatechange = function(){
	if (wordlist.readyState==4 && wordlist.status==200) {
		document.getElementById('textArea').innerHTML=wordlist.responseText;
		}
	}
	wordlist.send(null);
}

function saveList(){
	showPopUp("Saving list");
	var xhr = new XMLHttpRequest();
	var params = 'list='+encodeURIComponent(document.getElementById('textArea').value);
	xhr.open("POST", 'write.php', true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Content-length", params.length);
	xhr.setRequestHeader("Connection", "close");
	xhr.onreadystatechange = function(){
    if(xhr.readyState == 4 && xhr.status == 200) {
		showPopUp("List saved<br><br><span style='float:right;'><a onclick='closePopUp()'>CLOSE</a></span>");
		}
	}
	xhr.send(params);
}

function showPopUp(string){
	document.getElementById('popUp').innerHTML=string;
	document.getElementById('dimmer').style.display='block';
	document.getElementById('popUp').style.display='block';
}

function closePopUp(){
	document.getElementById('popUp').style.display='none';
	document.getElementById('dimmer').style.display='none';
}

function openMenu(){
	showPopUp("<a onclick='logOut()'>Logout</a><br><a onclick='changePswd()'>Change Password</a><br><a onclick='deleteAccount()'>Delete Account</a><br><br><br><span style='float:right;'><a onclick='closePopUp()'>CANCEL</a></span>");
}

function logOut(){
	showPopUp("Logging out");
	window.location.assign('logout.php')
}

function changePswd(){
	showPopUp("<input type='text' id='newpswd' placeholder='New Password' autofocus><br><br><a onclick='sendPswd()'>CHANGE</a><span style='float:right;'><a onclick='closePopUp()'>CANCEL</a></span>");
}

function sendPswd(){
	var xhr = new XMLHttpRequest();
	var params = 'pswd='+encodeURIComponent(document.getElementById('newpswd').value);
	showPopUp("Setting Password");
	xhr.open("POST", 'setpswd.php', true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Content-length", params.length);
	xhr.setRequestHeader("Connection", "close");
	xhr.onreadystatechange = function(){
    if(xhr.readyState == 4 && xhr.status == 200) {
		showPopUp("Password saved<br><br><span style='float:right;'><a onclick='closePopUp()'>CLOSE</a></span>");
		}
	}
	xhr.send(params);
}

function deleteAccount(){
	showPopUp("Do you want to delete your account?<br><br><a onclick='sendDelete()'>YES</a><span style='float:right;'><a onclick='closePopUp()'>NO</a></span>");
}

function sendDelete(){
	var xhr = new XMLHttpRequest();
	showPopUp("Deleting Account");
	xhr.open("POST", 'delete.php', true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Connection", "close");
	xhr.onreadystatechange = function(){
    if(xhr.readyState == 4 && xhr.status == 200) {
		logOut();
		}
	}
	xhr.send();
}
	


</script>
	
<body>
	<div id="wrapper">
		<div id="main">
			<div id="dimmer"></div>
			<div id="popUp"></div>
			<div id="head" ><div id="headtext">MOVIE RSS SNIFFER</div></div>
			<div id="button1" ><a onclick="openMenu()">MENU</a></div>
			<div id="button2" ><a onclick="saveList()" >SAVE</a></div>
			<div id="text" >
				<textarea id="textArea" placeholder="Write keywords here. Use one line for each movie. If two or more keywords are used on the same line all those words has to be in the movie title for a match to occur."></textarea>
			</div>
		</div>
	</div>   
</body>

<script>

window.onload = function() {
  getList();
};

</script>

</html>