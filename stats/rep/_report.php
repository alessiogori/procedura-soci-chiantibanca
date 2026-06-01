<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);


// Includo i dati di connessione
include("../../config/_config.php");
include("../../config/_functions.php");

// including FusionCharts PHP wrapper
include("../../graph/fusioncharts.php"); 

echo '<html>
        <head>
        <script type="text/javascript" src="../../js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="../../js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        <title>REPORT GENERALE</title>
        </head>
        <style type="text/css">
          @import "../../css/bootstrap.css";
          @import "../../css/bootstrap.min.css";
          @import "../../css/fontawesome-free/css/all.min.css";
        </style> 

        <body style="background-color:white; color:black;"><br><br>
        ';

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

$adesso = date("d/m/Y");
$anno = date("Y");
// $adesso_anno = date("Y");

if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/'.$anno;
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

// Controllo se è stato richiesto un periodo particolare
if (!isset($_GET['periodo']))
    {
   $datarichiesta = $adesso_anno - 1;    // conteggio da un anno indietro rispetto ad oggi
   $Condizione_AnnoMeseRichiesta = ' ';
//   $datarichiesta = '2019-01-01';
    }
    else
    {
   $datarichiesta = $_GET['periodo'];
   $Condizione_AnnoMeseRichiesta = ' AND AnnoMeseRichiesta >='.$datarichiesta.' ' ;
   $datarichiesta = substr($_GET['periodo'],0,4).'-'.substr($_GET['periodo'],-2).'-01'; 
  }
   //echo $Condizione_AnnoMeseRichiesta;
   //echo $datarichiesta;

//$data1 = new DateTime('2019-01-01');
$data1 = new DateTime($datarichiesta);
$data2 = new DateTime(date("Y-m-d"));
$mesi = $data2->diff($data1); 
$numeromesi = (($mesi->y) * 12) + ($mesi->m);

// Controllo se la richiesta arriva   
if (!isset($_GET['key']))
    {$condizionefiliale = '';
     $condizionefiliale2 = '';
     $titolofiliale = ' BANCA ';
     $filiale = '';
     $area = '';
     $rif = 'BANCA';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']) OR ($_GET['area']) == "")   
     {    
     $condizionefiliale = 'AND filiale in ('.$_GET['key'].')';
     $condizionefiliale2 = 'AND FILIALE_CAPOFILA in ('.$_GET['key'].')';
     $condizionefiliale3 = 'WHERE filiale in ('.$_GET['key'].')';
     $condizionefiliale4 = 'AND FIL_ANAGRAFICA in ('.$_GET['key'].')';
     $condizionefiliale5 = ' AND ANAG_NAG.FILIALE_CAPOFILA in ('.$_GET['key'].')';
     $titolofiliale = ' Filiale '.$_GET['key'];  
     $filiale = $_GET['key'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = 'AND filiale in ('.$_GET['key'].')';
     $condizionefiliale2 = 'AND FILIALE_CAPOFILA in ('.$_GET['key'].')';
     $condizionefiliale3 = 'WHERE filiale in ('.$_GET['key'].')';
     $condizionefiliale4 = 'AND FIL_ANAGRAFICA in ('.$_GET['key'].')';
     $condizionefiliale5 = ' AND ANAG_NAG.FILIALE_CAPOFILA in ('.$_GET['key'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

include ("rep_00_cover.php");
    echo '<P style="page-break-before: always">';
include ("rep_01_statistiche.php");
    echo '<P style="page-break-before: always">';
include ("rep_02_situazione.php");
    echo '<P style="page-break-before: always">'; 
include ("rep_02b_previsionale.php");
    echo '<P style="page-break-before: always">'; 
include ("rep_03_liquidazioni.php");
    echo '<P style="page-break-before: always">';
include ("rep_04_giovani.php");
    echo '<P style="page-break-before: always">';
include ("rep_05_azionifasce.php");
    echo '<P style="page-break-before: always">';
include ("rep_06_socistorici.php");
    echo '<P style="page-break-before: always">';
include ("rep_99_indici.php");

if (($rif == 'Area') OR ($rif == 'BANCA'))
  {
    echo '<P style="page-break-before: always">';   
    include ("rep_99_filiali.php");
  }    


?>