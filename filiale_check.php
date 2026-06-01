<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi 
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Controlli Filiale';

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// including FusionCharts PHP wrapper
include("graph/fusioncharts.php"); 

echo '<html>
        <head>
        <script type="text/javascript" src="js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        <title>'.$TITOLO.'</title>
        </head>
        <style type="text/css">
          @import "css/bootstrap.css";
          @import "css/bootstrap.min.css";
          @import "css/fontawesome-free/css/all.min.css";
        </style> 

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);


// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------
$adesso = date("d.m.Y");

// Calcolo data di partenza per Under 30
// *****************************************************************************
$date = new DateTime();             

if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = $inizioanno;
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

// Controllo se la richiesta arriva   
// da un FILIALE
      /*
     $condizionefiliale = ' AND s1.Filiale_Capofila in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
 */

if ($_GET['tipo'] == 1) {
// tipo = 1  >  C/C CON SALDI INFERIORI A 5.000 EURO
// stringa standard
//    http://10.197.139.22:8080/soci/filiale_check.php?tipo=1&filiale=61&tiponag=PF&socio=

    $filiale = $_GET['filiale'];
    $titolofiliale = ' Filiale '.$_GET['filiale'];  

    // FILTRO PERSONE FISICHE
    if ($_GET['tiponag'] == 'PF') 
        {
            $cond_pfcoi = "AND ANAG_NAG.TIPO_NAG in ('PF','COI')";
        }
    else
        {
            $cond_pfcoi = "";
        }

    // FILTRO SOCI
    if ($_GET['socio'] == '1')  
        {
            $cond_socio = "AND ANAG_NAG.SOCIO_ISTITUTO = '1' ";
        }
    else
        {
            $cond_socio = "";
        }

echo '
      <div class="alert alert-dismissible alert-danger">
            <h2 class="alert-heading">Posizioni con saldo C/C da negativo a + Eur 5.000</h2>
            <p class="mb-0 justify-content-between align-items-left">Filiale '.$filiale.' 
            <p class="mb-0 justify-content-between align-items-left">Parametri: <br>
            &tiponag=PF&socio=1 (solo persone fisiche e solo Soci)<br>
            &tiponag=&socio= (tutti i clienti)
            </p>
      </div>
';

    echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-danger">
          <td style="position: sticky;top: 0" align="left"><small>Filiale</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG</td>
          <td style="position: sticky;top: 0" align="left"><small>Intestazione</td>
          <td style="position: sticky;top: 0" align="left"><small>TipoNag</td>
          <td style="position: sticky;top: 0" align="left"><small>Settorista</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio</td>
          <td style="position: sticky;top: 0" align="left"><small>Numero Conto</td>
          <td style="position: sticky;top: 0" align="left"><small>Saldo</td>
        </tr>';

      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/filiale_check".$filiale.".csv", "w");
      $contenutoOutput = "FILIALE;NAG;INTESTAZIONE;TIPONAG;SETTORISTA;SOCIO;CONTO;SALDO\n";

    $select = "
                    SELECT
                         CG_SALDI.FILIALE  ,
                         CG_SALDI.NAG  ,
                         (ANAG_NAG.INTESTAZIONE_A + ' ' + ANAG_NAG.INTESTAZIONE_B) AS INTESTAZIONE ,
                         ANAG_NAG.TIPO_NAG ,
                         ANAG_NAG.SETTORISTA  ,
                         CASE ANAG_NAG.SOCIO_ISTITUTO
                            WHEN '1' THEN 'SOCIO' 
                            WHEN '9' THEN 'EX SOCIO' 
                            ELSE '' END AS SOCIO,
                         CG_SALDI.NUM_RAPP  ,
                         CG_SALDI.SALDO_DIV_CONTO/100 AS SALDO 
                    FROM
                        CG_SALDI INNER JOIN ANAG_NAG ON (CG_SALDI.NAG = ANAG_NAG.NAG ) ,
                        CG_SALDI INNER JOIN CC_CONTI_CORRENTI ON (CG_SALDI.NUM_RAPP = CC_CONTI_CORRENTI.NUM_RAPP  
                    AND
                        CG_SALDI.FILIALE = CC_CONTI_CORRENTI.FILIALE
                    AND
                        CG_SALDI.COD_RAPP = CC_CONTI_CORRENTI.COD_RAPP )  
                    WHERE
                        CG_SALDI.COD_RAPP = 2 
                    AND
                        CG_SALDI.SALDO_DIV_CONTO/100 <= 5000 
                    AND
                        CG_SALDI.FILIALE <> 990 
                    AND
                        CC_CONTI_CORRENTI.STATO =  '0' 
                    AND 
                        CG_SALDI.FILIALE in (".$filiale.")
                    ".$cond_pfcoi."
                    ".$cond_socio."
                    ORDER BY 
                        CG_SALDI.SALDO_DIV_CONTO/100 ASC
                    ";

    $result = odbc_exec($connect, $select);
    while ($dati = odbc_fetch_object($result)) {

              echo "<tr>
                        <td><small>".$dati->FILIALE."</td>
                        <td><small>".$dati->NAG."</td>
                        <td><small>".$dati->INTESTAZIONE."</td>
                        <td><small>".$dati->TIPO_NAG."</td>
                        <td><small>".$dati->SETTORISTA."</td>
                        <td><small>".$dati->SOCIO."</td>
                        <td><small>".$dati->NUM_RAPP."</td>
                        <td><small>".number_format($dati->SALDO,2,',','.')."</td>
                      </tr>
                    ";
    

        $contenutoOutput .= 
                         $dati->FILIALE.";"
                        .$dati->NAG.";"
                        .$dati->INTESTAZIONE.";"
                        .$dati->TIPO_NAG.";"
                        .$dati->SETTORISTA.";"
                        .$dati->SOCIO.";"
                        .$dati->NUM_RAPP.";"
                        .number_format($dati->SALDO,2,',','.')."\n";

    }

    echo '</table>';

      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);

echo '<br><center>
            <a href="tmp/filiale_check'.$filiale.'.csv" style="text-color:white;" target="_blank">Scarica Elenco</a>
            </center>';

}

elseif ($_GET['tipo'] == 2)

{
//tipo = 2  >  C/C SDD B2C DI FINANZIARIE ATTIVI
// stringa standard
//    http://10.197.139.22:8080/soci/filiale_check.php?tipo=2&filiale=61&tiponag=PF&socio=
     $filiale = $_GET['filiale'];
     $titolofiliale = ' Filiale '.$_GET['filiale'];  

    // FILTRO PERSONE FISICHE
    if ($_GET['tiponag'] == 'PF') 
        {
            $cond_pfcoi = "AND ANAG_NAG.TIPO_NAG in ('PF','COI')";
        }
    else
        {
            $cond_pfcoi = "";
        }

    // FILTRO SOCI
    if ($_GET['socio'] == '1')  
        {
            $cond_socio = "AND ANAG_NAG.SOCIO_ISTITUTO = '1' ";
        }
    else
        {
            $cond_socio = "";
        }

echo '
      <div class="alert alert-dismissible alert-danger">
            <h2 class="alert-heading">Posizioni C/C con Contratti di Finanziarie in essere</h2>
            <p class="mb-0 justify-content-between align-items-left">Filiale '.$filiale.' 
            <p class="mb-0 justify-content-between align-items-left">Parametri: <br>
            &tiponag=PF&socio=1 (solo persone fisiche e solo Soci)<br>
            &tiponag=&socio= (tutti i clienti)
            </p>
      </div>
';

    echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-danger">
          <td style="position: sticky;top: 0" align="left"><small>Filiale</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG</td>
          <td style="position: sticky;top: 0" align="left"><small>Intestazione</td>
          <td style="position: sticky;top: 0" align="left"><small>TipoNag</td>
          <td style="position: sticky;top: 0" align="left"><small>Settorista</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio</td>
          <td style="position: sticky;top: 0" align="left"><small>Numero Conto</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Apertura</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Inserimento</td>
          <td style="position: sticky;top: 0" align="left"><small>Descrizione Contratto</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Ultima Disp</td>
          <td style="position: sticky;top: 0" align="left"><small>Note</td>
          <td style="position: sticky;top: 0" align="left"><small>Classe Condiz</td>
        </tr>';

      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/filiale_check_fin_".$filiale.".csv", "w");
      $contenutoOutput = "FILIALE;NAG;INTESTAZIONE;TIPONAG;SETTORISTA;SOCIO;CONTO;DTAPERTURA;DTINSERIMENTO;DESCRIZIONECONTRATTO;DTULTIMADISP;NOTE;CLASSECONDIZ\n";

    $select = "
select
     CT_CONTRATTI.NAG_INTESTATARIO AS CT_CONTRATTI_NAG_INTESTATARIO ,
     (ANAG_NAG.INTESTAZIONE_A + ' ' + ANAG_NAG.INTESTAZIONE_B) AS INTESTAZIONE,
     ANAG_NAG.TIPO_NAG AS ANAG_NAG_TIPO_NAG ,
     ANAG_NAG.SETTORISTA AS ANAG_NAG_SETTORISTA ,
                         CASE ANAG_NAG.SOCIO_ISTITUTO
                            WHEN '1' THEN 'SOCIO' 
                            WHEN '9' THEN 'EX SOCIO' 
                            ELSE '' END AS SOCIO,
     CT_CONTRATTI.FILIALE_DB_CR AS CT_CONTRATTI_FILIALE_DB_CR ,
     CT_CONTRATTI.NUM_RAPP_DB_CR AS CT_CONTRATTI_NUM_RAPP_DB_CR ,
     CT_CONTRATTI.DATA_APERTURA AS CT_CONTRATTI_DATA_APERTURA ,
     CT_CONTRATTI.DATA_INSERIMENTO AS CT_CONTRATTI_DATA_INSERIMENTO ,
     CT_CONTRATTI.DESCR_CONTR AS CT_CONTRATTI_DESCR_CONTR ,
     CT_CONTRATTI.DATA_ULT_DISPOSIZ AS CT_CONTRATTI_DATA_ULT_DISPOSIZ ,
     CT_CONTRATTI.NOTE AS CT_CONTRATTI_NOTE ,
     CT_CONTRATTI.CLASSE_COND AS CT_CONTRATTI_CLASSE_COND 

                    FROM
                        CC_CONTI_CORRENTI INNER JOIN CT_CONTRATTI ON (CC_CONTI_CORRENTI.NUM_RAPP = CT_CONTRATTI.NUM_RAPP_DB_CR  
                    AND
                        CC_CONTI_CORRENTI.FILIALE = CT_CONTRATTI.FILIALE_DB_CR  
                    AND
                        CC_CONTI_CORRENTI.COD_RAPP = CT_CONTRATTI.COD_RAPP_DB_CR ) ,
                        CC_CONTI_CORRENTI INNER JOIN ANAG_NAG ON (CC_CONTI_CORRENTI.NAG = ANAG_NAG.NAG )  
                    WHERE
                        CC_CONTI_CORRENTI.STATO =  '0'  
                    AND
                        CT_CONTRATTI.TIPO_CONTRATTO = 40 
                    AND
                        CT_CONTRATTI.DATA_ESTINZIONE =  '00/00/0000' 
                    AND
                    (
                    CT_CONTRATTI.DESCR_CONTR like '%COFIDIS%'   
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%AGOS%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%FINDOMESTIC%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%COMPASS%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%BMW FINANCIAL%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%BCC CONSUMER%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%SANTANDER%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%FIDITALIA%'
                        OR
                    CT_CONTRATTI.DESCR_CONTR like '%PRESTITALIA%'
                    )

                    AND 
                        CT_CONTRATTI.FILIALE_DB_CR in (".$filiale.")
                    ".$cond_pfcoi."
                    ".$cond_socio."
                    ORDER BY 
                        2
                    ";

    $result = odbc_exec($connect, $select);
    while ($dati = odbc_fetch_object($result)) {

              echo "<tr>
                        <td><small>".$dati->CT_CONTRATTI_FILIALE_DB_CR."</td>
                        <td><small>".$dati->CT_CONTRATTI_NAG_INTESTATARIO."</td>
                        <td><small>".$dati->INTESTAZIONE."</td>
                        <td><small>".$dati->ANAG_NAG_TIPO_NAG."</td>
                        <td><small>".$dati->ANAG_NAG_SETTORISTA."</td>
                        <td><small>".$dati->SOCIO."</td>
                        <td><small>".$dati->CT_CONTRATTI_NUM_RAPP_DB_CR."</td>
                        <td><small>".$dati->CT_CONTRATTI_DATA_APERTURA."</td>
                        <td><small>".$dati->CT_CONTRATTI_DATA_INSERIMENTO."</td>
                        <td><small>".$dati->CT_CONTRATTI_DESCR_CONTR."</td>
                        <td><small>".$dati->CT_CONTRATTI_DATA_ULT_DISPOSIZ."</td>
                        <td><small>".$dati->CT_CONTRATTI_NOTE."</td>
                        <td><small>".$dati->CT_CONTRATTI_CLASSE_COND."</td>
                      </tr>
                    ";
    

        $contenutoOutput .= 
                         $dati->CT_CONTRATTI_FILIALE_DB_CR.";"
                        .$dati->CT_CONTRATTI_NAG_INTESTATARIO.";"
                        .$dati->INTESTAZIONE.";"
                        .$dati->ANAG_NAG_TIPO_NAG.";"
                        .$dati->ANAG_NAG_SETTORISTA.";"
                        .$dati->SOCIO.";"
                        .$dati->CT_CONTRATTI_NUM_RAPP_DB_CR.";"
                        .$dati->CT_CONTRATTI_DATA_APERTURA.";"
                        .$dati->CT_CONTRATTI_DATA_INSERIMENTO.";"
                        .$dati->CT_CONTRATTI_DESCR_CONTR.";"
                        .$dati->CT_CONTRATTI_DATA_ULT_DISPOSIZ.";"
                        .$dati->CT_CONTRATTI_NOTE.";"
                        .$dati->CT_CONTRATTI_CLASSE_COND."\n";

    }

    echo '</table>';

      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);

echo '<br><center>
            <a href="tmp/filiale_check_fin_'.$filiale.'.csv" style="text-color:white;" target="_blank">Scarica Elenco</a>
            </center>';
}

else

{
    echo 'Nessun parametro passato';
}




// Close ODBC
odbc_close($connect);


?>
