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
        <title>Stats Ammissioni</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");


if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/2022';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

// Controllo se la richiesta arriva dai SOCI
if (!isset($_GET['filiale']))
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

// ----------------------------------------------------
// FusionChart - Controllo tema e colorazione
// ----------------------------------------------------
if ($_SERVER["HTTP_REFERER"] == 'http://10.197.139.22:8080/soci/stats/repcda_prospetto_consiglio.php')
    {
        $tema = 'fusion';
        $valueFontColor = '#222222';
        $bgcolor = '#FFFFFF';
    }   
else  {
        $tema = 'candy';
        $valueFontColor = '#FFFFFF';
        $bgcolor = '#222222';
    } 

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Nuove Ammissioni Soci</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
	</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// CREAZIONE VISTA TEMPORANEA
// -------------------------------------------------------------------------------
$truncateview = mysqli_query($dbhandle,"DROP VIEW TMP_SOCI_INOUT") or die(mysql_error());;
$createview = mysqli_query($dbhandle," 
                    CREATE VIEW TMP_SOCI_INOUT AS
                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, count(*) as 'Soci_inizio',  '' as  'Incremento', '' as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['datain']."','%d/%m/%Y')  
                    AND
                        ( DATA_USCITA =  '0'  
                    OR
                        str_to_date(DATA_USCITA,'%d/%m/%Y') >  str_to_date('".$_GET['datain']."','%d/%m/%Y')  )
                    GROUP BY Area, Filiale
                        
                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  count(*) as  'Incremento', '' as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')  
                    AND
                        DATA_ENTRATA < NOW()
                    GROUP BY Area, Filiale
                        
                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  '' as  'Incremento', count(*) as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                             str_to_date(DATA_USCITA,'%d/%m/%Y') >= str_to_date('".$_GET['datain']."','%d/%m/%Y') AND
                             DATA_USCITA <= NOW()
                    GROUP BY Area, Filiale

                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  '' as  'Incremento', '' as 'Decremento', count(*)  as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                             DATA_ENTRATA <= NOW()
                             AND (DATA_USCITA =  '0' OR
                                     DATA_USCITA > NOW())
                    GROUP BY Area, Filiale
                    ORDER BY 1  
            ") or die(mysql_error());;             

// -------------------------------------------------------------------------------
// AMMISSIONI TOTALI
// -------------------------------------------------------------------------------
$strQuery2 = "  SELECT cast(Filiale as unsigned) as Filiale, NomeFiliale,
            sum(Incremento) as qta_entrati, 
            sum(Decremento) as qta_usciti
            FROM tmp_soci_inout
            WHERE Periodo >= '".$_GET['datain']."'
            AND Filiale < 900
            ".$condizionefiliale."
            GROUP BY Filiale
            ORDER BY 1 ASC
                ";


$result2 = $dbhandle->query($strQuery2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result2) {
            
    $arrData2 = array(
        "chart" => array(
            "caption"=> "Ammissioni e Uscite per Filiale dal ".$_GET['datain'],
            "captionFont" => "Arial",
            "captionFontSize" => "24",
            //"captionFontColor" => "#000000",
            "subcaptionFontSize" => "20",
            "xAxisname"=> "Filiale",
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
            while($rowResult2 = mysqli_fetch_array($result2)) {              
                    array_push($categoryArray, array(
                      "label" => $rowResult2["Filiale"].' '.$rowResult2["NomeFiliale"]
                    )
                );

                array_push($dataseries1, array(
                    "value" => $rowResult2["qta_entrati"]
                    //"value" => number_format($row["qta_Pulita"], 0, ',', '.')
                    ) 
                );
            
                array_push($dataseries2, array(
                    "value" => $rowResult2["qta_usciti"]
                    //"value" => number_format($row["qta_Passaggio"], 0, ',', '.')
                    )
                );
    
            }
            
        $arrData2["categories"]=array(array("category"=>$categoryArray));

            // creating dataset object
            $arrData2["dataset"] = array(array("seriesName"=> "Soci Entrati", "data"=>$dataseries1), array("seriesName"=> "Soci Usciti", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrData2);
      // chart object
      $msChart = new FusionCharts("msline", "myChart2" , "100%", "400", "amm2", "json", $jsonEncodedData);
      $msChart->render();
             
   }

// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - QUANTITA'
// -------------------------------------------------------------------------------
$trend = "  SELECT AnnoMeseRichiesta,
            sum(qta_entrati) as qta_entrati, 
            sum(qta_usciti) as qta_usciti
            FROM view_ammissioni_uscite
            WHERE AnnoMeseRichiesta >= 202001
            GROUP BY AnnoMeseRichiesta
            ORDER BY 1 ASC
                ";

$result_trend = $dbhandle->query($trend) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result_trend) {
        	
	$arrDataTrend = array(
        "chart" => array(
        	"caption"=> "Ammissioni e Uscite per mese/anno (storico Banca da 2020)",
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
            "theme"=> "".$tema."",
            "bgColor" => "".$bgcolor."",
            //"bgAlpha" => "10",
            "labelFont" => "Arial",  
            "labelFontSize" => "12" ,   
            //"labelFontColor" => "#000000",
            "rotateLabels" => "1",
            "valueFontBold" => "0",
            "rotateValues" => "0",
            "valueFont" => "Arial",
            //"valueFontColor" => "#000000",
            "valueFontColor" => "".$valueFontColor."",
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
					"value" => $rowTrend["qta_entrati"]
					//"value" => number_format($row["qta_Pulita"], 0, ',', '.')
					) 
				);
			
				array_push($dataseries2, array(
					"value" => $rowTrend["qta_usciti"]
					//"value" => number_format($row["qta_Passaggio"], 0, ',', '.')
				    )
				);
    
        	}
        	
    	$arrDataTrend["categories"]=array(array("category"=>$categoryArray));

			// creating dataset object
			$arrDataTrend["dataset"] = array(array("seriesName"=> "Soci Entrati", "data"=>$dataseries1), array("seriesName"=> "Soci Usciti", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrDataTrend);
      // chart object
      $msChart = new FusionCharts("msline", "myChart3" , "100%", "400", "amm3", "json", $jsonEncodedData);
      $msChart->render();
			 
   }

// -------------------------------------------------------------------------------
// MEDIA PER ANNO
// -------------------------------------------------------------------------------
$media = "  SELECT '2020' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2020
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2021' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2021
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2022' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2022
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            ORDER BY 1 ASC
                ";


$result_media = $dbhandle->query($media) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_media = '<table border="1" width="30%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="5" align="center">MEDIA BANCA</td>
        </tr>
        <tr class="table-secondary">
          <td>Anno</td>
          <td align="right">Qtà Mesi</td>
          <td align="right">Media Entrati</td>
          <td align="right">Media Usciti</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_media = mysqli_fetch_array($result_media)) {

    if (number_format($row_media['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_media .= "<tr>
            <td>".$row_media['AnnoMeseRichiesta']."</td>
            <td align='right'>".number_format($row_media['MesiCount'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_entrati'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_usciti'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_media['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_media .= '</table>';

// -------------------------------------------------------------------------------
// DETTAGLIO AREE
// -------------------------------------------------------------------------------
$dett_aree = "  SELECT Area, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(Incremento))  as Incremento,
                round(sum(Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff
                FROM tmp_soci_inout
                WHERE Periodo >= '".$_GET['datain']."'
                ".$condizionefiliale2."
                GROUP BY Area WITH ROLLUP
                ";

$result_dett_aree = $dbhandle->query($dett_aree) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_aree = '<table border="1" width="50%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="6" align="center">SITUAZIONE AREA</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_aree = mysqli_fetch_array($result_dett_aree)) {

    if ($row_dett_aree['Diff'] < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_dett_aree .= "<tr>
            <td>".$row_dett_aree['Area']."</td>
            <td align='right'>".number_format($row_dett_aree['SociInizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['SociFine'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_aree['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_aree .= '</table>';     

// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
$dett_fil = "   SELECT Area, Filiale, NomeFiliale, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(Incremento))  as Incremento,
                round(sum(Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff
                FROM tmp_soci_inout
                WHERE Periodo >= '".$_GET['datain']."'
                ".$condizionefiliale2."
                GROUP BY Area, Filiale, NomeFiliale  
                ";

$result_dett_fil = $dbhandle->query($dett_fil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_fil = '<table border="1" width="50%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="8" align="center">SITUAZIONE FILIALE</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td>Filiale</td>
          <td>Nome Filiale</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_fil = mysqli_fetch_array($result_dett_fil)) {

    if (number_format($row_dett_fil['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_dett_fil .= "<tr>
            <td>".$row_dett_fil['Area']."</td>
            <td>".$row_dett_fil['Filiale']."</td>
            <td>".$row_dett_fil['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_fil['SociInizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['SociFine'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['Diff'],0,',','.')."</td>
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

if ($rif != 'filiale') { echo '  <tr><td colspan="2"><div id="amm2"><!-- Fusion Charts will also be rendered here--></div></td></tr>';} 

echo '       
  <tr>     
       <td colspan="2"><div id="amm3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>';

if ($rif != 'filiale') { echo '  
  <tr>     
       <td>'.$tab_media.'</td>
       <td><br>'.$tab_dett_aree.'</td>
  </tr>
  '; }

echo '  
  <tr>     
       <td colspan="2"><br>'.$tab_dett_fil.'</td>
  </tr>
</table>
<br>
';
//<!-- <center><h4>Aggiungere link per elenco</h4></center> -->

// closing database connection      
$dbhandle->close();				
?>

<br><center>
            <a href="../lista_ammissioni.php?filiale=<?php echo $filiale; ?>" style="text-color:white;" target="_blank">Visualizza lista dettaglio Ammissioni</a>
            </center>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>