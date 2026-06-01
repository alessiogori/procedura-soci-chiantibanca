<?php
// -------------------------------------
// Prendo i valori dell'UTENTE e FILIALE
// -------------------------------------

// -------------------------------------
// Creo i COOKIES se non ci sono
// -------------------------------------
if (isset($_COOKIE['filiale_id'])) { 	$filiale_id = $_COOKIE['filiale_id'];   }
else
{	$filiale_id = $_GET['f'] ;
	setcookie('filiale_id',$filiale_id);   }

if (isset($_COOKIE['usr_id'])) { 	$usr_id = $_COOKIE['usr_id'];   }
else
{	$usr_id = ltrim($_GET['u'],'0') ;
	setcookie('usr_id',$usr_id);   }

if (isset($_COOKIE['usr_mail'])) { 	$usr_mail = $_COOKIE['usr_mail'];   }
else
{	$usr_mail = $_GET['e'] ;
	setcookie('usr_mail',$usr_mail);   }

header("refresh: 1; url = http://10.197.139.22:8080/soci/");

?>