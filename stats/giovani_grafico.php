<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
$NOME_SCRIPT = 'GIOVANI UNDER 35';
$TITOLO = 'Giovani Under 35';

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
        <title>Stats Giovani</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';


// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');        

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
$adesso = date("d.m.Y");

// Calcolo data di partenza Under 35
// *****************************************************************************
$date = new DateTime();                   // empty for now or pass any date string as param
$date->modify('- 35 years');        // 35 anni indietro da oggi
$AnnoMesedipartenzaU30 = $date->format('Ymd');       // formato output AAAAMMDD



if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '20250101';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("Ymd", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }


// Controllo se la richiesta arriva   
if (!isset($_GET['filiale']))
    {$condizionefiliale = '';
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
     $condizionefiliale = ' AND FIL_ANAGRAFICA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND FIL_ANAGRAFICA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['filiale'];
     $rif = 'Area';
     }
    }

/*
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO .'</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=aaaammgg (eventuale &dataout=aaaammgg)</p>
      </div>
';
*/
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO .'</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.'</p>
      </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER FASCE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary" align="center">
        <strong>Giovani con età tra 18 e 35 anni
       </div>';

$dettaglioA = " 
                SELECT
                    eta, 
                    sum(qta_socio_si + qta_socio_no) as qta,
                    sum(qta_socio_si) as qta_socio_si,
                    (sum(qta_socio_si) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_socio_si,
                    sum(qta_socio_no) as qta_socio_no,
                    (sum(qta_socio_no) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_socio_no,
                    sum(qta_rapporti_si) as qta_rapporti_si,
                    (sum(qta_rapporti_si) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_rapporti_si,
                    sum(qta_rapporti_no) as qta_rapporti_no,
                    (sum(qta_rapporti_no) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_rapporti_no
                FROM view_under35
                WHERE eta between 18 and 35
                ".$condizionefiliale."
                GROUP BY eta
                ORDER BY 1
                 ";

           
$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table  class="table table-bordered table-hover"  border="1" valign="top" width="80%" align="center">
        <tr class="table-secondary">
          <td align="left"  width="20%">&nbsp;Età</td>
          <td align="right" width="20%">Qtà Anagrafiche&nbsp;</td>
          <td align="right" width="20%">con Rapporti&nbsp;</td>
          <td align="right" width="20%">senza Rapporti&nbsp;</td>
          <td align="right" width="20%">di cui già Soci Banca&nbsp;</td>          
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {
      
    //if ( $row_areeA['eta']  == 17 ) {$param = "filiale=".$filiale."&eta=17";}
    if ( $row_areeA['eta']  == 18 ) {$param = "filiale=".$filiale."&eta=18";}
    if ( $row_areeA['eta']  == 19 ) {$param = "filiale=".$filiale."&eta=19";}
    if ( $row_areeA['eta']  == 20 ) {$param = "filiale=".$filiale."&eta=20";}
    if ( $row_areeA['eta']  == 21 ) {$param = "filiale=".$filiale."&eta=21";}
    if ( $row_areeA['eta']  == 22 ) {$param = "filiale=".$filiale."&eta=22";}
    if ( $row_areeA['eta']  == 23 ) {$param = "filiale=".$filiale."&eta=23";}
    if ( $row_areeA['eta']  == 24 ) {$param = "filiale=".$filiale."&eta=24";}
    if ( $row_areeA['eta']  == 25 ) {$param = "filiale=".$filiale."&eta=25";}
    if ( $row_areeA['eta']  == 26 ) {$param = "filiale=".$filiale."&eta=26";}
    if ( $row_areeA['eta']  == 27 ) {$param = "filiale=".$filiale."&eta=27";}
    if ( $row_areeA['eta']  == 28 ) {$param = "filiale=".$filiale."&eta=28";}
    if ( $row_areeA['eta']  == 29 ) {$param = "filiale=".$filiale."&eta=29";}
    if ( $row_areeA['eta']  == 30 ) {$param = "filiale=".$filiale."&eta=30";}
    if ( $row_areeA['eta']  == 31 ) {$param = "filiale=".$filiale."&eta=31";}
    if ( $row_areeA['eta']  == 32 ) {$param = "filiale=".$filiale."&eta=32";}
    if ( $row_areeA['eta']  == 33 ) {$param = "filiale=".$filiale."&eta=33";}
    if ( $row_areeA['eta']  == 34 ) {$param = "filiale=".$filiale."&eta=34";}
    if ( $row_areeA['eta']  == 35 ) {$param = "filiale=".$filiale."&eta=35";}
    
    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='20%'><h6>&nbsp;".$row_areeA['eta']."&nbsp; &nbsp;</td>
                          <td align='right' width='20%'>
                          ".number_format($row_areeA['qta'],0,',','.')."</a>&nbsp;
                          </td>                          
                          <td align='right' width='20%'>
                          <a href='../giovani_lista.php?".$param."&rapporti=si&socio=' onclick='return ray.ajax()'>
                          ".number_format($row_areeA['qta_rapporti_si'],0,',','.')."</a>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_rapporti_si'],2,',','.')."%&nbsp;</td>  
                          <td align='right' width='20%'>
                          <a href='../giovani_lista.php?".$param."&rapporti=no&socio=' onclick='return ray.ajax()'>
                          ".number_format($row_areeA['qta_rapporti_no'],0,',','.')."</a>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_rapporti_no'],2,',','.')."%&nbsp;</td> 
                          </td>  
                          <td align='right' width='20%'>
                          <a style='color:orange;' href='../giovani_lista.php?".$param."&rapporti=si&socio=si' onclick='return ray.ajax()'>
                          ".number_format($row_areeA['qta_socio_si'],0,',','.')."</a>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_socio_si'],2,',','.')."%&nbsp;</td> 
                          </td> 
                         </tr>";
  // chiudo ciclo WHILE  
  }

$dettaglioA_TOT = " 
                SELECT
                    sum(qta_socio_si + qta_socio_no) as qta,
                    sum(qta_rapporti_si) as qta_rapporti_si,
                    sum(qta_rapporti_no) as qta_rapporti_no,
                    sum(qta_socio_si) as qta_socio_si
                FROM view_under35
                WHERE eta between 18 and 35
                ".$condizionefiliale."
                 ";
$result_areeA_TOT = $dbhandle->query($dettaglioA_TOT) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
  while ($row_areeA_TOT = mysqli_fetch_array($result_areeA_TOT)) {
    $tab_dettaglioA .=  "<tr class='table-secondary'>
                          <td align='right' width='20%'><h6>&nbsp;TOTALE &nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_rapporti_si'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_rapporti_no'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_socio_si'],0,',','.')."</a>&nbsp;
                          </td>                          
                         </tr>";
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';


    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // GRAFICO TOTALI PER FASCE
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    $dettaglioA2 = " 
                SELECT
                    eta,
                    sum(qta_socio_si + qta_socio_no) as qta
                FROM view_under35
                WHERE eta between 18 and 35
                ".$condizionefiliale."
                GROUP BY eta
                ORDER BY 1
                 ";  
    $result_areeA2 = $dbhandle->query($dettaglioA2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

      if ($result_areeA2) {
      $arrData = array(
      "chart" => array(
        "theme" => "candy",  // fusion - candy
        //"caption" => "Fasce Quote Soci senza richieste in corso",
        //"subcaption" => "(Plafond permettendo)",
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
     
      $arrData["data"] = array();
      while ($row2 = mysqli_fetch_array($result_areeA2)) { 
       array_push($arrData["data"], array(
          "label" => $row2["eta"],
          "value" => $row2["qta"]
        ));
      }
      $jsonEncodedData = json_encode($arrData);
    }
    $Chartpie2d = new FusionCharts("pie2d", "myChart" , "100%", "450", "eta", "json", $jsonEncodedData);
    $Chartpie2d->render();

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// ESPORTAZIONE ELENCO NUOVI SOCI UNDER 30 FATTI DOPO 01.10.2020
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
/*          
    // Preparo il file per l'estrazione in CSV
    $contenutounder30 = '';
    $select_under30 = "SELECT
                        codFil as Filiale
                        ,cag as CAG
                        ,concat (int1Socio, ' ', int2Socio) as Intestazione
                        ,nAzTot as NumAzioni
                        ,CAST(nominaleAzTot AS UNSIGNED)  as ValoreNominale
                        ,STR_TO_DATE(dataAmmiss, '%d/%m/%Y') as DataAmmissione
                        ,STR_TO_DATE(dataEntrata, '%d/%m/%Y') as DataEntrata
                        ,STR_TO_DATE(dataNasc, '%d/%m/%Y') as DataNascita
                        ,DATEDIFF(now(), STR_TO_DATE(dataNasc, '%d/%m/%Y')) as GGDIFF
                        FROM tab_soci_as37
                        WHERE DATEDIFF(now(), STR_TO_DATE(dataNasc, '%d/%m/%Y')) <= 10950
                        AND STR_TO_DATE(dataEntrata, '%d/%m/%Y') >= '2020-10-01'
                        AND statoVAL not in ('E','S')
                        AND tipoContropVAL = 11000
                        ".$condizionefiliale1."
                    " ;
    
    $qry_under30 = $dbhandle->query($select_under30) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfileunder30 = fopen("../tmp/under30_".$filiale.".csv", "w");
    $contenutounder30 .= "Filiale;CAG;Nominativo;NumAzioni;ValoreNominale;DataAmmissione;DataEntrata;DataNascita;GG_Diff\n";
    while($cnt_under30 = mysqli_fetch_array($qry_under30)){ 
        $contenutounder30 .= $cnt_under30['Filiale'].";".$cnt_under30['CAG'].";".$cnt_under30['Intestazione'].";".$cnt_under30['NumAzioni'].";".$cnt_under30['ValoreNominale'].";".$cnt_under30['DataAmmissione'].";".$cnt_under30['DataEntrata'].";".$cnt_under30['DataNascita'].";".$cnt_under30['GGDIFF']."\n";
    }
    fwrite($myfileunder30, $contenutounder30);
    fclose($myfileunder30);    

*/
    
?>

<table border="0" align="center">
  <tr>     
       <td><div id="eta"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<?php

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// ESPORTAZIONE ELENCO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
                    
    // Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $select_file = "select
                    fil_anagrafica as Filiale, g.nag as NAG, 
                    concat(trim(intestazione_a), ' ', trim(intestazione_b)) as Nominativo,
                    procedura, valore_dato_cnt as email
                    from sds_soci_under35 as g left join sds_soci_daticontatto as c
                    on g.nag = c.nag
                    where g.sociobanca = 'SI'
                    ".$condizionefiliale."
                    and tipo_dato_cnt = 'MAIL'
                    ORDER BY intestazione_a, intestazione_b
                    " ;
    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/under35_mailing_".$filiale.".csv", "w");
    $contenutofile .= "Filiale;NAG;Nominativo;Procedura;Email\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
            $cnt_file['Filiale'].";".
            $cnt_file['NAG'].";".
            $cnt_file['Nominativo'].";".
            $cnt_file['procedura'].";".         
            $cnt_file['email']."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/under35_mailing_'.$filiale.'.csv">Scarica il CSV per il mailing</a>';


    
// closing database connection      
$dbhandle->close();       
?>

    <center>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>


