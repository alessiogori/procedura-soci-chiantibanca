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
        <title>Stats Volumi</title>
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

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Volumi Soci</h2>
  		<p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione più recente delle masse di raccolta e impieghi e della quantità dei principali prodotti posseduti.</p>
	</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// MULTILINE RACCOLTA E IMPIEGHI - AREA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery = "   SELECT area, round(sum(Raccolta)/1000) as Raccolta, round((sum(Impieghi)*-1)/1000) as Impieghi 
                FROM view_volumi_aree
                WHERE area <> '' 
                GROUP BY area";
//                $strQueryCondition;

 	$result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result) {
        	
	$arrData = array(
        "chart" => array(
        	"caption"=> "Raccolta e Impieghi per Area (in milioni)",
        	"captionFontSize" => "24",
            "subcaptionFontSize" => "20",
            "xAxisname"=> "Aree",
            "yAxisName"=> "Valore (in Eur)",
            "numberPrefix"=> "€ ",
            "plotFillAlpha"=> "80",
        	  "showValues"=> "1",
        	  "placeValuesInside"=> "1",
        	  "usePlotGradientColor"=> "0",
        	  "rotateValues"=> "1",
        	  "valueFontColor"=> "#FFFFFF",
        	  "showHoverEffect"=> "1",
            "rotateValues"=> "1",
            "showXAxisLine"=> "1",
            "xAxisLineThickness"=> "1",
            "xAxisLineColor"=> "#999999",
            "showAlternateHGridColor"=> "0",
            "legendBgAlpha"=> "0",
            "legendBorderAlpha"=> "0",
            "legendShadow"=> "0",
            "legendItemFontSize"=> "10",
            "legendItemFontColor"=> "#666666",
            "theme"=> "candy",
            "bgColor" => "#222222"
          	)
         	);

        	// creating array for categories object
        	$categoryArray=array();
        	$dataseries1=array();
        	$dataseries2=array();
        	
            // pushing category array values
        	while($row = mysqli_fetch_array($result)) {				
				    array_push($categoryArray, array(
					  "label" => $row["area"]
					)
				);

				array_push($dataseries1, array(
					"value" => $row["Raccolta"]
					//"value" => number_format($row["Raccolta"], 0, ',', '.')
					) 
				);
			
				array_push($dataseries2, array(
					"value" => $row["Impieghi"]
					//"value" => number_format($row["Impieghi"], 0, ',', '.')
				    )
				);
    
        	}
        	
    	$arrData["categories"]=array(array("category"=>$categoryArray));

			// creating dataset object
			$arrData["dataset"] = array(array("seriesName"=> "Raccolta", "data"=>$dataseries1), array("seriesName"=> "Impieghi", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrData);
      // chart object
      $msChart = new FusionCharts("mscolumn2d", "chart1" , "100%", "400", "chart-container", "json", $jsonEncodedData);
      $msChart->render();
			 
   }

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// RACCOLTA - FILIALI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery2 = "SELECT cast(codFil as unsigned) as Filiale, round(sum(Raccolta)/1000) as Raccolta
                FROM view_volumi_filiali
                WHERE codFil <> '' 
                AND Raccolta <> 0
                GROUP BY codFil 
                ORDER BY cast(codFil as unsigned) ";

$result2 = $dbhandle->query($strQuery2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result2) {
  $arrData2 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Raccolta per Filiali",
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
 
  $arrData2["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row2 = mysqli_fetch_array($result2)) {
    array_push($arrData2["data"], array(
      "label" => $row2["Filiale"],
      "value" => $row2["Raccolta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData2);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChart2" , "100%", "450", "raccFil", "json", $jsonEncodedData);
$Chartcolumn2d->render();

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// IMPIEGHI - FILIALI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery3 = "SELECT cast(codFil as unsigned) as Filiale, round((sum(Impieghi)*-1)/1000) as Impieghi
                FROM view_volumi_filiali
                WHERE codFil <> '' 
                AND Impieghi <> 0
                GROUP BY codFil 
                ORDER BY cast(codFil as unsigned) ";

$result3 = $dbhandle->query($strQuery3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result3) {
  $arrData3 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Impieghi per Filiali",
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
 
  $arrData3["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row3 = mysqli_fetch_array($result3)) {
    array_push($arrData3["data"], array(
      "label" => $row3["Filiale"],
      "value" => $row3["Impieghi"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData3);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChart3" , "100%", "450", "impFil", "json", $jsonEncodedData);
$Chartcolumn2d->render();

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI - VOLUMI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Dettaglio VOLUMI per Filiale
       </div>';

$dettaglioA = " SELECT 
                sum(qta) as qta, cast(codFil as unsigned) as Filiale, 
                sum(NumAzTot) as NumAzTot, sum(ValNomTot) as ValNomTot, sum(NumCC) as NumCC, 
                sum(NumCarte) as NumCarte, sum(NumTitPol) as NumTitPol, sum(NumHB) as NumHB, 
                round(sum(Raccolta)/1000) as Raccolta, round((sum(Impieghi)*-1)/1000) as Impieghi 
                FROM view_volumi_filiali
                WHERE codFil <> '' 
                GROUP BY codFil
                ORDER BY cast(codFil as unsigned)
                ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
            <td align="right">Filiale</td>
            <td align="right">Qtà Soci</td>
            <td align="right">Nr.Azioni</td>
            <td align="right">Val.Nominale</td>
            <td align="right">Nr.C/C</td>
            <td align="right">Nr.Carte</td>
            <td align="right">Nr.Tit/Pol</td>
            <td align="right">Nr.HB</td>
            <td align="right">Raccolta</td>
            <td align="right">Impieghi</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    $tab_dettaglioA .=  "<tr>
            <td>".$row_areeA['Filiale']."</td>
            <td align='right'>".number_format($row_areeA['qta'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_areeA['NumAzTot'],0,',','.')."</td>            
            <td align='right'>".number_format($row_areeA['ValNomTot'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_areeA['NumCC'],0,',','.')."</td>            
            <td align='right'>".number_format($row_areeA['NumCarte'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_areeA['NumTitPol'],0,',','.')."</td>
            <td align='right'>".number_format($row_areeA['NumHB'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_areeA['Raccolta'],0,',','.')."</td>
            <td align='right'>".number_format($row_areeA['Impieghi'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dettaglioA .=  '  </table>';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO AREE - RACCOLTA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglio =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Dettaglio VOLUMI per Area
       </div>';

$dettaglio1 = " SELECT 
                sum(qta) as qta, area, 
                sum(NumAzTot) as NumAzTot, sum(ValNomTot) as ValNomTot, sum(NumCC) as NumCC, 
                sum(NumCarte) as NumCarte, sum(NumTitPol) as NumTitPol, sum(NumHB) as NumHB, 
                round(sum(Raccolta)/1000) as Raccolta, round((sum(Impieghi)*-1)/1000) as Impieghi 
                FROM view_volumi_aree
                WHERE area <> '' 
                GROUP BY area
                ORDER BY area
                ";

$result_aree2 = $dbhandle->query($dettaglio1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglio .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
            <td align="right">Area</td>
            <td align="right">Qtà Soci</td>
            <td align="right">Nr.Azioni</td>
            <td align="right">Val.Nominale</td>
            <td align="right">Nr.C/C</td>
            <td align="right">Nr.Carte</td>
            <td align="right">Nr.Tit/Pol</td>
            <td align="right">Nr.HB</td>
            <td align="right">Raccolta</td>
            <td align="right">Impieghi</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree2 = mysqli_fetch_array($result_aree2)) {

    $tab_dettaglio .=  "<tr>
            <td>".$row_aree2['area']."</td>
            <td align='right'>".number_format($row_aree2['qta'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_aree2['NumAzTot'],0,',','.')."</td>            
            <td align='right'>".number_format($row_aree2['ValNomTot'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_aree2['NumCC'],0,',','.')."</td>            
            <td align='right'>".number_format($row_aree2['NumCarte'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_aree2['NumTitPol'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['NumHB'],0,',','.')."&nbsp;</td>
            <td align='right'>".number_format($row_aree2['Raccolta'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['Impieghi'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dettaglio .=  '  </table>';
    
   

?>

<center>
 <div id="chart-container">Aree - Raccolta e Impieghi</div></center>
 <center>
 <div id="raccFil">Filiali - Raccolta</div></center>
  <center>
 <div id="impFil">Filiali - Impieghi</div></center>
 
 <table border="0" align="center" width="100%">
  <tr>     
       <td valign="top" width="49%"><?php echo $tab_dettaglio; ?></td>
       <td valign="top" width="2%">&nbsp;&nbsp;</td>
       <td valign="top" width="49%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
 
   </body>
</html>

<?php
      // closing db connection
      $dbhandle->close();
?>