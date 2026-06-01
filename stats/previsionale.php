<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("../config/_config.php");
include("../config/_functions.php");

// including FusionCharts PHP wrapper
include("../graph/fusioncharts.php"); 

echo '<html>
        <head>
        <script type="text/javascript" src="../js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="../js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        <title>Previsionale</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Connessione a MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");
$adesso_anno = date("Y");
$iniziodecadenza = $adesso_anno - 1;
$proxdecadenza = $adesso_anno + 1;

//$Tipo = 'LIMIT';

if (!isset($_GET['tipo']))
    {$Tipo = 'LIMIT';}
elseif ($_GET['tipo'] == "full") 
    {$Tipo = 'FULL';} 
else 
    {$Tipo = 'LIMIT';}
     

// Controllo se la richiesta arriva dai SOCI
if ((!isset($_GET['filiale'])) OR ($_GET['filiale'] == ''))
    {$condizionefiliale = '';
     $condizionefiliale2 = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
     $rif = '';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']) OR ($_GET['area']) == "")   
     {    
     $condizionefiliale = ' AND Filiale in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in ('.$_GET['filiale'].')';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND Filiale in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['filiale'];
     $rif = 'Area';
     }
    }

echo '
<style>
    br.custom-br {
        display: block;
        content: "";
        height: 5px; 
    }
</style>';

echo '
    <div class="alert alert-dismissible alert-warning">
        <h2 class="alert-heading">Previsionale Soci <small>('.$Tipo.')</small></h2>
<a class="btn btn-outline-success" style="text-decoration:none;" href="previsionale.php?tipo=full&filiale='.$filiale.'" >
<i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Completo</a>
&nbsp;&nbsp;<small>Importi TOTALI di tutti i rimborsi da effettuare per tipologia (con necessità di nuovi Soci a pareggio)</small>
<br>
<br class="custom-br">
<a class="btn btn-outline-success" style="text-decoration:none;" href="previsionale.php?tipo=limit&filiale='.$filiale.'" ><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;&nbsp;Limitato&nbsp;&nbsp;</a>
&nbsp;&nbsp;<small>Importi LIMITATI a tutti i rimborsi da effettuare fino al '.$iniziodecadenza.' per tipologia (con necessità di nuovi Soci a pareggio)</small>
<br class="custom-br">
Le Cessioni a Banca sono complete in entrambe le interrogazioni.<br>    
            <p class="mb-0 justify-content-between align-items-left">'.$titolofiliale.'</p>
    </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// DETTAGLIO AREE
// -------------------------------------------------------------------------------
$dett_aree = "  SELECT
                Area,
                sum(ESCLUSIONE) as ESCLUSIONE,
                sum(ESCLUSIONE_SOFFERENZA) as ESCLUSIONE_SOFFERENZA,
                sum(RECESSO) as RECESSO,
                sum(MORTE) as MORTE,
                sum(CESSIONE_BANCA) as CESSIONE_BANCA,
                sum(TOTALE) as TOTALE,
                sum(NUMERO_SOCI) as NUMERO_SOCI
                FROM tab_previsionale 
                WHERE Tipo = '".$Tipo."'
                ".$condizionefiliale."
                GROUP BY Area with ROLLUP
                ";

$result_dett_aree = $dbhandle->query($dett_aree) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_aree = '<table class="table" border="1" width="80%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="8" align="center">SITUAZIONE AREA</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Esclusione</td>
          <td align="right">Esclusione Soff</td>
          <td align="right">Recesso</td>
          <td align="right">Morte</td>
          <td align="right">Cessione Banca</td>
          <td align="right">Totale</td>
          <td align="right">Ulteriori Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_aree = mysqli_fetch_array($result_dett_aree)) {

    if ($row_dett_aree['NUMERO_SOCI'] < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:red;"';}

    $tab_dett_aree .= "<tr>
            <td>".$row_dett_aree['Area']."</td>
            <td align='right'>".number_format($row_dett_aree['ESCLUSIONE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['ESCLUSIONE_SOFFERENZA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['RECESSO'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['MORTE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['CESSIONE_BANCA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['TOTALE'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_aree['NUMERO_SOCI'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_aree .= '</table>';  

// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
$dett_fil = "   
            SELECT
            Area,
            Filiale, 
            NomeFiliale,
            sum(ESCLUSIONE) as ESCLUSIONE,
            sum(ESCLUSIONE_SOFFERENZA) as ESCLUSIONE_SOFFERENZA,
            sum(RECESSO) as RECESSO,
            sum(MORTE) as MORTE,
            sum(CESSIONE_BANCA) as CESSIONE_BANCA,
            sum(TOTALE) as TOTALE,
            sum(NUMERO_SOCI) as NUMERO_SOCI
            FROM tab_previsionale 
            WHERE Tipo = '".$Tipo."'
            ".$condizionefiliale."
            GROUP BY Filiale
            ORDER BY cast(Filiale as unsigned)
            ";

$result_dett_fil = $dbhandle->query($dett_fil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_fil = '<table class="table" border="1" width="80%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="10" align="center">SITUAZIONE FILIALE</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td>Filiale</td>
          <td>Nome Filiale</td>
          <td align="right">Esclusione</td>
          <td align="right">Esclusione Soff</td>
          <td align="right">Recesso</td>
          <td align="right">Morte</td>
          <td align="right">Cessione Banca</td>
          <td align="right">Totale</td>
          <td align="right">Ulteriori Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_fil = mysqli_fetch_array($result_dett_fil)) {

    if (number_format($row_dett_fil['NUMERO_SOCI'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:red;"';}

    $tab_dett_fil .= "<tr>
            <td>".$row_dett_fil['Area']."</td>
            <td>".$row_dett_fil['Filiale']."</td>
            <td>".$row_dett_fil['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_fil['ESCLUSIONE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['ESCLUSIONE_SOFFERENZA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['RECESSO'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['MORTE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['CESSIONE_BANCA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['TOTALE'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['NUMERO_SOCI'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_fil .= '</table>';     


// -------------------------------------------------------------------------------
// COSTRUZIONE LAYOUT
// -------------------------------------------------------------------------------

echo '
<table border="0" align="center" width="90%">
';

/*
if ($rif != 'filiale') { echo '  <tr><td colspan="2"><div id="amm2"><!-- Fusion Charts will also be rendered here--></div></td></tr>';} 

echo '       
  <tr>     
       <td colspan="2"><div id="amm3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>';
*/

if ($rif != 'filiale') { echo '  
  <tr>     
       <td><br>'.$tab_dett_aree.'</td>
  </tr>
  '; }

echo '  
  <tr>     
       <td><br>'.$tab_dett_fil.'</td>
  </tr>
</table>
<br>
';

// closing database connection      
$dbhandle->close();             
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>