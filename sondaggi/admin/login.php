<?php
define("IN_SCRIPT","1");
error_reporting(0);
session_start();
include("../include/SiteManager.class.php");
$website = new SiteManager();
$website->LoadSettings();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>PHP Survey Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<script src="js/jquery-1.10.2.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<script type="text/javascript" src="js/login.js"></script>
	</head>  
<body>

<div class="page-header">
  <h1>PHP Survey</h1>
  <h3>Please log in to continue to the administration panel</h3>
</div>
<form id="main" style="margin-top:0px" action="login_action.php" method="post">
  <div class="group">
    <input name="username" required type="text"><span class="highlight"></span><span class="bar"></span>
    <label><?php echo $website->texts["username"];?></label>
  </div>
  <div class="group">
    <input name="password" required type="password"><span class="highlight"></span><span class="bar"></span>
    <label><?php echo $website->texts["password"];?></label>
  </div>
  <input type="submit" class="button buttonBlue" value="<?php echo $website->texts["login"];?>"/>
  
</form>
<footer>
  <p></p>
</footer>


  </body>
</html>