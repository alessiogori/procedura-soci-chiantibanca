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

if (!isset($_GET['key']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
    }
    else
    {
     if (!isset($_GET['area']))   
     {    
     $condizionefiliale = 'AND filiale = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
     }
     else
     {    
     $condizionefiliale = 'AND filiale in ('.$_GET['key'].')';
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     }
    }

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// Verifica data file di inventario
$select_datafile = "SELECT * FROM tab_ultimo_caricamento WHERE fonte = 'tab_xls_esclusioni' " ;
$qry_datafile = $dbhandle->query($select_datafile) 
                or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_datafile = mysqli_fetch_array($qry_datafile)){ 
  $datafile = $dati_datafile['caricamento'];
}

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Soci Esclusi '.$titolofiliale.'</h2>
  		<p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione attuale di tutte le richieste di esclusione (art.6, art.14) deliberate dal CdA.<br>
        Inventario aggiornato al '.$datafile.'</p>
	</div>
';

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


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TORTA - RICHIESTE DA LIQUIDARE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery_esc1 = "SELECT count(*) as qta, 'Escluso art.6' as Tipo 
                    FROM tab_xls_esclusioni
                    WHERE Escluso_art_6 = 'S'
                    and MovimentoSIcra not in ('RE','ES')
                    ".$condizionefiliale."
                    UNION
                    SELECT count(*) as qta, 'Escluso art.14' as Tipo 
                    FROM tab_xls_esclusioni
                    WHERE Escluso_art_14 = 'S'
                    and MovimentoSIcra not in ('RE','ES')
                    ".$condizionefiliale."
                    UNION
                    SELECT count(*) as qta, 'Escluso Sofferenze' as Tipo 
                    FROM tab_xls_esclusioni
                    WHERE Escluso_x_Passaggio_a_Sofferenze = 'S' 
                    and MovimentoSIcra not in ('RE','ES')
                    ".$condizionefiliale."";

$result_esc1 = $dbhandle->query($strQuery_esc1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

if ($result_esc1) {
  // creating an associative array to store the chart attributes        
  $arrData_esc1 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Tipologia Soci esclusi - da liquidare",
      //"subcaption" => "in percentuale",
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

 
   $arrData_esc1["data"] = array();

  // iterating over each data and pushing it into $arrData array
  while ($row_esc1 = mysqli_fetch_array($result_esc1)) {
    array_push($arrData_esc1["data"], array(
      "label" => $row_esc1['Tipo'],
      "value" => $row_esc1["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData_esc1);
}

//$Chartcolumn2d = new FusionCharts("column2d", "myChartX" , "100%", "450", "aree", "json", $jsonEncodedData);
//$Chartcolumn2d->render();
$Chartpie2d = new FusionCharts("pie2d", "myChart_esc1" , "50%", "450", "esc1", "json", $jsonEncodedData);
$Chartpie2d->render();


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - RICHIESTE DA LIQUIDARE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery3 = "SELECT count(*) as qta, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as data  
                  FROM tab_xls_esclusioni
                  WHERE Escluso_x_Passaggio_a_Sofferenze <> 'S' 
                  and MovimentoSIcra not in ('RE','ES')
                  AND substring(Data_Richiesta,7,4) >= 2020
                  ".$condizionefiliale."
                  GROUP BY CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) 
                  ORDER BY 2 ASC";

$result3 = $dbhandle->query($strQuery3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result3) {
  $arrData3 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Trend richieste di Esclusione ricevute - da liquidare",
      "subcaption" => "Escluse Sofferenze",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
      "valueFontBold" => "0",
      //"valueFontColor" => "".$valueFontColor."",
      "showValues"=> "1",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",      
      "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
    )
  );
 
/*
        ],
        "trendlines": [{
            "line": [{
                "startvalue": "18500",
                "color": "#29C3BE",
                "displayvalue": "Average{br}weekly{br}footfall",
                "valueOnRight": "1",
                "thickness": "2"

*/
  $arrData3["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row3 = mysqli_fetch_array($result3)) {
    array_push($arrData3["data"], array(
      "label" => $row3["data"],
      "value" => $row3["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData3);
}

$Chartline = new FusionCharts("line", "myChart_esc2" , "100%", "450", "esc2", "json", $jsonEncodedData);
$Chartline->render();



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - RICHIESTE ESCLUSIONE COMPLESSIVE RICEVUTE (liquidate e non)
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery4 = "SELECT count(*) as qta, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) as data  
                  FROM tab_xls_esclusioni
                  WHERE Escluso_x_Passaggio_a_Sofferenze <> 'S' 
                  AND substring(Data_Richiesta,7,4) >= 2020
                  ".$condizionefiliale."
                  GROUP BY CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) 
                  ORDER BY 2 ASC";

$result4 = $dbhandle->query($strQuery4) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result4) {
  $arrData4 = array(
    "chart" => array(
      "theme" => "".$tema."",
      "caption" => "Trend richieste di Esclusione ricevute - storico",
      "subcaption" => "Escluse Sofferenze",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
      "valueFontBold" => "0",
      "valueFontColor" => "".$valueFontColor."",
      "showValues"=> "1",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "".$bgcolor."",      
      "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
    )
  );
 
/*
        ],
        "trendlines": [{
            "line": [{
                "startvalue": "18500",
                "color": "#29C3BE",
                "displayvalue": "Average{br}weekly{br}footfall",
                "valueOnRight": "1",
                "thickness": "2"

*/
  $arrData4["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row4 = mysqli_fetch_array($result4)) {
    array_push($arrData4["data"], array(
      "label" => $row4["data"],
      "value" => $row4["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData4);
}

$Chartline = new FusionCharts("line", "myChart_esc4" , "100%", "450", "esc4", "json", $jsonEncodedData);
$Chartline->render();

     
?>

<table border="0" align="center">
  <tr>     
       <td ><div id="esc1"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr> 
       <td ><div id="esc2"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr> 
       <td c><div id="esc4"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<!-- <center><h4>Aggiungere link per elenco</h4></center> -->

<?php
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// ESPORTAZIONE ELENCO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
					
    // Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $select_file = "SELECT *
					FROM tab_xls_esclusioni
					WHERE MovimentoSIcra not in ('RE','ES')
					".$condizionefiliale."
    				ORDER BY Nominativo
                    " ;
    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/esclusioni".$filiale.".csv", "w");
    $contenutofile .= "Filiale;NAG;Nominativo;NumAzioni;ValoreNominale;DataRichiesta;Note;EsclusoArt6;EsclusoArt14;EsclusoSofferenza;Data_InizioDecadenza;DataCDA;DataLettera\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['Filiale'].";".
			$cnt_file['NAG'].";".
			$cnt_file['Nominativo'].";".
			$cnt_file['Numero_Azioni'].";".			
			$cnt_file['Valore_Nominale'].";".
			$cnt_file['Data_Richiesta'].";".
			$cnt_file['Note_Motivazioni'].";".
			$cnt_file['Escluso_art_6'].";".
			$cnt_file['Escluso_art_14'].";".
			$cnt_file['Escluso_x_Passaggio_a_Sofferenze'].";".
			$cnt_file['Data_InizioDecadenza'].";".
            $cnt_file['CDA'].";".
			$cnt_file['Data_Lettera']."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/esclusioni'.$filiale.'.csv">Scarica il dettaglio completo</a>';


// closing database connection      
$dbhandle->close();				
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>