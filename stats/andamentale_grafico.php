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

// Controllo se è stato richiesto un periodo particolare
if (!isset($_GET['periodo']))
    {
   $datarichiesta = $adesso_anno - 1;    // conteggio da un anno indietro rispetto ad oggi
   $Condizione_AnnoMeseRichiesta = ' ';
   $datarichiesta = '2019-01-01';
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
        <strong>Situazione andamentale storica (mesi di calcolo '.$numeromesi.' dal '.$datarichiesta.')</strong>
        <br>I dati si basano sulle effettive richieste giunte nel periodo, escludendo quelle pregresse
       </div>';

// CREO LA VISTA DI APPOGGIO
$truncateviewA = mysqli_query($dbhandle,"DROP VIEW view_andamentale") or die(mysqli_error($dbhandle));;
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

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - QUANTITA'
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$trend = "SELECT  AnnoMeseRichiesta,
                        sum(qta_DL) + sum(qta_DA) + sum(qta_SO) + sum(qta_ET) + sum(qta_CE)as qta_US,
                        sum(qta_AM) as qta_AM
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                GROUP BY AnnoMeseRichiesta";

$result_trend = $dbhandle->query($trend) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result_trend) {
        	
	$arrDataTrend = array(
        "chart" => array(
        	"caption"=> "Ammissioni ed Uscite di Soci (quantità)",
        	"captionFont" => "Arial",
        	"captionFontSize" => "24",
            //"captionFontColor" => "#000000",
            "subcaptionFontSize" => "20",
            "xAxisname"=> "Anno/Mese",
            "yAxisName"=> "Quantità",
            //"numberPrefix"=> "€ ",
            "plotFillAlpha"=> "80",
        	  "showValues"=> "1",
        	  "placeValuesInside"=> "1",
        	  "usePlotGradientColor"=> "0",
        	  //"rotateValues"=> "1",
        	  //"valueFontColor"=> "#FFFFFF",
        	  "showHoverEffect"=> "1",
            "rotateValues"=> "1",
            "showXAxisLine"=> "1",
            "xAxisLineThickness"=> "1",
            "xAxisLineColor"=> "#999999",
            "showAlternateHGridColor"=> "0",
            "legendBgAlpha"=> "0",
            "legendBorderAlpha"=> "0",
            "legendShadow"=> "0",
            "legendItemFontSize"=> "12",
            //"legendItemFontColor"=> "#222222",
            "legendItemFontColor"=> "#666666",
            "theme"=> "candy",
            "bgColor" => "#222222",
            //"bgAlpha" => "10",
            "labelFont" => "Arial",  
            "labelFontSize" => "12" ,   
            //"labelFontColor" => "#000000",
            "rotateLabels" => "1",
            "valueFontBold" => "0",
            "rotateValues" => "0",
            "valueFont" => "Arial",
            //"valueFontColor" => "#000000",
            "valueFontColor" => "#FFFFFF",
            "valueFontSize" => "12"
          	)
         	);

        	// creating array for categories object
        	$categoryArray=array();
        	$dataseries1=array();
        	$dataseries2=array();
        	
            // pushing category array values
        	while($rowTrend = mysqli_fetch_array($result_trend)) {				
				    array_push($categoryArray, array(
					  "label" => $rowTrend["AnnoMeseRichiesta"]
					)
				);

				array_push($dataseries1, array(
					"value" => $rowTrend["qta_US"]
					//"value" => number_format($row["Raccolta"], 0, ',', '.')
					) 
				);
			
				array_push($dataseries2, array(
					"value" => $rowTrend["qta_AM"]
					//"value" => number_format($row["Impieghi"], 0, ',', '.')
				    )
				);
    
        	}
        	
    	$arrDataTrend["categories"]=array(array("category"=>$categoryArray));

			// creating dataset object
			$arrDataTrend["dataset"] = array(array("seriesName"=> "Uscite", "data"=>$dataseries1), array("seriesName"=> "Ammissioni", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrDataTrend);
      // chart object
      $msChart = new FusionCharts("msline", "chart1" , "100%", "400", "trend", "json", $jsonEncodedData);
      $msChart->render();
			 
   }


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
    // MEDIA ENTRATE/USCITE PER MESE PER FILIALE
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    $tab_dettaglioA3 =  ' <div class="alert alert-dismissible alert-primary">
            <strong>Media mensile delle entrate e uscite (mesi di calcolo '.$numeromesi.')
           </div>';

    $dettaglioA3 = " 
                SELECT  filiale, 
                        ((sum(Importo_DL) + sum(Importo_DA) + sum(Importo_SO) + 
                        sum(Importo_ET) + sum(Importo_CE)) / ".$numeromesi.") as MediaUsciteMese,
                        (sum(Importo_AM) / ".$numeromesi.") as MediaEntrateMese
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                GROUP BY filiale
                 ";  

    $result_areeA3 = $dbhandle->query($dettaglioA3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    $tab_dettaglioA3 .= '<table border="1" valign="top" width="100%">
            <tr class="table-secondary">
              <td align="right">Filiale</td>            
              <td align="right">Media Uscite / Mese</td>
              <td align="right">Media Entrate / Mese</td>  
              <td align="right">Differenza</td>
              <td align="right">Ulteriore qtà Soci necessari al pareggio</td>                
            </tr>
          ';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA3 = mysqli_fetch_array($result_areeA3)) {

    $differenzaA3 = $row_areeA3['MediaEntrateMese'] - $row_areeA3['MediaUsciteMese'] ;
    $quoteA3    = $differenzaA3 / 30.33;
    $sociA3     = $quoteA3 / 33;

    if ($differenzaA3 < 0) {$coloredifferenzaA3 = " style=color:red;" ;} else {$coloredifferenzaA3 = " style=color:green;";}
        
    $tab_dettaglioA3 .=  "<tr>
                          <td align='right' width='4%'>".$row_areeA3['filiale']."&nbsp;</td>
                          <td align='right' width='5%'>".number_format($row_areeA3['MediaUsciteMese'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA3['MediaEntrateMese'],0,',','.')."</td>
                          <td align='right' width='8%'>".number_format($differenzaA3,0,',','.')."</td>
                          <td align='right' width='8%'".$coloredifferenzaA3."'>".(number_format($sociA3,0,',','.')*-1)."</td>
                          </tr>";
          
  }
  
  // CALCOLO IL TOTALE
    $dettaglioA3T = " 
                SELECT  ((sum(Importo_DL) + sum(Importo_DA) + sum(Importo_SO) + 
                        sum(Importo_ET) + sum(Importo_CE)) / ".$numeromesi.") as MediaUsciteMese,
                        (sum(Importo_AM) / ".$numeromesi.") as MediaEntrateMese
                FROM view_andamentale
                WHERE filiale <> 999 
                ".$Condizione_AnnoMeseRichiesta."
                ".$condizionefiliale."
                 ";  

    $result_areeA3T = $dbhandle->query($dettaglioA3T) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
    while ($row_areeA3T = mysqli_fetch_array($result_areeA3T)) {
    $differenzaA3T = $row_areeA3T['MediaEntrateMese'] - $row_areeA3T['MediaUsciteMese'] ;
    $quoteA3T    = $differenzaA3T / 30.33;
    $sociA3T     = $quoteA3T / 33;
    $tab_dettaglioA3 .=  "<tr class='table-secondary'>
                          <td align='right' width='4%'>TOTALE</td>
                          <td align='right' width='5%'>".number_format($row_areeA3T['MediaUsciteMese'],0,',','.')."&nbsp;</td>
                          <td align='right' width='8%'>".number_format($row_areeA3T['MediaEntrateMese'],0,',','.')."</td>
                          <td align='right' width='8%'>".number_format($differenzaA3T,0,',','.')."</td>
                          <td align='right' width='8%'>".(number_format($sociA3T,0,',','.')*-1)."</td>
                          </tr>";
          
  }  
// Chiudo la tabella
$tab_dettaglioA3 .=  '</table>';
       
?>

<table border="0" align="center">
  <tr>     
       <td><div id="trend"><!-- Fusion Charts will also be rendered here--></div></td> 
  </tr>
</table>
<br>

<?php
if (!isset($_GET['key'])) {
?>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" align="center" width="30%">
        <form class="form-inline my-2 my-lg-0" action="andamentale_grafico.php" method="GET" onsubmit="return ray.ajax()">
		<input class="form-control mr-sm-2" type="text" name="periodo" id="periodo" placeholder="AAAAMM" >
		<button class="btn btn-primary my-2 my-sm-0" type="submit">Aggiorna Grafico</button>
        </form>
        </td>
        <td valign="top" align="right" width="30%">
            <img src="../img/graph.png" align="absmiddle">
            <a href = "andamentale_grafico_trendimporto.php">Trend per IMPORTO</a>
        </td>
        <td valign="top" align="right" width="30%">
            <img src="../img/graph.png" align="absmiddle">
            <a href = "andamentale_grafico_graphimporto.php">Grafico per IMPORTO</a>
       </td>
  </tr>
</table>
<?php
}
?>

<br>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="100%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="100%"><?php  echo $tab_dettaglioA3; ?></td>
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