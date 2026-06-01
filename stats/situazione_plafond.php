<?php
//////////////////////////////////////////////////////////////////
// SITUAZIONE SOCI
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'SITUAZIONE PLAFOND';
$TITOLO = 'Situazione PLAFOND Soci';
$anno = date("Y");
$plafond_iniziale = 400000;

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

// Includo i dati di connessione
include("../config/_config.php");
include("../config/_functions.php");

// including FusionCharts PHP wrapper
include("../graph/fusioncharts.php"); 

echo '<html>
        <head>
        <script type="text/javascript" src="../js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="../js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        <title>'.$TITOLO.'</title>
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
// --------------------------------------------------------------------
$adesso = date("d.m.Y");
$adesso_anno = date("Y");


if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = $inizioanno;
            $dataMediaPlafond = date("d/m/Y", strtotime($_GET['datain']."-6 months"));            // 6 mesi indietro per calcolare la media delle ammissioni
            //echo $dataMediaPlafond;
        }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {
            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
            $dataMediaPlafond = date("d/m/Y", strtotime($_GET['datain']."-6 months"));            // 6 mesi indietro per calcolare la media delle ammissioni
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
     $condizionefiliale = ' AND ANAG_NAG.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $condizionefiliale3 = ' AND FILIALE_CAPOFILA in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND ANAG_NAG.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in ('.$_GET['filiale'].')';
     $condizionefiliale3 = ' AND FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }

/*
if (@$_SERVER["HTTP_REFERER"] != 'http://10.197.139.22:8080/soci/soci_auth.php') {
    $aggiungi_titolo = ' - Verifica cessione Socio '.$_GET['nominativo'];
}
else
{
    $aggiungi_titolo = '';
}
*/
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO .'</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
      </div>
';
// var_dump($inizioanno);

// -----------------------------------------------------------------
// Tabella MASTER
// -----------------------------------------------------------------

echo '<table border="0" align="center">
        <tr>
            <td valign="top" width="30%">';

// -----------------------------------------------------------------
// Estrazione dei valori CONTABILI - 2881 CAPITALE
// -----------------------------------------------------------------

$select_somma_2881 =   "
            SELECT 
                'In Entrata' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'A' 
            AND COD_RAPP = 2881 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'In Uscita' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'D' 
            AND COD_RAPP = 2881 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'SALDO' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE COD_RAPP = 2881 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
                        ";

      // Tabella 2881 CAPITALE
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-light">
                <td align="center" colspan="3"><b>2881.990.100 - CAPITALE SOCIALE</td>
              </tr>
              <tr class="table-light">
                <td align="center" width="50%"><small>Tipologia</td>
                <td align="right" width="50%"><small>Saldo Contabile</td>
              </tr>';

$res_somma_2881 = odbc_exec($connect, $select_somma_2881);
  while($dati_somma_2881 = odbc_fetch_object($res_somma_2881)) {

    if ($dati_somma_2881->TIPO == 'SALDO') 
            {$tr = " class='table-secondary'";
             $saldo2881 = $dati_somma_2881->IMPORTO ; } 
            else {$tr = '';
             $saldo2881 = 0;}

    echo "<tr ".$tr.">
            <td height='40' align='left'><small>".$dati_somma_2881->TIPO."</td>
            <td align='right'><small>".number_format($dati_somma_2881->IMPORTO,2,',','.')."</td>
          </tr>
        ";
  }

// -----------------------------------------------------------------
// Divisorio
// -----------------------------------------------------------------
        echo '</table>

            </td>
              <td width="1%">&nbsp;&nbsp;</td>
              <td  valign="top" width="30%">';

// -----------------------------------------------------------------
// Estrazione dei valori CONTABILI - 2885 SOVRAPPREZZO
// -----------------------------------------------------------------

$select_somma_2885 =   "
            SELECT 
                'In Entrata' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'A' 
            AND COD_RAPP = 2885 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'In Uscita' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'D' 
            AND COD_RAPP = 2885 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'SALDO' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE COD_RAPP = 2885
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
                        ";

      // Tabella 2885 SOVRAPPREZZO
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-light">
                <td align="center" colspan="3"><b>2885.990.100 - SOVRAPPREZZO</td>
              </tr>
              <tr class="table-light">
                <td align="center" width="50%"><small>Tipologia</td>
                <td align="right" width="50%"><small>Saldo Contabile</td>
              </tr>';

$res_somma_2885 = odbc_exec($connect, $select_somma_2885);
  while($dati_somma_2885 = odbc_fetch_object($res_somma_2885)) {

    if ($dati_somma_2885->TIPO == 'SALDO') 
            {$tr = " class='table-secondary'";
             $saldo2885 = $dati_somma_2885->IMPORTO ; } 
            else {$tr = '';
             $saldo2885 = 0;}

    echo "<tr ".$tr.">
            <td height='40' align='left'><small>".$dati_somma_2885->TIPO."</td>
            <td align='right'><small>".number_format($dati_somma_2885->IMPORTO,2,',','.')."</td>
          </tr>
        ";
  } 

// -----------------------------------------------------------------
// Divisorio
// -----------------------------------------------------------------
        echo '</table>

            </td>
              <td width="1%">&nbsp;&nbsp;</td>
              <td  valign="top" width="30%">';


// -----------------------------------------------------------------
// Estrazione dei valori CONTABILI - 1770 FONDO RIACQUISTO
// -----------------------------------------------------------------

$select_somma_1770 =   "
            SELECT 
                'In Entrata' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'A' 
            AND COD_RAPP = 1770 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'In Uscita' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE SEGNO = 'D' 
            AND COD_RAPP = 1770 
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
            UNION
            SELECT 
                'SALDO' as TIPO, 
                COD_RAPP , FILIALE , NUM_RAPP , 
                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
            FROM CG_MOVIMENTI_CONTABILI 
            WHERE COD_RAPP = 1770
            AND FILIALE = 990 
            AND NUM_RAPP = 100 
            AND STORNO <> 'A'
            AND DATA_CONT >= '".$_GET['datain']."'
            AND DATA_CONT <= '".$_GET['dataout']."'
            GROUP BY TIPO, COD_RAPP, FILIALE, NUM_RAPP
                        ";

      // Tabella 1770 FONDO RIACQUISTO
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-light">
                <td align="center" colspan="3"><b>1770.990.100 - FONDO RIACQUISTO AZIONI PROPRIE</td>
              </tr>
              <tr class="table-light">
                <td align="center" width="50%"><small>Tipologia</td>
                <td align="right" width="50%"><small>Saldo Contabile</td>
              </tr>';

$res_somma_1770 = odbc_exec($connect, $select_somma_1770);
  while($dati_somma_1770 = odbc_fetch_object($res_somma_1770)) {

    if ($dati_somma_1770->TIPO == 'SALDO') 
            {$tr = " class='table-secondary'";
             $saldo1770 = $dati_somma_1770->IMPORTO ; } 
            else {$tr = '';
             $saldo1770 = 0;}

    echo "<tr ".$tr.">
            <td height='40' align='left'><small>".$dati_somma_1770->TIPO."</td>
            <td align='right'><small>".number_format($dati_somma_1770->IMPORTO,2,',','.')."</td>
          </tr>
        ";
  }

// -----------------------------------------------------------------
// Chiusura tabella MASTER
// -----------------------------------------------------------------
        echo '</table>

            </td>
            </tr>
            <tr>
            <td colspan="5">';


     echo ' 
            <table class="table table-bordered table-hover" width="60%" align="center" cellspacing="0">
              <tr class="table-info">
                <td height="40" align="center" colspan="3">CALCOLO DISPONIBILITA\' PLAFOND '.$anno.'</td>
              </tr>';

              // RECUPERO VALORI LIQUIDAZIONI DA FARE PER L'ANNO IN CORSO
              // stesso script di "liquidazioni_grafico.php"
              // -----------------------------------------------------------------

                // senza le Sofferenze
                $dettaglioA = " 
                                SELECT
                                    count(*) as qta,
                                    sum(importo) as Importo
                                from TAB_DECADUTI_NONLIQUIDATI 
                                where filiale <> 999  
                                ".$condizionefiliale."
                                    and importo <> 0 
                                    and DATA_DECADENZA <= '$adesso_anno"."-01-01"."'
                                    and Sofferenza <> 'S'
                                ";
//echo $dettaglioA;
                $dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
                 
                if ($dbhandle -> connect_error) {
                    exit("There was an error with your connection: ".$dbhandle -> connect_error);
                }

                $result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
                while ($row_areeA = mysqli_fetch_array($result_areeA)) {

                    $totale_liquidazioni_dafare = round($row_areeA['Importo']);
            
                }

                // comprese le Sofferenze
                $dettaglioB = " 
                                SELECT
                                    count(*) as qta,
                                    sum(importo) as Importo
                                from TAB_DECADUTI_NONLIQUIDATI 
                                where filiale <> 999  
                                ".$condizionefiliale."
                                    and importo <> 0 
                                    and DATA_DECADENZA <= '$adesso_anno"."-01-01"."'
                                ";

                $dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
                 
                if ($dbhandle -> connect_error) {
                    exit("There was an error with your connection: ".$dbhandle -> connect_error);
                }

                $result_areeB = $dbhandle->query($dettaglioB) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
                while ($row_areeB = mysqli_fetch_array($result_areeB)) {

                    $totale_liquidazioni_dafare_COMPLETO = round($row_areeB['Importo']);
            
                }

                // COSTRUZIONE TABELLA DISPONIBILITA'
                // ----------------------------------

            $saldoconticontabili = @$saldo2881+@$saldo2885+@$saldo1770;
            
            $disp_attuale        = $plafond_iniziale + $saldoconticontabili;
                if ($disp_attuale < 0) 
                    {$disp_attuale_style = ' <span style = "color:red;"> ';} 
                else {$disp_attuale_style = '';}

            $disp_senzaliquidaz  = $disp_attuale - $totale_liquidazioni_dafare;


                        // Ultimo Aggiornamento 
                        $select_aggto = " SELECT caricamento
                                          FROM tab_ultimo_caricamento 
                                          WHERE tipo = 'SICRA'
                                          AND fonte = 'sicra_decaduti_nonliquidati'
                                          " ;
                        $qry_aggto = mysqli_query($connection, $select_aggto);
                        while($aggto = mysqli_fetch_array($qry_aggto)){ 
                            $data_aggto = $aggto['caricamento']; 
                              }

                echo "<tr>
                        <td height='40' align='left'>PLAFOND INIZIALE </td>
                        <td align='right'>".number_format($plafond_iniziale,0,',','.')."</td>
                        <td align='right'><small>&nbsp; P</td>
                     </tr>
                     <tr>
                        <td height='40' align='left'>Saldo della somma dei Conti Contabili &nbsp;&nbsp;<small>
                        (".number_format(@$saldo2881,2,',','.')." + ".number_format(@$saldo2885,2,',','.')." + ".number_format(@$saldo1770,2,',','.').")</small>
                         </td>
                        <td align='right'>".number_format($saldoconticontabili,0,',','.')."</td>
                        <td align='right'><small>&nbsp; S</td>
                     </tr>
                     <tr class='table-success' style='border-bottom:2px solid green;'>
                        <td height='40' align='left'>DISPONIBILITA' ATTUALE</td>
                        <td align='right'><h4><b>".$disp_attuale_style.number_format($disp_attuale,2,',','.')."</b></h4></td>
                        <td align='right'><small>&nbsp; P+S</td>
                     </tr>
                     <tr class='table-success' style='border-bottom:2px solid green;'>
                        <td height='40' align='left'>DISPONIBILITA' ATTUALE (nella logica SICRA)</td>
                        <td align='right'><b>".$disp_attuale_style.number_format(($plafond_iniziale + @$saldo2881),2,',','.')."</b></td>
                        <td align='right'><small>&nbsp; P+Capitale</td>
                     </tr>
                     <tr>
                        <td height='40' align='left'>Liquidazioni da effettuare (al netto Sofferenze)</small> </td>
                        <td align='right'>".number_format($totale_liquidazioni_dafare,2,',','.')."</td>
                        <td align='right'><small>&nbsp; L</td>
                     </tr>
                     <tr>
                        <td height='40' align='left'>Liquidazioni da effettuare (comprese le Sofferenze)</small></td>
                        <td align='right'>".number_format($totale_liquidazioni_dafare_COMPLETO,2,',','.')."</td>
                        <td align='right'><small>&nbsp; L</td>
                     </tr>
                     <tr class='table-light'>
                        <td height='40' align='left'>DISPONIBILITA' AL NETTO DELLE LIQUIDAZIONI DA FARE</td>
                        <td align='right'><h4><b>".number_format($disp_senzaliquidaz,2,',','.')."</b></h4></td>
                        <td align='right'><small>&nbsp; (P+S) - L</td>
                     </tr>                      
                     <tr class='table-light'>
                        <td height='40' align='left'>DISPONIBILITA' AL NETTO DELLE LIQUIDAZIONI DA FARE (nella logica SICRA)</td>
                        <td align='right'><b>".number_format((($plafond_iniziale + @$saldo2881) - $totale_liquidazioni_dafare),2,',','.')."</b></td>
                        <td align='right'><small>&nbsp; (P+Capitale) - L</td>
                     </tr>                      
";

// ---- FINE TABELLA ----
echo '</table>

        </td>
      </tr>
      </table>';

      // SALDI CONTI CONTABILI
      // -----------------------------------------------------------------
        $select_saldi_coge =   "
                           SELECT
                                 CG_SALDI.COD_RAPP  ,
                                 CG_SALDI.FILIALE  ,
                                 CG_SALDI.NUM_RAPP  ,
                                 CG_SALDI.SALDO_DIV_CONTO/100 AS SALDO,
                            case CG_SALDI.COD_RAPP
                            when 1770 then (400000 + (CG_SALDI.SALDO_DIV_CONTO/100))
                            else
                                 ROUND((CG_SALDI.SALDO_DIV_CONTO/100),0)
                            end AS SALDO2 
                            FROM
                                CG_SALDI  
                            WHERE
                                CG_SALDI.COD_RAPP IN ( 2881,2885,1770,2557 ) 
                            AND
                                CG_SALDI.FILIALE = 990 
                            AND
                                CG_SALDI.NUM_RAPP = 100 
                            ORDER BY
                                 SALDO ASC

                        ";

        echo "<table border='0' align='center' width='20%'>";

        $result_saldi_coge = odbc_exec($connect, $select_saldi_coge);
        while($dati_saldi_coge = odbc_fetch_object($result_saldi_coge)) {

                if ($dati_saldi_coge->COD_RAPP == 2881) 
                        {$descrapp = '2881.990.100 - Capitale Sociale'; 
                         $saldo_2881 = $dati_saldi_coge->SALDO;}
                if ($dati_saldi_coge->COD_RAPP == 2885) 
                        {$descrapp = '2885.990.100 - Sovrapprezzo';  
                         $saldo_2885 = $dati_saldi_coge->SALDO;}
                if ($dati_saldi_coge->COD_RAPP == 1770) 
                        {$descrapp = '1770.990.100 - Fondo Riacquisto Azioni Proprie';
                         $saldo_1770 = $dati_saldi_coge->SALDO;}
                if ($dati_saldi_coge->COD_RAPP == 2557) 
                        {$descrapp = '2557.990.100 - Quote da Liquidare'; }

            echo  "<tr>
                        <td align='left'>".$descrapp."</td>
                        <td align='right'>".number_format($dati_saldi_coge->SALDO,2,',','.')."</td>
                        <td align='right'></td>
                     </tr>  ";
        }

        echo  "</table>
        <br><br><br>";


// INSERIMENTO DATI SU TABELLA TAB_VALOREFONDO
      $updcaricamento = "     UPDATE tab_valorefondo
                              SET   
                              aggiornamento = now(), 
                              valore = ".$saldo_1770.",
                              plafond = ".$disp_attuale.",
                              capitale = ".$saldo_2881.",
                              sovrapprezzo = ".$saldo_2885." 
                        ";
      $querydati_updcaricamento = mysqli_query($connection, $updcaricamento);        



echo "
<script language='javascript' type='text/javascript'>
function windowClose() {
window.open('','_parent','');
window.close();
}
</script>";

// Close ODBC
odbc_close($connect);

        echo '<center><a class="btn btn-outline-warning" href="situazione.php" target="_blank">Situazione Soci alla data odierna</a><br><br>';

if (@$_SERVER["HTTP_REFERER"] != 'http://10.197.139.22:8080/soci/soci_auth.php') {
        echo "<br><a class='btn btn-outline-success' style='text-decoration:none;color:white;' onclick=\"windowClose();\" href='../admin_cessioni.php?nominativo=".@$_GET['nominativo']."&id=".@$_GET['id']."&id2=".@$_GET['id2']."&dr=".@$_GET['dr']."&vn=".@$_GET['vn']."&disponibilita=".$disp_attuale."&plafond_iniziale=".$plafond_iniziale."&disp_senzaliquidaz=".$disp_senzaliquidaz."' target='_blank'>Previsione Rimborso Cessione ".@$_GET['nominativo']." - ID ".@$_GET['id']."</a><br><br>";
        }
        else {echo '';}

?>

