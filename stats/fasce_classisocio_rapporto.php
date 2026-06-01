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
        <title>Stats Fasce</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");
$adesso_anno = date("Y");

$data1 = new DateTime('2019-01-01');
$data2 = new DateTime(date("Y-m-d"));
$mesi = $data2->diff($data1); 
$numeromesi = (($mesi->y) * 12) + ($mesi->m);
// echo $howeverManyMonths;

// Controllo se è stato richiesto un periodo particolare
if (!isset($_GET['periodo']))
    {
   $datarichiesta = $adesso_anno - 1;    // conteggio da un anno indietro rispetto ad oggi
    }
    else
    {
   $datarichiesta = $_GET['periodo'];
  }

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
     $condizionefiliale = 'AND FILIALE_CAPOFILA = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = 'AND FILIALE_CAPOFILA in ('.$_GET['key'].')';
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     }
    }

echo '
  <div class="alert alert-dismissible alert-warning">
      <h2 class="alert-heading">Classi Soci per anzianità di rapporto '.$titolofiliale.'</h2>
  </div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


// -------------------------------------------------------------------------------
// CREAZIONE VISTA TEMPORANEA
// -------------------------------------------------------------------------------
$truncatetable = mysqli_query($dbhandle,"TRUNCATE TMP_SOCI_ANZRAPPORTO") or die(mysqli_error($dbhandle));;

$createtable_1 = "
                SELECT
                     ANAG_NAG.FILIALE_CAPOFILA as FILIALE_CAPOFILA,
                     ANAG_RAPPORTI.NAG as NAG, 
                     case TIPO_SOGGETTO
                         when '1' then 'PF'
                         when '3' then 'PG'
                         end as TIPO,
                    '1' as QTA,
                     SUBSTR(min(data_censimento),1,4) as ANNORAPPORTOPIUVECCHIO 
                FROM
                    ANAG_NAG, ANAG_RAPPORTI
                WHERE
                    ANAG_NAG.SOCIO_ISTITUTO = '1'
                AND ANAG_RAPPORTI.NAG = ANAG_NAG.NAG
                GROUP BY ANAG_NAG.FILIALE_CAPOFILA, ANAG_RAPPORTI.NAG, TIPO  , QTA 
                ";

          $result_createtable = odbc_exec($connect, $createtable_1);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_ANZRAPPORTO
                VALUES 
               (
                '".$dati_createtable->FILIALE_CAPOFILA."'
                ,'".$dati_createtable->NAG."'
                ,'".$dati_createtable->TIPO."'
                ,'".$dati_createtable->QTA."'
                ,'".$dati_createtable->ANNORAPPORTOPIUVECCHIO."'
                )
                ";
            //echo $insert_createtable;      
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER ANNI DI ANZIANITA' (RAPPORTO)
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Classi Soci per anni anzianità di rapporto (per Data Censimento)
       </div>';

// Interrogo la view
$dettaglioA = " 
              SELECT 
                IFNULL(TIPO, 'TOTALE') AS TIPO, 
                ROUND(SUM(QTA),0) AS TOTALE_POSIZIONI,
                ROUND(SUM(
                    CASE
                        WHEN ANNORAPPORTOPIUVECCHIO >= ($adesso_anno - 1)
                        THEN QTA
                        ELSE 0
                    END
                ),0) AS qta_meno_1_anno,
                ROUND(SUM(
                    CASE
                        WHEN ANNORAPPORTOPIUVECCHIO between ($adesso_anno - 3) and ($adesso_anno - 2)
                        THEN QTA 
                        ELSE 0
                    END
                ),0) AS qta_tra_2_e_3_anni,
                ROUND(SUM(
                    CASE
                        WHEN ANNORAPPORTOPIUVECCHIO between ($adesso_anno - 6) and ($adesso_anno - 4)
                        THEN QTA 
                        ELSE 0
                    END
                ),0) AS qta_tra_4_e_6_anni,
                ROUND(SUM(
                    CASE
                        WHEN ANNORAPPORTOPIUVECCHIO between ($adesso_anno - 9) and ($adesso_anno - 7)
                        THEN QTA 
                        ELSE 0
                    END
                ),0) AS qta_tra_7_e_9_anni,
                ROUND(SUM(
                    CASE
                        WHEN ANNORAPPORTOPIUVECCHIO <= ($adesso_anno - 10)
                        THEN QTA 
                        ELSE 0
                    END
                ),0) AS qta_oltre_10_anni
            FROM
                tmp_soci_anzrapporto
            WHERE TIPO in ('PF','PG')
                ".$condizionefiliale."
            GROUP BY
                TIPO WITH ROLLUP
                 ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left"  width="22%">Tipo</td>
          <td align="right" width="13%">Soci Totali</td>
          <td align="right" width="13%">Da meno 1 anno</td>
          <td align="right" width="13%">Tra 2 e 3 anni</td>  
          <td align="right" width="13%">Tra 4 e 6 anni</td>  
          <td align="right" width="13%">Tra 7 e 9 anni</td>  
          <td align="right" width="13%">Oltre 10 anni</td>
        </tr>';

  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    $Perc1 = " <i style='color:lightgray;font-size:13px;'>(".
                number_format((($row_areeA['qta_meno_1_anno'] / $row_areeA['TOTALE_POSIZIONI'])*100),2,',','.')
             ."%)</i>";
    $Perc2 = " <i style='color:lightgray;font-size:13px;'>(".
                number_format((($row_areeA['qta_tra_2_e_3_anni'] / $row_areeA['TOTALE_POSIZIONI'])*100),2,',','.')
             ."%)</i>";
    $Perc3 = " <i style='color:lightgray;font-size:13px;'>(".
                number_format((($row_areeA['qta_tra_4_e_6_anni'] / $row_areeA['TOTALE_POSIZIONI'])*100),2,',','.')
             ."%)</i>";
    $Perc4 = " <i style='color:lightgray;font-size:13px;'>(".
                number_format((($row_areeA['qta_tra_7_e_9_anni'] / $row_areeA['TOTALE_POSIZIONI'])*100),2,',','.')
             ."%)</i>";
    $Perc5 = " <i style='color:lightgray;font-size:13px;'>(".
                number_format((($row_areeA['qta_oltre_10_anni'] / $row_areeA['TOTALE_POSIZIONI'])*100),2,',','.')
             ."%)</i>";

    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='22%'>".$row_areeA['TIPO']."&nbsp;</td>
                          <td align='right' width='13%'>".number_format($row_areeA['TOTALE_POSIZIONI'],0,',','.')."&nbsp;</td>
                          <td align='right' width='13%'>".number_format($row_areeA['qta_meno_1_anno'],0,',','.').$Perc1."</td>
                          <td align='right' width='13%'>".number_format($row_areeA['qta_tra_2_e_3_anni'],0,',','.').$Perc2."</td>
                          <td align='right' width='13%'>".number_format($row_areeA['qta_tra_4_e_6_anni'],0,',','.').$Perc3."</td>
                          <td align='right' width='13%'>".number_format($row_areeA['qta_tra_7_e_9_anni'],0,',','.').$Perc4."</td>
                          <td align='right' width='13%'>".number_format($row_areeA['qta_oltre_10_anni'],0,',','.').$Perc5."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';


?>
<br>

<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
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