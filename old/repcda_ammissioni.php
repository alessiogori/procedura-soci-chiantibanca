<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
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
        <title>RepCDA Ammissioni</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
          @import "../css/fontawesome-free/css/all.min.css";
        </style> 

        <body><br><br>
        ';

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");

if ( $_GET['annomese'] =="" ) {$annomese = 202101;} else {$annomese = $_GET['annomese'];}

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Report Progressione Nuove Ammissioni Soci</h2>
        <small>Fonte: SIB AS37 - tab_soci_as37</small>
	</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - QUANTITA'
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$contenutofile = '';

$trend = "
            SELECT 
                anno as AnnoMeseEntrata,
                ROUND(SUM(qta),0) AS TOTALE_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CHIANTI-FIRENZE' AND Tipo = 'IN'
                        THEN qta
                        ELSE 0
                    END
                ),0) AS qta_CF_IN,
                ROUND(SUM(
                    CAS
                        WHEN area = 'SIENA'  AND Tipo = 'IN'
                        THEN qta
                        ELSE 0
                    END
                ),0) AS qta_SI_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'PISTOIA-TIRRENO' AND Tipo = 'IN'
                        THEN qta
                        ELSE 0
                    END
                ),0) AS qta_PT_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CAMPI-PRATO' AND Tipo = 'IN'
                        THEN qta
                        ELSE 0
                    END
                ),0) AS qta_CP_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CENTRO IMPRESE' AND Tipo = 'IN'
                        THEN qta
                        ELSE 0
                    END
                ),0) AS qta_CI_IN,

                ROUND(SUM(qta),0) AS TOTALE_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CHIANTI-FIRENZE' AND Tipo = 'OUT'
                        THEN (qta)
                        ELSE 0
                    END
                ),0) AS qta_CF_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'SIENA' AND Tipo = 'OUT'
                        THEN (qta)
                        ELSE 0
                    END
                ),0) AS qta_SI_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'PISTOIA-TIRRENO' AND Tipo = 'OUT'
                        THEN (qta)
                        ELSE 0
                    END
                ),0) AS qta_PT_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CAMPI-PRATO' AND Tipo = 'OUT'
                        THEN (qta)
                        ELSE 0
                    END
                ),0) AS qta_CP_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CENTRO IMPRESE' AND Tipo = 'OUT'
                        THEN (qta)
                        ELSE 0
                    END
                ),0) AS qta_CI_OUT

            FROM
                view_storico_flussi
            WHERE anno >= ".$annomese."                
            GROUP BY
                anno
                ";

$result_trend = $dbhandle->query($trend) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    $myfile = fopen("../tmp/repcda_ammissioni_uscite.csv", "w");
    $contenutofile .= "AnnoMeseEvento;TOTALE_IN;qta_CF_IN;qta_SI_IN;qta_PT_IN;qta_CP_IN;qta_CI_IN;TOTALE_OUT;qta_CF_OUT;qta_SI_OUT;qta_PT_OUT;qta_CP_OUT;qta_CI_OUT\n";

  if ($result_trend) {
        	
	$arrDataTrend = array(
        "chart" => array(
        	"caption"=> "Ingressi nuovi Soci per AREA (mese/anno)",
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
            "valueFontSize" => "12",
            "exportEnabled" => "1"
          	)
         	);

        	// creating array for categories object
        	$categoryArray=array();
        	$dataseries1=array();
        	$dataseries2=array();
        	$dataseries3=array();
        	$dataseries4=array();

            $dataseries5=array();
            $dataseries6=array();
            $dataseries7=array();
            $dataseries8=array();

            // pushing category array values
        	while($rowTrend = mysqli_fetch_array($result_trend)) {				
				@array_push($categoryArray, array(
					  "label" => $rowTrend["AnnoMeseEntrata"]
					)
				);
				@array_push($dataseries1, array(
					"value" => $rowTrend["qta_CF_IN"]
					) 
				);			
				@array_push($dataseries2, array(
					"value" => $rowTrend["qta_SI_IN"]
				    )
				);			
				@array_push($dataseries3, array(
					"value" => $rowTrend["qta_PT_IN"]
				    )
				);
				@array_push($dataseries4, array(
					"value" => $rowTrend["qta_CP_IN"]
				    )
				);            
                @array_push($dataseries5, array(
                    "value" => $rowTrend["qta_CI_IN"]
                    )
                );            
                @array_push($dataseries6, array(
                    "value" => $rowTrend["qta_CF_OUT"]
                    )
                );	
                @array_push($dataseries7, array(
                    "value" => $rowTrend["qta_SI_OUT"]
                    )
                );  
                @array_push($dataseries8, array(
                    "value" => $rowTrend["qta_PT_OUT"]
                    )
                );  
                @array_push($dataseries9, array(
                    "value" => $rowTrend["qta_CP_OUT"]
                    )
                );  			
                @array_push($dataseries10, array(
                    "value" => $rowTrend["qta_CI_OUT"]
                    )
                );              
				
				$contenutofile .= 
    			$rowTrend["AnnoMeseEntrata"].";".
                $rowTrend["TOTALE_IN"].";".
    			$rowTrend["qta_CF_IN"].";".
    			$rowTrend["qta_SI_IN"].";".
    			$rowTrend["qta_PT_IN"].";".
    			$rowTrend["qta_CP_IN"].";".
                $rowTrend["qta_CI_IN"].";".
                $rowTrend["TOTALE_OUT"].";".
                $rowTrend["qta_CF_OUT"].";".
                $rowTrend["qta_SI_OUT"].";".
                $rowTrend["qta_PT_OUT"].";".
                $rowTrend["qta_CP_OUT"].";".
                $rowTrend["qta_CI_OUT"]."\n";


      	}

  //  fwrite($myfile, $contenutofile);
  //  fclose($myfile);
        	
    	$arrDataTrend["categories"]=array(array("category"=>$categoryArray));

			// creating dataset object
			$arrDataTrend["dataset"] = array(array("seriesName"=> "Area CHIANTI-FIRENZE", "data"=>$dataseries1), 
                                       array("seriesName"=> "Area SIENA", "data"=>$dataseries2), 
                                       array("seriesName"=> "Area PISTOIA-TIRRENO", "data"=>$dataseries3), 
                                       array("seriesName"=> "Area CAMPI-PRATO", "data"=>$dataseries4),
                                       array("seriesName"=> "Area CENTRO IMPRESE", "data"=>$dataseries5)
                                  );

      $jsonEncodedData = json_encode($arrDataTrend);
      // chart object
      $msChart = new FusionCharts("msline", "myChart1" , "98%", "400", "rikarea", "json", $jsonEncodedData);
      $msChart->render();
			 
   }


?>

<table border="0" align="center">
  <tr>     
       <td colspan="2"><div id="rikarea"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<?php    

// TABELLA DI DETTAGLIO

    echo '<table border="1" width="90%" valign="top" align="center">
            <tr class="table-secondary table-hover">
              <td rowspan="2" align="center" width="4%">AnnoMese Evento</td>
              <td align="center" colspan="6" width="48%" style="background-color:green;">ENTRATE</td>
              <td align="center" colspan="6" width="48%" style="background-color:orange;color:black;">USCITE</td>
            </tr>
            <tr class="table-secondary">
              <td align="right" style="background-color:green;">TOTALE</td>
              <td align="right">Area<br>CHIANTI<br>FIRENZE</td>
              <td align="right">Area<br>SIENA</td>
              <td align="right">Area<br>PISTOIA<br>TIRRENO</td>
              <td align="right">Area<br>CAMPI<br>PRATO</td>
              <td align="right">Area<br>CENTRO<br>IMPRESE</td>

              <td align="right" style="background-color:orange;color:black;">TOTALE</td>
              <td align="right">Area<br>CHIANTI<br>FIRENZE</td>
              <td align="right">Area<br>SIENA</td>
              <td align="right">Area<br>PISTOIA<br>TIRRENO</td>
              <td align="right">Area<br>CAMPI<br>PRATO</td>
              <td align="right">Area<br>CENTRO<br>IMPRESE</td>
            </tr>';

    $result_trend2 = $dbhandle->query($trend) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
    while($rowTrend2 = mysqli_fetch_array($result_trend2)) {              
    echo '
            <tr>
              <td align="center" width="4%">'.$rowTrend2["AnnoMeseEntrata"].'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["TOTALE_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CF_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_SI_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_PT_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CP_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CI_IN"],0,',','.').'</td>

              <td align="right" width="8%">'.number_format($rowTrend2["TOTALE_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CF_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_SI_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_PT_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CP_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend2["qta_CI_OUT"],0,',','.').'</td>
            </tr>';
    }   

// Calcolo i totali di fine riga
    $trend_TOTALI = "
            SELECT 
                ROUND(SUM(qtaEntrata),0) AS TOTALE_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CHIANTI-FIRENZE'
                        THEN qtaEntrata
                        ELSE 0
                    END
                ),0) AS qta_CF_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'SIENA'
                        THEN qtaEntrata
                        ELSE 0
                    END
                ),0) AS qta_SI_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'PISTOIA-TIRRENO'
                        THEN qtaEntrata
                        ELSE 0
                    END
                ),0) AS qta_PT_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CAMPI-PRATO'
                        THEN qtaEntrata
                        ELSE 0
                    END
                ),0) AS qta_CP_IN,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CENTRO IMPRESE'
                        THEN qtaEntrata
                        ELSE 0
                    END
                ),0) AS qta_CI_IN,

                ROUND(SUM(qtaUscita),0) AS TOTALE_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CHIANTI-FIRENZE'
                        THEN (qtaUscita)
                        ELSE 0
                    END
                ),0) AS qta_CF_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'SIENA'
                        THEN (qtaUscita)
                        ELSE 0
                    END
                ),0) AS qta_SI_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'PISTOIA-TIRRENO'
                        THEN (qtaUscita)
                        ELSE 0
                    END
                ),0) AS qta_PT_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CAMPI-PRATO'
                        THEN (qtaUscita)
                        ELSE 0
                    END
                ),0) AS qta_CP_OUT,
                ROUND(SUM(
                    CASE
                        WHEN area = 'CENTRO IMPRESE'
                        THEN (qtaUscita)
                        ELSE 0
                    END
                ),0) AS qta_CI_OUT

            FROM
                view_storico_flussi
            WHERE anno >= ".$annomese."                
                ";

    $result_trend_TOTALI = $dbhandle->query($trend_TOTALI) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
    while($rowTrend_TOTALI = mysqli_fetch_array($result_trend_TOTALI)) {              

    echo '  <tr class="table-secondary">
              <td></td>
              <td align="right" width="8%" style="background-color:green;">'.number_format($rowTrend_TOTALI["TOTALE_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CF_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_SI_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_PT_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CP_IN"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CI_IN"],0,',','.').'</td>

              <td align="right" width="8%" style="background-color:orange;color:black;">'.number_format($rowTrend_TOTALI["TOTALE_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CF_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_SI_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_PT_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CP_OUT"],0,',','.').'</td>
              <td align="right" width="8%">'.number_format($rowTrend_TOTALI["qta_CI_OUT"],0,',','.').'</td>
            </tr>';

    // INDICI USCITE / ENTRATE
    $Banca  = number_format(($rowTrend_TOTALI["TOTALE_OUT"] / $rowTrend_TOTALI["TOTALE_IN"])*100,2,',','.');
    $AreaCF = number_format(($rowTrend_TOTALI["qta_CF_OUT"] / $rowTrend_TOTALI["qta_CF_IN"])*100,2,',','.');
    $AreaSI = number_format(($rowTrend_TOTALI["qta_SI_OUT"] / $rowTrend_TOTALI["qta_SI_IN"])*100,2,',','.');
    $AreaPT = number_format(($rowTrend_TOTALI["qta_PT_OUT"] / $rowTrend_TOTALI["qta_PT_IN"])*100,2,',','.');
    $AreaCP = number_format(($rowTrend_TOTALI["qta_CP_OUT"] / $rowTrend_TOTALI["qta_CP_IN"])*100,2,',','.');
    $AreaCI = number_format(($rowTrend_TOTALI["qta_CI_OUT"] / $rowTrend_TOTALI["qta_CI_IN"])*100,2,',','.');

    echo '  <tr class="table-dark">
              <td align="right" ><i class="fas fa-chart-line fa-1x text-gray-300 col-auto" title="Uscite/Entrate*100"></i>Indici</td>
              <td align="right" width="8%">'.$Banca.' %</td>
              <td align="right" width="8%">'.$AreaCF.' %</td>
              <td align="right" width="8%">'.$AreaSI.' %</td>
              <td align="right" width="8%">'.$AreaPT.' %</td>
              <td align="right" width="8%">'.$AreaCP.' %</td>
              <td align="right" width="8%">'.$AreaCI.' %</td>

              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
         </tr>';
    }

echo '            
    </table><br><br>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// Parte 2 - Dettaglio Eventi IN/OUT per AREA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------

$listArea = "
            select area, 
            if(anno<=> null, 'TOTALE', anno) as anno, 
            sum(qtaEntrata) as Entrate, sum(qtaUscita) as Uscite,
            ( sum(qtaEntrata) - sum(qtaUscita) ) as Diff,
            round(( sum(qtaUscita) / sum(qtaEntrata) *100 ),2) as Perc
            FROM view_storico_flussi
            where anno >= ".$annomese."
            group by area, anno WITH ROLLUP
            ";

$result_listArea = $dbhandle->query($listArea) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    $contenutofile .= "\n\nArea;AnnoMeseEvento;Entrate;Uscite;Diff;Perc\n";

            while($rowlistArea = mysqli_fetch_array($result_listArea)) {    

                $contenutofile .= 
                $rowlistArea["area"].";".
                $rowlistArea["anno"].";".
                $rowlistArea["Entrate"].";".
                $rowlistArea["Uscite"].";".
                $rowlistArea["Diff"].";".
                $rowlistArea["Perc"]."\n";

        }


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// Parte 3 - Dettaglio Eventi IN/OUT per AREA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------

$listFiliali = "
            select area, filiale, desc_filiale,
            if(anno<=> null, 'TOTALE', anno) as anno, 
            sum(qtaEntrata) as Entrate, sum(qtaUscita) as Uscite,
            ( sum(qtaEntrata) - sum(qtaUscita) ) as Diff,
            round(( sum(qtaUscita) / sum(qtaEntrata) *100 ),2) as Perc
            FROM view_storico_flussi
            where anno >= ".$annomese."
            group by area, filiale,  anno WITH ROLLUP
            ";

$result_listFiliali = $dbhandle->query($listFiliali) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    $contenutofile .= "\n\nArea;Filiale;AnnoMeseEvento;Entrate;Uscite;Diff;Perc\n";

            while($rowlistFiliali = mysqli_fetch_array($result_listFiliali)) {    

                $contenutofile .= 
                $rowlistFiliali["area"].";".
                $rowlistFiliali["filiale"]." ".$rowlistFiliali["desc_filiale"].";".
                $rowlistFiliali["anno"].";".
                $rowlistFiliali["Entrate"].";".
                $rowlistFiliali["Uscite"].";".
                $rowlistFiliali["Diff"].";".
                $rowlistFiliali["Perc"]."\n";

        }

// -------------------------------------------------------------------------------
// FINE - CHIUDO CSV
// -------------------------------------------------------------------------------

    fwrite($myfile, $contenutofile);
    fclose($myfile);


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - FASCE ETA'
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
    $contenutofile2 = '';
    
//if ( !isset($_GET['annomese']) ) {$annomese = 202001;} else {$annomese = $_GET['annomese'];}

$trend2 = "
            SELECT  Fascia, Area, AnnoMeseEvento, sum(qta) as qta
            FROM view_fasce_area
            WHERE AnnoMeseEvento >= ".$annomese."
            GROUP BY Fascia, Area, AnnoMeseEvento
            ORDER BY 1 ASC";

$result_trend2 = $dbhandle->query($trend2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    $myfile2 = fopen("../tmp/repcda_ammissioni_fascerichieste.csv", "w");
    $contenutofile2 .= "Fascia;Area;AnnoMeseEvento;qta\n";
            while($rowTrend2 = mysqli_fetch_array($result_trend2)) {    

                $contenutofile2 .= 
                $rowTrend2["Fascia"].";".
                $rowTrend2["Area"].";".
                $rowTrend2["AnnoMeseEvento"].";".
                $rowTrend2["qta"]."\n";

        }
        
    fwrite($myfile2, $contenutofile2);
    fclose($myfile2);



    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/repcda_ammissioni_uscite.csv">Scarica il dettaglio Ammissioni e Uscite</a>
    <br>
    <br><a class="btn btn-outline-warning" href="../tmp/repcda_ammissioni_fascerichieste.csv">Scarica il dettaglio Fasce Eta\'</a>
    <br>
    <br><small>File: O:\COMUNITY BANKING\_Statistiche\RepCDA - Entrate e Uscite.xlsx</small></center> ';

// closing database connection      
$dbhandle->close();				
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>