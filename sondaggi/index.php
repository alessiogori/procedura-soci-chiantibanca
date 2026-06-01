<?php
// PHP Survey, https://www.netartmedia.net/php-survey
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
define("IN_SCRIPT","1");
error_reporting(0);
session_start();
require("include/SiteManager.class.php");
$website = new SiteManager();
$website->InitData();
$website->LoadSettings();
$website->LoadTemplate();
if(isset($_REQUEST["page"]))
{
	$website->check_word($_REQUEST["page"]);
	$website->SetPage($_REQUEST["page"]);
}
$website->Render();
?>