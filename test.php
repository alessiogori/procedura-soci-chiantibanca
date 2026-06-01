<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");
//include("bday_mail.php");
?>

<?php
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
// bday_mail(); 

$dataentrata = strtotime("15/05/2021");
$databritish = date('Y/m/d',$dataentrata);
echo $databritish;
$nAzTot = 3 ; // sostituire con valore letto da DB
$nMesiMancanti = 33 / $nAzTot ;
$final = $dataentrata . ' + '.$nMesiMancanti.' months';
echo "'".$final."'";
echo '<br>';
echo date($dataentrata, strtotime("'".$final."'"));
echo '<br>';
echo date('d/m/Y', strtotime(' 10 months'));



$dt = strtotime("2012-12-21");
echo date("Y-m-d", strtotime("+1 month", $dt))."\n";
?>