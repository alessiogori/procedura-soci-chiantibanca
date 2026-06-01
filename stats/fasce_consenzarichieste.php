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
     $condizionefiliale = 'AND filiale in '.$_GET['key'];
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     }
    }

echo '
  <div class="alert alert-dismissible alert-warning">
      <h2 class="alert-heading">Fasce Quote Soci senza richieste in corso '.$titolofiliale.'</h2>
  </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Fasce Quote Soci senza richieste di cessione in corso
       </div>';

$tab_dettaglioB =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Fasce Quote Soci - Generali
       </div>';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER FASCE - GENERALE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------

// CREO LA VISTA DI APPOGGIO
$truncateviewB = mysqli_query($dbhandle,"DROP VIEW view_fasce_generale") or die(mysql_error());;
$viewB = mysqli_query($dbhandle," 

            CREATE VIEW view_fasce_generale AS 

            SELECT
                'Fascia 1 (0 - 1.000 Euro)' AS Fascia,
                s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 0 AND 1000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 2 (1.001 - 2.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 1001 AND 2000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 3 (2.001 - 5.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 2001 AND 5000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION

            SELECT
                'Fascia 4 (5.001 - 15.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 5001 AND 15000
            AND s.SOCIO_ISTITUTO = 1
             AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 5 (15.001 - 30.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 15001 AND 30000
            AND s.SOCIO_ISTITUTO = 1
                        AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 6 (30.001 - 50.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 30001 AND 50000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 7 (oltre 50.001 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI > 50001
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            ORDER BY  1, 2

                ") or die(mysql_error());;


// Interrogo la view
$dettaglioB = " 
                SELECT
                    count(*) as qta,
                    Fascia,
                    round( sum(ValAzTotali) ) as CapitaleTotale,
                    round( (sum(ValAzTotali) ) / count(*) ) as MediaCapitale,
                    round( (sum(ValAzTotali) / 30.33) / count(*) ) as MediaAzioni
                FROM view_fasce_generale
                WHERE Filiale <> 999
                ".$condizionefiliale."
                GROUP BY Fascia
                ORDER BY Fascia
                 ";

$result_areeB = $dbhandle->query($dettaglioB) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioB .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="25%">Fascia</td>
          <td align="right" width="14%">Qtà Soci</td>
          <td align="right" width="14%">Capitale</td>          
          <td align="right" width="14%">Media Capitale</td>
          <td align="right" width="14%">Media Azioni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeB = mysqli_fetch_array($result_areeB)) {
      
    if ( substr($row_areeB['Fascia'],7,1) == "1" ) {$param = "filiale=".$filiale."&fascia=1";}
    if ( substr($row_areeB['Fascia'],7,1) == "2" ) {$param = "filiale=".$filiale."&fascia=2";}
    if ( substr($row_areeB['Fascia'],7,1) == "3" ) {$param = "filiale=".$filiale."&fascia=3";}
    if ( substr($row_areeB['Fascia'],7,1) == "4" ) {$param = "filiale=".$filiale."&fascia=4";}
    if ( substr($row_areeB['Fascia'],7,1) == "5" ) {$param = "filiale=".$filiale."&fascia=5";}
    if ( substr($row_areeB['Fascia'],7,1) == "6" ) {$param = "filiale=".$filiale."&fascia=6";}
    if ( substr($row_areeB['Fascia'],7,1) == "7" ) {$param = "filiale=".$filiale."&fascia=7";}
    if ( substr($row_areeB['Fascia'],7,1) == "8" ) {$param = "filiale=".$filiale."&fascia=8";}
    
    $tab_dettaglioB .=  "<tr>
                          <td align='left' width='25%'>".$row_areeB['Fascia']."&nbsp;</td>
                          <td align='right' width='14%'>
                          <a href='../fasce_senzarichieste_lista.php?".$param."' onclick='return ray.ajax()'>
                          ".number_format($row_areeB['qta'],0,',','.')."</a>&nbsp;</td>                          
                          <td align='right' width='14%'>".number_format($row_areeB['CapitaleTotale'],0,',','.')."</td>
                          <td align='right' width='14%'>".number_format($row_areeB['MediaCapitale'],0,',','.')."&nbsp;</td>
                          <td align='right' width='14%'>".number_format($row_areeB['MediaAzioni'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioB .=  '</table>';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER FASCE SENZA RICHIESTE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------

// CREO LA VISTA DI APPOGGIO
$truncateviewA = mysqli_query($dbhandle,"DROP VIEW view_fasce_senzarichieste") or die(mysql_error());;
$viewA = mysqli_query($dbhandle," 

            CREATE VIEW view_fasce_senzarichieste AS 

            SELECT
                'Fascia 1 (0 - 1.000 Euro)' AS Fascia,
                s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 0 AND 1000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 2 (1.001 - 2.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE c.VALORE_AZIONI BETWEEN 1001 AND 2000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 3 (2.001 - 5.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE s.nag not in (select ces.NAG from tab_xls_cessionibanca as ces)
            AND c.VALORE_AZIONI BETWEEN 2001 AND 5000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION

            SELECT
                'Fascia 4 (5.001 - 15.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE s.nag not in (select ces.NAG from tab_xls_cessionibanca as ces)
            AND c.VALORE_AZIONI BETWEEN 5001 AND 15000
            AND s.SOCIO_ISTITUTO = 1
             AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 5 (15.001 - 30.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE s.nag not in (select ces.NAG from tab_xls_cessionibanca as ces)
            AND c.VALORE_AZIONI BETWEEN 15001 AND 30000
            AND s.SOCIO_ISTITUTO = 1
                        AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 6 (30.001 - 50.000 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE s.nag not in (select ces.NAG from tab_xls_cessionibanca as ces)
            AND c.VALORE_AZIONI BETWEEN 30001 AND 50000
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            UNION
            
            SELECT
                'Fascia 7 (oltre 50.001 Euro)' AS Fascia,
                 s.nag as nag, CONCAT(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as Nominativo, s.FILIALE_CAPOFILA as Filiale,
                c.VALORE_AZIONI as ValAzTotali
            FROM sds_soci as s, sds_soci_certificati as c
            WHERE s.nag not in (select ces.NAG from tab_xls_cessionibanca as ces)
            AND c.VALORE_AZIONI > 50001
            AND s.SOCIO_ISTITUTO = 1
            AND s.IDSOCIO = c.IDSOCIO
            GROUP BY s.nag, c.VALORE_AZIONI
            
            ORDER BY  1, 2

                ") or die(mysql_error());;



// Interrogo la view
$dettaglioA = " 
                SELECT
                    count(*) as qta,
                    Fascia,
                    round( sum(ValAzTotali) ) as CapitaleTotale,
                    round( (sum(ValAzTotali) ) / count(*) ) as MediaCapitale,
                    round( (sum(ValAzTotali) / 30.33) / count(*) ) as MediaAzioni
                FROM view_fasce_senzarichieste
                WHERE Filiale <> 999
                ".$condizionefiliale."
                GROUP BY Fascia
                ORDER BY Fascia
                 ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="25%">Fascia</td>
          <td align="right" width="14%">Qtà Soci</td>
          <td align="right" width="14%">Capitale</td>          
          <td align="right" width="14%">Media Capitale</td>
          <td align="right" width="14%">Media Azioni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {
      
    if ( substr($row_areeA['Fascia'],7,1) == "1" ) {$param = "filiale=".$filiale."&fascia=1";}
    if ( substr($row_areeA['Fascia'],7,1) == "2" ) {$param = "filiale=".$filiale."&fascia=2";}
    if ( substr($row_areeA['Fascia'],7,1) == "3" ) {$param = "filiale=".$filiale."&fascia=3";}
    if ( substr($row_areeA['Fascia'],7,1) == "4" ) {$param = "filiale=".$filiale."&fascia=4";}
    if ( substr($row_areeA['Fascia'],7,1) == "5" ) {$param = "filiale=".$filiale."&fascia=5";}
    if ( substr($row_areeA['Fascia'],7,1) == "6" ) {$param = "filiale=".$filiale."&fascia=6";}
    if ( substr($row_areeA['Fascia'],7,1) == "7" ) {$param = "filiale=".$filiale."&fascia=7";}
    if ( substr($row_areeA['Fascia'],7,1) == "8" ) {$param = "filiale=".$filiale."&fascia=8";}
    
    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='25%'>".$row_areeA['Fascia']."&nbsp;</td>
                          <td align='right' width='14%'>
                          <a href='../fasce_senzarichieste_lista.php?".$param."' onclick='return ray.ajax()'>
                          ".number_format($row_areeA['qta'],0,',','.')."</a>&nbsp;</td>                          
                          <td align='right' width='14%'>".number_format($row_areeA['CapitaleTotale'],0,',','.')."</td>
                          <td align='right' width='14%'>".number_format($row_areeA['MediaCapitale'],0,',','.')."&nbsp;</td>
                          <td align='right' width='14%'>".number_format($row_areeA['MediaAzioni'],0,',','.')."</td>
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
                    Fascia
                FROM view_fasce_senzarichieste
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

?>

<table border="0" align="center">
  <tr>     
       <td><div id="fasce"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioB; ?><br></td>
  </tr>
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
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