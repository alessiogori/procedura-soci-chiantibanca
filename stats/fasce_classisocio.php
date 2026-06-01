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
        <title>Stats Fasce</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");
$adesso_anno = date("Y");

$data1 = new DateTime('2019-01-01');
$data2 = new DateTime(date("Y-m-d"));
$mesi = $data2->diff($data1); 
$numeromesi = (($mesi->y) * 12) + ($mesi->m);
// echo $howeverManyMonths;

// Controllo se è stato richiesto un periodo particolare
if (!isset($_GET['periodo']))
    {
   $datarichiesta = $adesso_anno - 1;    // conteggio da un anno indietro rispetto ad oggi
    }
    else
    {
   $datarichiesta = $_GET['periodo'];
  }

// Controllo se la richiesta arriva   
if (!isset($_GET['key']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']))   
     {    
     $condizionefiliale = 'AND filiale = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = 'AND filiale in ('.$_GET['key'].')';
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     }
    }

echo '
  <div class="alert alert-dismissible alert-warning">
      <h2 class="alert-heading">Classi Soci per anzianità di appartenenza alla compagine sociale '.$titolofiliale.'</h2>
  </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER ANNI DI ANZIANITA' (ENTRATA)
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Classi Soci per anni anzianità di appartenenza alla compagine sociale (per Data Entrata)
       </div>';

// Interrogo la view
$dettaglioA = " 
                SELECT
                    case 
                        when AnniAnzianitaSocio <= 10          then 'Fascia 1 - fino a 10 anni'
                        when AnniAnzianitaSocio between 10 and 20 then 'Fascia 2 - fino a 20 anni'
                        when AnniAnzianitaSocio between 20 and 30 then 'Fascia 3 - fino a 30 anni'
                        when AnniAnzianitaSocio between 30 and 40 then 'Fascia 4 - fino a 40 anni'
                        when AnniAnzianitaSocio between 40 and 50 then 'Fascia 5 - fino a 50 anni'
                        when AnniAnzianitaSocio > 50           then 'Fascia 6 - oltre 50 anni'
                    else '' end as Fascia,
                    count(*) as qta,
                    sum(NumeroAzioni) as AzTotali,
                    round( sum(Importo) ) as CapitaleTotale
                FROM view_fasce_anzianitasocio
                WHERE Filiale <> 999
                ".$condizionefiliale."
                GROUP BY Fascia WITH ROLLUP
                -- ORDER BY Fascia 
                 ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table class="table table-hover" border="0" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="25%">Fascia</td>
          <td align="right" width="14%">Qtà Soci</td>
          <td align="right" width="14%">Qtà Azioni</td>
          <td align="right" width="14%">Capitale</td>  
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    /*      
    if ( $row_areeA['Fascia'] == "Socio 10" ) {$param = "filiale=".$filiale."&fascia=10";}
    if ( $row_areeA['Fascia'] == "Socio 20" ) {$param = "filiale=".$filiale."&fascia=20";}
    if ( $row_areeA['Fascia'] == "Socio 30" ) {$param = "filiale=".$filiale."&fascia=30";}
    if ( $row_areeA['Fascia'] == "Socio 40" ) {$param = "filiale=".$filiale."&fascia=40";}
    if ( $row_areeA['Fascia'] == "Socio 50" ) {$param = "filiale=".$filiale."&fascia=50";}
    */

    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='25%'>".$row_areeA['Fascia']."&nbsp;</td>
                          <td align='right' width='14%'>
                          <a href='../fasce_classisocio_lista.php?start=0&filiale=".$filiale."&fascia=".substr($row_areeA['Fascia'],7,1)."' onclick='return ray.ajax()'>
                          ".number_format($row_areeA['qta'],0,',','.')."</a>&nbsp;</td>                          
                          <td align='right' width='14%'>".number_format($row_areeA['AzTotali'],0,',','.')."&nbsp;</td>
                          <td align='right' width='14%'>".number_format($row_areeA['CapitaleTotale'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';


    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // GRAFICO TOTALI PER FASCE
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    $dettaglioA2 = " 
                SELECT
                    count(*) as qta,
                    case 
                        when AnniAnzianitaSocio <= 10          then 'Fascia 1 - fino a 10 anni'
                        when AnniAnzianitaSocio between 10 and 20 then 'Fascia 2 - fino a 20 anni'
                        when AnniAnzianitaSocio between 20 and 30 then 'Fascia 3 - fino a 30 anni'
                        when AnniAnzianitaSocio between 30 and 40 then 'Fascia 4 - fino a 40 anni'
                        when AnniAnzianitaSocio between 40 and 50 then 'Fascia 5 - fino a 50 anni'
                        when AnniAnzianitaSocio > 50           then 'Fascia 6 - oltre 50 anni'
                    else '' end as Fascia
                FROM view_fasce_anzianitasocio
                WHERE Filiale <> 999
                ".$condizionefiliale."
                GROUP BY Fascia
                 ";  
    $result_areeA2 = $dbhandle->query($dettaglioA2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

      if ($result_areeA2) {
      $arrData = array(
      "chart" => array(
        "theme" => "candy",
        //"caption" => "Fasce Quote Soci senza richieste in corso",
        //"subcaption" => "(Plafond permettendo)",
        "captionFontSize" => "24",
        "subcaptionFontSize" => "20",
        "subcaptionFontColor" => "#646464",
        "bgColor" => "#222222",
        //"paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
        "baseFont" => "Quicksand, sans-serif",
        "showLegend" => "0",
        //"exportEnabled" => "1"
        )
      );
     
      $arrData["data"] = array();
      while ($row2 = mysqli_fetch_array($result_areeA2)) { 
       array_push($arrData["data"], array(
          "label" => $row2["Fascia"],
          "value" => $row2["qta"]
        ));
      }
      $jsonEncodedData = json_encode($arrData);
    }
    $Chartpie2d = new FusionCharts("pie2d", "myChart" , "100%", "450", "fasce", "json", $jsonEncodedData);
    $Chartpie2d->render();



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TOTALE PER ANNI ANZIANITA' AD OGGI 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioAO =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Riepilogo Soci PF per anni anzianità (ad oggi)
       </div>';

// Interrogo la view
$dettaglioAO = " 
                SELECT AnniAnzianitaSocio, count(*) as qta
				FROM view_fasce_anzianitasocio
                WHERE Filiale <> 999
                AND TIPO_NAG = 'PF'
                ".$condizionefiliale."
				GROUP BY AnniAnzianitaSocio
				ORDER BY AnniAnzianitaSocio desc
                 ";

$result_areeAO = $dbhandle->query($dettaglioAO) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioAO .= '<table class="table table-hover" border="0" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="25%">Anni Anzianità</td>
          <td align="right" width="14%">Qtà Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeAO = mysqli_fetch_array($result_areeAO)) {
      
    $tab_dettaglioAO .=  "<tr>
                          <td align='left' width='25%'>".$row_areeAO['AnniAnzianitaSocio']."&nbsp;</td>
                          <td align='right' width='14%'>
                          <a href='../fasce_classisocio_lista.php?start=1&filiale=".$filiale."&anzianita=".$row_areeAO['AnniAnzianitaSocio']."&nextyear=no&fascia=' onclick='return ray.ajax()'>
                          ".number_format($row_areeAO['qta'],0,',','.')."</a>&nbsp;</td>                          
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioAO .=  '</table>';



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TOTALE PER ANNI ANZIANITA' PROSSIMO ANNO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioAN =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Riepilogo Soci PF per anni anzianità (prossimo anno)
       </div>';

// Interrogo la view
$dettaglioAN = " 
                SELECT AnniAnzianitaSocio+1 as AnzSocioNEXTYEAR, count(*) as qta
                FROM view_fasce_anzianitasocio
                WHERE Filiale <> 999
                AND TIPO_NAG = 'PF'
                ".$condizionefiliale."
                GROUP BY AnzSocioNEXTYEAR
                ORDER BY AnzSocioNEXTYEAR desc
                 ";

$result_areeAN = $dbhandle->query($dettaglioAN) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioAN .= '<table class="table table-hover" border="0" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="25%">Anni Anzianità</td>
          <td align="right" width="14%">Qtà Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeAN = mysqli_fetch_array($result_areeAN)) {
      
    $tab_dettaglioAN .=  "<tr>
                          <td align='left' width='25%'>".$row_areeAN['AnzSocioNEXTYEAR']."&nbsp;</td>
                          <td align='right' width='14%'>
                          <a href='../fasce_classisocio_lista.php?start=1&filiale=".$filiale."&anzianita=".$row_areeAN['AnzSocioNEXTYEAR']."&nextyear=si&fascia=' onclick='return ray.ajax()'>
                          ".number_format($row_areeAN['qta'],0,',','.')."</a>&nbsp;</td>                       
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioAN .=  '</table>';

?>





<table border="0" align="center">
  <tr>     
       <td><div id="fasce"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="40%"><?php echo $tab_dettaglioAO; ?></td>
       <td valign="top" width="10%">&nbsp;</td>
       <td valign="top" width="40%"><?php echo $tab_dettaglioAN; ?></td>
  </tr>
</table>
<br>

<?php

    
// closing database connection      
$dbhandle->close();       
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>