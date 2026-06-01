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
        <title>Stats Cessioni</title>
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

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// Verifica data file di inventario
$select_datafile = "SELECT * FROM tab_ultimo_caricamento WHERE fonte = 'tab_xls_cessionibanca' " ;
$qry_datafile = $dbhandle->query($select_datafile) 
                or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_datafile = mysqli_fetch_array($qry_datafile)){ 
  $datafile = $dati_datafile['caricamento'];
}  

echo '
	<div class="alert alert-dismissible alert-warning">
  		<h2 class="alert-heading">Cessioni a Banca (in essere)</h2>
  		<p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione storica (aggiornata al '.$adesso.') di tutte le Cessioni a Banca ricevute.<br>
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
// CESSIONI TOTALI A BANCA - LIMIT 20 - VALORE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery = "SELECT count(*) as qta, Filiale, sum(Valore_Nominale) as Valore_Nominale 
                  FROM tab_xls_cessionibanca
                  WHERE 
                  -- AND Totale_Parziale = 'T'
                  Rimborsato <> 'S' 
                  GROUP BY Filiale 
                  ORDER BY 3 desc LIMIT 20; ";

$result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result) {
  $arrData = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Prime 20 Filiali in ordine di importo",
      //"subcaption" => "Situazione al ".$adesso,
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "1",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",      
      "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
    )
  );
 
  $arrData["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row = mysqli_fetch_array($result)) {
    array_push($arrData["data"], array(
      "label" => $row["Filiale"],
      "value" => $row["Valore_Nominale"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChart0" , "45%", "450", "cess0", "json", $jsonEncodedData);
$Chartcolumn2d->render();


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// CESSIONI TOTALI A BANCA - LIMIT 20 - QUANTITA'
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery1 = "SELECT count(*) as qta, Filiale 
                  FROM tab_xls_cessionibanca 
                  WHERE Rimborsato <> 'S'
                  GROUP BY Filiale 
                  ORDER BY 1 desc LIMIT 20; ";

$result1 = $dbhandle->query($strQuery1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result1) {
  $arrData1 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Prime 20 Filiali in ordine di qtà richieste",
      // "subcaption" => "Situazione al ".$adesso,
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
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
      "label" => $row1["Filiale"],
      "value" => $row1["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData1);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChart1" , "50%", "450", "cess1", "json", $jsonEncodedData);
$Chartcolumn2d->render();


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// CESSIONI TOTALI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery2 = "SELECT count(*) as qta, Filiale, sum(Valore_Nominale) as Valore_Nominale 
                  FROM tab_xls_cessionibanca 
                  WHERE Rimborsato <> 'S'
                  GROUP BY Filiale ";

$result2 = $dbhandle->query($strQuery2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result2) {
  $arrData2 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Filiali per importo",
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
      "value" => $row2["Valore_Nominale"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData2);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChart2" , "100%", "450", "cess2", "json", $jsonEncodedData);
$Chartcolumn2d->render();



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TREND PER ANNO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery3 = "SELECT count(*) as qta, CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2))  as AnnoMeseRichiesta
                  FROM tab_xls_cessionibanca 
                  GROUP BY CONCAT(substring(Data_Richiesta,7,4), substring(Data_Richiesta,4,2)) 
                  ORDER BY 2 ASC";

$result3 = $dbhandle->query($strQuery3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
if ($result3) {
  $arrData3 = array(
    "chart" => array(
      "theme" => "".$tema."",
      "caption" => "Trend qtà per mese/anno",
      //"subcaption" => "Situazione al ".$adesso,
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "rotateValues" => "0",
      "rotateNames" => "1" ,
      "labeldisplay" => "rotate",
      "valueFontBold" => "0",
      "showValues"=> "1",
      "subcaptionFontColor" => "#646464",
      "valueFontColor" => "".$valueFontColor."",
      "bgColor" => "".$bgcolor."",      
      "paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
    )

    /*,
    "trendlines" => array(
            "line" => array(
                "startvalue" => "2000",
                "valueOnRight" => "1",
                "color" => "#29C3BE",
                "thickness" => "2",
                "displayvalue" => "Monthly Target"
          )
    )
    */
  );
 
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

$Chartline = new FusionCharts("line", "myChart3" , "100%", "450", "cess3", "json", $jsonEncodedData);
$Chartline->render();

     
?>

<table border="0" align="center">
  <tr>
       <td><div id="cess0"><!-- Fusion Charts will render here--></div> </td>
       <td><div id="cess1"><!-- Fusion Charts will render here--></div> </td>
  </tr>
  <tr>     
       <td colspan="2"><div id="cess2"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr>     
       <td colspan="2"><div id="cess3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<!-- <center><h4>Aggiungere link per elenco</h4></center> -->

<?php
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo '<a name="dettaglio">';
include("cessioni_dettaglio.php");

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// ESPORTAZIONE ELENCHI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
					
    // Preparo il file per l'estrazione in CSV
    $contenutocessioni = '';
    $select_cessioni = " SELECT c.NAG, c.nominativo, cast(a.valore_azioni as decimal(10,2)) as ValoreTotaleAzioni, cast(replace(c.Valore_Nominale,',','.') as decimal(10,2))  as Cessione_in_corso, c.Totale_Parziale, 
                        (cast(a.valore_azioni as decimal(10,2)) - cast(replace(c.Valore_Nominale,',','.') as decimal(10,2))) as Residuo, c.Data_Richiesta,
                        p.desc_filiale, p.area, s.STATO_NAG,
                        DESC_STATUS as STATUS, 
                        TOT_RACCOLTA, TOT_ACCORDATO, TOT_UTILIZZATO
                        FROM tab_xls_cessionibanca as c, sds_soci as s, sds_soci_certificati as a, tab_psw as p, view_impieghiraccolta as v
                        WHERE c.Rimborsato <> 'S'
                        AND a.valore_azioni <> 0
                        AND c.NAG = s.NAG
                        AND c.NAG = v.NAG
                        AND s.IDSOCIO = a.IDSOCIO
                        AND c.Filiale = CAST(p.filiale AS UNSIGNED)
                        ORDER BY c.Data_Richiesta 
                    " ;
    
    $qry_cessioni = $dbhandle->query($select_cessioni) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/cessioni_in_essere.csv", "w");
    $contenutocessioni .= "StatoNag;NAG;Nominativo;DataRichiesta;ValoreNominale;CessioneInCorso;Totale_Parziale;Residuo;Filiale;Area;Status;TotRaccolta;TotAccordato;TotUtilizzato\n";
    while($cnt_cessioni = mysqli_fetch_array($qry_cessioni)){ 

    // decodifica STATO_NAG 
    if ($cnt_cessioni['STATO_NAG'] == '0')  { $statonag = 'Cliente Potenziale'; $colorenag = 'lightyellow';} 
        elseif ($cnt_cessioni['STATO_NAG'] == '1') { $statonag = 'Cliente con rapporti'; $colorenag = 'lightgreen';} 
        else   { $statonag = 'Ex Cliente'; $colorenag = 'red';} 

        $contenutocessioni .= $statonag.";".$cnt_cessioni['NAG'].";".$cnt_cessioni['nominativo'].";".$cnt_cessioni['Data_Richiesta'].";".$cnt_cessioni['ValoreTotaleAzioni'].";".$cnt_cessioni['Cessione_in_corso'].";".$cnt_cessioni['Totale_Parziale'].";".$cnt_cessioni['Residuo'].";".$cnt_cessioni['desc_filiale'].";".$cnt_cessioni['area'].";".$cnt_cessioni['STATUS'].";".$cnt_cessioni['TOT_RACCOLTA'].";".$cnt_cessioni['TOT_ACCORDATO'].";".$cnt_cessioni['TOT_UTILIZZATO']."\n";
    }
    fwrite($myfile, $contenutocessioni);
    fclose($myfile);

/*
    // CESSIONI IN ESSERE CON CONTI CORRENTI CHIUSI
    // Preparo il file per l'estrazione in CSV
    $contenutocessioni2 = '';
    $select_cessioni2 = "   SELECT c.Filiale, c.cag, c.Nominativo, v.CC_num, c.Data_Richiesta, c.ID, c.Numero_Azioni, c.Valore_Nominale, c.Totale_Parziale
                            FROM `tab_xls_cessioni` as c LEFT JOIN tab_volumi as v 
                            ON c.CAG = v.cag
                            WHERE c.Cessione_a_banca = 'S'
                            AND c.Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VB')
                            AND (v.CC_num is null OR v.CC_num = 0)
                            GROUP BY c.cag, c.Nominativo, v.CC_num
                            ORDER BY c.ID
                        " ;
    
    $qry_cessioni2 = $dbhandle->query($select_cessioni2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile2 = fopen("../tmp/cessioni_in_essere_cc_chiusi.csv", "w");
    $contenutocessioni2 .= "ATTENZIONE: verificare che i nominativi qui sotto abbiano effettivamente i rapporti chiusi\n";
    $contenutocessioni2 .= "Filiale;CAG;Nominativo;CC_Num;DataRichiesta;NumeroAzioni;CessioneInCorso;Totale_Parziale;ID\n";
    while($cnt_cessioni2 = mysqli_fetch_array($qry_cessioni2)){ 
        $contenutocessioni2 .=  $cnt_cessioni2['Filiale'].";".$cnt_cessioni2['cag'].";".$cnt_cessioni2['Nominativo'].";".
                                $cnt_cessioni2['CC_num'].";".$cnt_cessioni2['Data_Richiesta'].";".$cnt_cessioni2['Numero_Azioni'].";".
                                $cnt_cessioni2['Valore_Nominale'].";".$cnt_cessioni2['Totale_Parziale'].";".
                                $cnt_cessioni2['ID']."\n";
    }
    fwrite($myfile2, $contenutocessioni2);
    fclose($myfile2);
*/
    echo '<br><center>
    		<!-- <a class="btn btn-outline-warning" href="../tmp/cessioni_ipotesirimborso.csv">Scarica PREVISIONALE di rimborso</a> -->
            &nbsp;
            <a class="btn btn-outline-warning" href="../tmp/cessioni_in_essere.csv">Scarica dettaglio delle Cessioni</a>
            &nbsp;
            <!-- <a class="btn btn-outline-warning" href="../tmp/cessioni_in_essere_cc_chiusi.csv">Scarica elenco Cessioni con C/C chiusi</a> -->';



// closing database connection      
$dbhandle->close();				
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>