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
    
echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Cessioni a Banca '.$titolofiliale.'</h2>
  		<p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione attuale (aggiornata al '.$adesso.') di tutte le richieste di cessione a banca.</p>
	</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER ANNO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery3 = "SELECT count(*) as qta, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))  as AnnoMeseRichiesta 
                  FROM tab_xls_cessioni 
                  WHERE Cessione_a_banca = 'S'
                  ".$condizionefiliale."
                  GROUP BY CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) 
                  ORDER BY 2 ASC";

$result3 = $dbhandle->query($strQuery3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result3) {
  $arrData3 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Trend qtà Cessioni a Banca per mese/anno",
      //"subcaption" => "Situazione al ".$adesso,
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
      "label" => $row3["AnnoMeseRichiesta"],
      "value" => $row3["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData3);
}

$Chartline = new FusionCharts("line", "myChart3" , "100%", "450", "amm1", "json", $jsonEncodedData);
$Chartline->render();

     
?>

<table border="0" align="center">
  <tr>     
       <td colspan="2"><div id="amm1"><!-- Fusion Charts will also be rendered here--></div></td>
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
					FROM tab_xls_cessioni  
					WHERE Nominativo <> ''
					".$condizionefiliale."
					AND Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VB','VC')
    				ORDER BY Nominativo
                    " ;
    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/cessioni".$filiale.".csv", "w");
    $contenutofile .= "Filiale;CAG;Conto;Nominativo;NumAzioni;ValoreNominale;DataRichiesta;TotaleParziale;CessioneBanca;CessioneSocio;DataOperaz;NoteOperaz;DataLettera\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['Filiale'].";".
			$cnt_file['CAG'].";".
			$cnt_file['Conto'].";".
			$cnt_file['Nominativo'].";".
			$cnt_file['Numero_Azioni'].";".	
			$cnt_file['Valore_Nominale'].";".
			$cnt_file['Data_Richiesta'].";".						
			$cnt_file['Totale_Parziale'].";".			
			$cnt_file['Cessione_a_Banca'].";".
			$cnt_file['Cessione_a_Socio'].";".
			$cnt_file['Data'].";".
			$cnt_file['Note_AO08'].";".
			$cnt_file['Data_Lettera']."\n";
			//$cnt_file['ID'].\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/cessioni'.$filiale.'.csv">Scarica il dettaglio completo delle cessioni da eseguire</a>';


// closing database connection      
$dbhandle->close();			
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>