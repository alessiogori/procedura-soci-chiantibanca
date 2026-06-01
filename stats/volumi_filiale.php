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

if (!isset($_GET['key']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
    }
    else
    {$condizionefiliale = 'AND filiale = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
    }

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Volumi Soci '.$titolofiliale.'</h2>
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
$strQuery = "   SELECT codFil, round(sum(Raccolta)/1000) as Raccolta, round((sum(Impieghi)*-1)/1000) as Impieghi 
                FROM view_volumi_filiali
                WHERE codFil = ".$filiale."
                GROUP BY codFil";

 	$result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result) {
        	
	$arrData = array(
        "chart" => array(
        	"caption"=> "Raccolta e Impieghi per Filiale ".$filiale." (in milioni)",
        	"captionFontSize" => "24",
            "subcaptionFontSize" => "20",
            "xAxisname"=> "Filiale",
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
					  "label" => $row["codFil"]
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
                WHERE codFil = ".$filiale." 
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
            <td align='right'>".$row_areeA['Filiale']."</td>
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

?>

<center>
 <div id="chart-container">Filiale - Raccolta e Impieghi</div></center>

 <table border="0" align="center" width="100%">
  <tr>     
       <td valign="top" width="49%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
 
 <br><center><h5>Attenzione: dati indicativi, non contabili.</h5></center>
   </body>
</html>

<?php
      // closing db connection
      $dbhandle->close();
?>