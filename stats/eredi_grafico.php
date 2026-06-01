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

if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/2022';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

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
     $condizionefiliale = 'AND filiale_capofila = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
     }
     else
     {    
     $condizionefiliale = 'AND filiale_capofila in ('.$_GET['key'].')';
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     }
    }
    
echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Soci deceduti '.$titolofiliale.'</h2>
  		<p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione attuale (aggiornata al '.$adesso.') di tutte le richieste di liquidazione e intestazione avanzate da eredi.</p>
	</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER ANNO - INIZIO DECADENZA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery1 = "SELECT count(*) as qta,  concat(substring(data_movimento_ID,7,4),substring(data_movimento_ID,4,2)) as data   
                  FROM view_decessi
                  WHERE 
                  str_to_date(data_movimento_ID,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                  ".$condizionefiliale."
                  GROUP BY data 
                  ORDER BY 2 ASC";       

$result1 = $dbhandle->query($strQuery1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result1) {
  $arrData1 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Trend Decessi",
      "subcaption" => "Inizio Decadenza registrata in Sicra",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "showValues"=> "1",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
      "valueFontBold" => "0",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",      
      "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
    )
  );
 

  $arrData1["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row1 = mysqli_fetch_array($result1)) {
    array_push($arrData1["data"], array(
      "label" => $row1["data"],
      "value" => $row1["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData1);
}

$Chartline = new FusionCharts("line", "myChart_eredi1" , "100%", "450", "eredi1", "json", $jsonEncodedData);
$Chartline->render();

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER ANNO - LIQUIDATI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery2 = "SELECT count(*) as qta,  concat(substring(data_movimento_RL,7,4),substring(data_movimento_RL,4,2)) as data   
                  FROM view_decessi
                  WHERE 
                  str_to_date(data_movimento_RL,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                  ".$condizionefiliale."
                  GROUP BY data 
                  ORDER BY 2 ASC";       

$result2 = $dbhandle->query($strQuery2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result2) {
  $arrData2 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Trend Decessi",
      "subcaption" => "Liquidati",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "showValues"=> "1",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
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
      "label" => $row2["data"],
      "value" => $row2["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData2);
}

$Chartline = new FusionCharts("line", "myChart_eredi2" , "100%", "450", "eredi2", "json", $jsonEncodedData);
$Chartline->render();

     
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER ANNO - CON SUBENTRO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery3 = "SELECT count(*) as qta,  concat(substring(data_movimento_RS,7,4),substring(data_movimento_RS,4,2)) as data   
                  FROM view_decessi
                  WHERE 
                  str_to_date(data_movimento_RS,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                  ".$condizionefiliale."
                  GROUP BY data 
                  ORDER BY 2 ASC";       

$result3 = $dbhandle->query($strQuery3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result3) {
  $arrData3 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Trend Decessi",
      "subcaption" => "Con Subentro",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "showValues"=> "1",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
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
      "label" => $row3["data"],
      "value" => $row3["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData3);
}

$Chartline = new FusionCharts("line", "myChart_eredi3" , "100%", "450", "eredi3", "json", $jsonEncodedData);
$Chartline->render();

?>

<table border="0" align="center" width="90%">
  <tr>     
       <td ><div id="eredi1"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr>     
       <td ><div id="eredi2"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr>     
       <td ><div id="eredi3"><!-- Fusion Charts will also be rendered here--></div></td>
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
    $select_file = "SELECT
                      IDSOCIO,
                      NAG,
                      Nominativo,
                      NumeroAzioni,
                      ValoreTotaleAzioni,
                      Data_Decesso,
                      data_uscita,
                      ctipmovuscita,
                      data_movimento_ID,
                      data_movimento_RS,
                      data_movimento_RL,
                      Filiale_capofila,
                      desc_filiale,
                      area
                    FROM
                      view_decessi
                    WHERE NAG <> 0
                    AND str_to_date(data_uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                    ".$condizionefiliale."
                    GROUP BY IDSOCIO
                    order by Filiale_capofila, Nominativo
                    " ;
    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/eredi".$filiale.".csv", "w");
    $contenutofile .= "Filiale;IDSOCIO;NAG;Nominativo;NumAzioni;ValoreNominale;DataDecesso;DataUscita;MovimUscita;DataMov_ID;DataMov_RS;DataMov_RL;Filiale;DescFiliale;Area\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['Filiale_capofila'].";".
      $cnt_file['IDSOCIO'].";".
			$cnt_file['NAG'].";".
			$cnt_file['Nominativo'].";".
			$cnt_file['NumeroAzioni'].";".			
			$cnt_file['ValoreTotaleAzioni'].";".
			$cnt_file['Data_Decesso'].";".
			$cnt_file['data_uscita'].";".
			$cnt_file['ctipmovuscita'].";".
			$cnt_file['data_movimento_ID'].";".
			$cnt_file['data_movimento_RS'].";".
      $cnt_file['data_movimento_RL'].";".
      $cnt_file['Filiale_capofila'].";".
      $cnt_file['desc_filiale'].";".
			$cnt_file['area'].";"."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/eredi'.$filiale.'.csv">Scarica il dettaglio completo</a>';


// closing database connection      
$dbhandle->close();			
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>