<?php
// *****************************************************************************
// Portale ChiantiBanca
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("../config/_config.php");
include("../config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("../css/main.php");
// include("../css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>


<a href="https://teams.microsoft.com/l/chat/0/0?users=luca.franchi@chiantibanca.it" target="_blank">Luca</a>