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
        <title>Stats Andamentale</title>
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
   $Condizione_AnnoMeseRichiesta = ' ';
    }
    else
    {
   $datarichiesta = $_GET['periodo'];
   $Condizione_AnnoMeseRichiesta = ' AND AnnoMeseRichiesta >='.$datarichiesta.' ' ;
  }
   //echo $Condizione_AnnoMeseRichiesta;
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
      <h2 class="alert-heading">Andamentale '.$titolofiliale.'</h2>
      <p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione andamentale con scostamenti tra entrate e uscite.</p>
  </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Situazione andamentale storica</strong>
       </div>';

// CREO LA VISTA DI APPOGGIO
$truncateviewA = mysqli_query($dbhandle,"DROP VIEW view_andamentale") or die(mysql_error());;
$viewA = mysqli_query($dbhandle," CREATE VIEW view_andamentale as 
                SELECT filiale, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as AnnoMeseRichiesta, 
                count(*) as qta_DL, sum(Valore_Nominale) as Importo_DL, '' as qta_DA, '' as Importo_DA, '' as qta_SO, '' as Importo_SO, '' as qta_ET, '' as Importo_ET, '' as qta_CE, '' as Importo_CE, '' as qta_AM, '' as Importo_AM
                FROM tab_xls_decessi_eredi
                WHERE Liquidazione_a_eredi = 'S'
                GROUP BY filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))
                UNION
               
                SELECT filiale, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as AnnoMeseRichiesta,
                '' as qta_DL, '' as Importo_DL, count(*) as qta_DA, sum(Valore_Nominale) as Importo_DA, '' as qta_SO, '' as Importo_SO, '' as qta_ET, '' as Importo_ET, '' as qta_CE, '' as Importo_CE, '' as qta_AM, '' as Importo_AM
                FROM tab_xls_decessi_eredi
                WHERE Intestazione_a_eredi <> 'S' 
                AND Liquidazione_a_eredi <> 'S'
                GROUP BY filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))
                UNION
                
                SELECT filiale, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as AnnoMeseRichiesta,
                '' as qta_DL, '' as Importo_DL, '' as qta_DA, '' as Importo_DA, count(*) as qta_SO, sum(Valore_Nominale) as Importo_SO,'' as qta_ET, '' as Importo_ET, '' as qta_CE, '' as Importo_CE, '' as qta_AM, '' as Importo_AM
                FROM tab_xls_recessi_esclusioni_sofferenze
                WHERE Escluso_x_Passaggio_a_Sofferenze = 'S' 
                GROUP BY filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))
                UNION
                
                SELECT filiale, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as AnnoMeseRichiesta,
                '' as qta_DL, '' as Importo_DL, '' as qta_DA, '' as Importo_DA, '' as qta_SO, '' as Importo_SO, count(*) as qta_ET, sum(Valore_Nominale) as Importo_ET, '' as qta_CE, '' as Importo_CE, '' as qta_AM, '' as Importo_AM
                FROM tab_xls_recessi_esclusioni_sofferenze
                WHERE Escluso_x_Passaggio_a_Sofferenze <> 'S'
                GROUP BY filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))
                UNION
                
                SELECT filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as AnnoMeseRichiesta,
                '' as qta_DL, '' as Importo_DL, '' as qta_DA, '' as Importo_DA, '' as qta_SO, '' as Importo_SO, '' as qta_ET, '' as Importo_ET, count(*) as qta_CE, sum(Valore_Nominale) as Importo_CE, '' as qta_AM, '' as Importo_AM
                FROM tab_xls_cessioni
                WHERE Cessione_a_Banca ='S' 
                GROUP BY filiale,CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))  

                UNION
                SELECT filiale,CONCAT(substring(Data_Domanda,7,4), substring(Data_Domanda,4,2)) as AnnoMeseRichiesta,
                '' as qta_DL, '' as Importo_DL, '' as qta_DA, '' as Importo_DA, '' as qta_SO, '' as Importo_SO, '' as qta_ET, '' as Importo_ET, '' as qta_CE,'' as Importo_CE, count(*) as qta_AM, (sum(Azioni_Sottoscritte) * 30.33) as Importo_AM
                FROM tab_xls_ammissioni
                WHERE Manca_DB <> 'S' 
                and Flag_da_SUCC_CESS not in ('S','C') 
                GROUP BY filiale, CONCAT(substring(Data_Domanda,7,4), substring(Data_Domanda,4,2))  

                ") or die(mysql_error());;


// Interrogo la view
$dettaglioA = " 
                SELECT  filiale, 
                        sum(qta_DL)     as qta_DL, 
                        sum(Importo_DL) as Importo_DL, 
                        sum(qta_DA)     as qta_DA, 
                        sum(Importo_DA) as Importo_DA, 
                        sum(qta_SO)     as qta_SO,
                        sum(Importo_SO) as Importo_SO, 
                        sum(qta_ET)     as qta_ET, 
                        sum(Importo_ET) as Importo_ET, 
                        sum(qta_CE)     as qta_CE,
                        sum(Importo_CE) as Importo_CE,
                        sum(qta_AM)     as qta_AM,
                        sum(Importo_AM) as Importo_AM
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                GROUP BY filiale
                 ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td rowspan="2">Filiale</td>
          <td colspan="2" align="center">Defunti<br><small>richiesta liquidazione</small></td>
          <td colspan="2" align="center">Defunti<br><small>in attesa decisione eredi</small></td>
          <td colspan="2" align="center">Sofferenze</td>
          <td colspan="2" align="center">Esclusioni</td>
          <td colspan="2" align="center">Cessioni Banca</td>          
          <td colspan="2" align="center" style="background-color:red;">TOTALE USCITE<br><small>No Succ</small></td>   
          <td colspan="2" align="center" style="background-color:green;">TOTALE ENTRATE<br><small>No Cess/Succ/Donaz</small></td>  
          <td colspan="2" align="center" style="background-color:#F39C12;">DIFFERENZA</td>  
        </tr>
        <tr class="table-secondary">
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
          <td align="right" style="background-color:red;">Qtà</td>
          <td align="right" style="background-color:red;">Importo</td>
          <td align="right" style="background-color:green;">Qtà</td>
          <td align="right" style="background-color:green;">Importo</td>
          <td align="right" style="background-color:#F39C12;">Qtà</td>
          <td align="right" style="background-color:#F39C12;">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    $tab_dettaglioA .=  "<tr>
                          <td align='right' width='4%'>".$row_areeA['filiale']."&nbsp;</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_DL'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_DL'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_DA'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_DA'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_SO'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_SO'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_ET'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_ET'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_CE'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_CE'],0,',','.')."</td>
                        ";

    $totale_qtaA = $row_areeA['qta_DL'] + $row_areeA['qta_DA'] + $row_areeA['qta_SO'] + $row_areeA['qta_ET'] + $row_areeA['qta_CE'];
    $totale_valA = $row_areeA['Importo_DL'] + $row_areeA['Importo_DA'] + $row_areeA['Importo_SO'] + $row_areeA['Importo_ET'] + $row_areeA['Importo_CE'];

    $tab_dettaglioA .=  " <td align='right' width='5%'>".number_format($totale_qtaA,0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($totale_valA,0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeA['qta_AM'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA['Importo_AM'],0,',','.')."</td>       
                          ";
    $differenza_qta = $row_areeA['qta_AM'] - $totale_qtaA;
    $differenza_val = $row_areeA['Importo_AM'] - $totale_valA;

    if ($differenza_qta < 0) {$coloredifferenzaqta = " style=color:red;" ;} else {$coloredifferenzaqta = "";}
    if ($differenza_val < 0) {$coloredifferenzaval = " style=color:red;" ;} else {$coloredifferenzaval = "";}
    
    $tab_dettaglioA .=  " <td align='right' width='5%' ".$coloredifferenzaqta.">".number_format($differenza_qta,0,',','.')."&nbsp;</td>
                          <td align='right' width='8%' ".$coloredifferenzaval.">".number_format($differenza_val,0,',','.')."</td>       
                          ";

    $tab_dettaglioA .=  "</tr> ";

  // chiudo ciclo WHILE  
  }

// CALCOLO I TOTALI
  $dettaglioT = " 
                SELECT   
                        sum(qta_DL)     as qta_DL, 
                        sum(Importo_DL) as Importo_DL, 
                        sum(qta_DA)     as qta_DA, 
                        sum(Importo_DA) as Importo_DA, 
                        sum(qta_SO)     as qta_SO,
                        sum(Importo_SO) as Importo_SO, 
                        sum(qta_ET)     as qta_ET, 
                        sum(Importo_ET) as Importo_ET, 
                        sum(qta_CE)     as qta_CE,
                        sum(Importo_CE) as Importo_CE,
                        sum(qta_AM)     as qta_AM,
                        sum(Importo_AM) as Importo_AM
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                 ";

$result_areeT = $dbhandle->query($dettaglioT) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

 while ($row_areeT = mysqli_fetch_array($result_areeT)) {

    $tab_dettaglioA .=  "<tr class='table-secondary'>
                          <td align='right' width='4%'>TOTALE&nbsp;</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_DL'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_DL'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_DA'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_DA'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_SO'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_SO'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_ET'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_ET'],0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_CE'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_CE'],0,',','.')."</td>
                        ";

    $totale_qtaT = $row_areeT['qta_DL'] + $row_areeT['qta_DA'] + $row_areeT['qta_SO'] + $row_areeT['qta_ET'] + $row_areeT['qta_CE'];
    $totale_valT = $row_areeT['Importo_DL'] + $row_areeT['Importo_DA'] + $row_areeT['Importo_SO'] + $row_areeT['Importo_ET'] + $row_areeT['Importo_CE'];

    $tab_dettaglioA .=  " <td align='right' width='5%'>".number_format($totale_qtaT,0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($totale_valT,0,',','.')."</td>
                          <td align='right' width='5%'>".number_format($row_areeT['qta_AM'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeT['Importo_AM'],0,',','.')."</td>       
                          ";
    $differenza_qtaT = $row_areeT['qta_AM'] - $totale_qtaT;
    $differenza_valT = $row_areeT['Importo_AM'] - $totale_valT;

    if ($differenza_qtaT < 0) {$coloredifferenzaqtaT = " style=color:red;" ;} else {$coloredifferenzaqtaT = "";}
    if ($differenza_valT < 0) {$coloredifferenzavalT = " style=color:red;" ;} else {$coloredifferenzavalT = "";}
    
    $tab_dettaglioA .=  " <td align='right' width='5%' ".$coloredifferenzaqtaT.">".number_format($differenza_qtaT,0,',','.')."&nbsp;</td>
                          <td align='right' width='8%' ".$coloredifferenzavalT.">".number_format($differenza_valT,0,',','.')."</td>       
                          ";

    $tab_dettaglioA .=  "</tr> ";


}


// Chiudo la tabella
$tab_dettaglioA .=  '</table>';


    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // GRAFICO TOTALI PER IMPORTO
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
	
    $dettaglioA2 = " 
                SELECT  filiale, 
                        sum(Importo_DL) + sum(Importo_DA) + sum(Importo_SO) + 
                        sum(Importo_ET) + sum(Importo_CE) as TotaleUscite,
                        sum(Importo_AM) as TotaleEntrate,
                        (
                        sum(Importo_AM) -
                        (sum(Importo_DL) + sum(Importo_DA) + sum(Importo_SO) + 
                        sum(Importo_ET) + sum(Importo_CE))
                        ) as Differenza
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                GROUP BY filiale
                ORDER BY 4 asc
                 ";  
    $result_areeA2 = $dbhandle->query($dettaglioA2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

      if ($result_areeA2) {
      $arrData = array(
        "chart" => array(
          "theme" => "candy",
          "caption" => "Andamentale Filiali per importo",
          //"subcaption" => "Situazione al ".$adesso,
          "captionFontSize" => "24",
          "subcaptionFontSize" => "20",
          "rotateValues" => "1",
          "valueFontBold" => "0",
          "subcaptionFontColor" => "#646464",
          "bgColor" => "#222222",      
          "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
          "baseFont" => "Quicksand, sans-serif",
        )
      );
     
      $arrData["data"] = array();
      while ($row2 = mysqli_fetch_array($result_areeA2)) { 
       array_push($arrData["data"], array(
          "label" => $row2["filiale"],
          "value" => $row2["Differenza"]
        ));
      }
      $jsonEncodedData = json_encode($arrData);
    }
    $Chartcolumn2d = new FusionCharts("column2d", "myChart2" , "100%", "450", "and1", "json", $jsonEncodedData);
    $Chartcolumn2d->render();


?>

<table border="0" align="center">
  <tr>     
       <td><div id="and1"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" align="center" width="30%">
        <form class="form-inline my-2 my-lg-0" action="andamentale_grafico_graphimporto.php" method="GET" onsubmit="return ray.ajax()">
		<input class="form-control mr-sm-2" type="text" name="periodo" id="periodo" placeholder="AAAAMM" >
		<button class="btn btn-primary my-2 my-sm-0" type="submit">Aggiorna Grafico</button>
        </form>
        </td>
        <td valign="top" align="right" width="30%">
            <img src="../img/graph.png" align="absmiddle">
            <a href = "andamentale_grafico.php">Trend per QUANTITA'</a>
        </td>
        <td valign="top" align="right" width="30%">
            <img src="../img/graph.png" align="absmiddle">
            <a href = "andamentale_grafico_trendimporto.php">Trend per IMPORTO</a>
       </td>
  </tr>
</table>
<br>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="100%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="100%"><?php // echo $tab_dettaglioA3; ?></td>
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