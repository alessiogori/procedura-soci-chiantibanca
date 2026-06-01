<?php
//////////////////////////////////////////////////////////////////
// SITUAZIONE SOCI
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'SITUAZIONE';
$TITOLO = 'Situazione Soci';

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
          @import "../css/fontawesome-free/css/all.min.css";
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
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

// Controllo se la richiesta arriva dai SOCI
if (!isset($_GET['filiale']))
    {$condizionefiliale = '';
     $condizionefiliale2 = '';
     $condizionefiliale3 = '';
     $condizionefiliale4 = '';
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
     $condizionefiliale2 = ' AND tmp_soci_inout2.Filiale in ('.$_GET['filiale'].')';
     $condizionefiliale3 = ' AND FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale4 = ' AND IR.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND ANAG_NAG.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND tmp_soci_inout2.Filiale in ('.$_GET['filiale'].')';
     $condizionefiliale3 = ' AND FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }

echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">Situazione Soci</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
      </div>
    <a name="inizio"></a>
    <center>
        <a href="#sit" class="m-3 font-weight-italic text-success">[Situazione]</a>
        <a href="#sitmens" class="m-3 font-weight-italic text-success">[Situazione Mensile]</a>
        <a href="#racimp" class="m-3 font-weight-italic text-success">[Raccolta e Impieghi]</a>
    </center>
    <br>
';

// ----------------------------------------------------------------------------
// CAPITALE SOCIALE E NUMERO AZIONI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ----------------------------------------------------------------------------
$select_Capitale =   "        SELECT 'A1 - Capitale Sociale alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND cert.DATA_ACQUISTO <= '".$_GET['datain']."'
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['datain']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['datain']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale."
                              
                              UNION
                              
                              SELECT 'A2 - Capitale Sociale incremento' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO >= '".$_GET['datain']."' AND
                                     cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')
                              ".$condizionefiliale."
                              
                              UNION

                              SELECT 'A3 - Capitale Sociale decremento' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND ((cert.DATA_ANNULLAMENTO >= '".$_GET['datain']."' AND
                                       cert.DATA_ANNULLAMENTO <= '".$_GET['dataout']."') OR
                                       (cert.DATA_VENDITA >= '".$_GET['datain']."' AND
                                       cert.DATA_VENDITA <= '".$_GET['dataout']."'))
                              AND mov.ctipomov not in
                                       ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')  
                              ".$condizionefiliale."
                              
                              UNION

                              SELECT 'A4 - Capitale Sociale alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['dataout']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale."                                     
                              ";
             // echo $select_Capitale;


// ---------------------------------------------------------
// SOVRAPPREZZO - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ---------------------------------------------------------
$select_Sovrapprezzo    = "   SELECT 'B1 - Sovrapprezzo alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, sum(mov.isovrapprezzo) as Valore
                                    FROM SOCI_MOVIMENTI mov, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where mov.DATA_MOVIMENTO <= '".$_GET['datain']."'
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale."

                              UNION

                              SELECT 'B2 - Sovrapprezzo incremento' as Tipo, '".$_GET['datain']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where MOV.ISOVRAPPREZZO > 0
                                    AND (mov.DATA_MOVIMENTO >= '".$_GET['datain']."' AND
                                           mov.DATA_MOVIMENTO <= '".$_GET['dataout']."')
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale."
                              
                              UNION

                              SELECT 'B3 - Sovrapprezzo decremento' as Tipo, '".$_GET['datain']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where MOV.ISOVRAPPREZZO < 0
                                    AND (mov.DATA_MOVIMENTO >= '".$_GET['datain']."' AND
                                     mov.DATA_MOVIMENTO <= '".$_GET['dataout']."')
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale."

                              UNION

                              SELECT 'B4 - Sovrapprezzo alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where mov.DATA_MOVIMENTO <= '".$_GET['dataout']."'
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale."
                              ";

// ----------------------------------------------------------
// QUANTITA' SOCI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ----------------------------------------------------------
$select_Soci =         "      SELECT 'C1 - Soci alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA <= '".$_GET['datain']."'
                                    AND (DATA_USCITA = '00/00/0000'
                                          OR DATA_USCITA > '".$_GET['datain']."')
                                    ".$condizionefiliale."

                              UNION

                              SELECT 'C2 - Soci incrementati' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA >= '".$_GET['datain']."'
                                    AND DATA_ENTRATA <= '".$_GET['dataout']."'
                                    ".$condizionefiliale."
                              
                              UNION

                              SELECT 'C3 - Soci decrementati' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_USCITA >= '".$_GET['datain']."'
                                    AND DATA_USCITA <= '".$_GET['dataout']."'
                                    ".$condizionefiliale."

                              UNION

                              SELECT 'C4 - Soci alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA <= '".$_GET['dataout']."'
                                    AND (DATA_USCITA = '00/00/0000'
                                          OR DATA_USCITA > '".$_GET['dataout']."')
                                    ".$condizionefiliale."
                              ";

//echo $select_Soci;
// -----------------------------------------------------------------
// Estrazione dei valori dalle singole Select
// -----------------------------------------------------------------

echo '<table border="0" align="center">
        <tr>
            <td>';


      // CAPITALE SOCIALE
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>CAPITALE SOCIALE</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Valore Capitale</td>
                <td align="right"><small>Numero Azioni</td>
              </tr>';

      $result_Capitale = odbc_exec($connect, $select_Capitale);
      while($dati_Capitale = odbc_fetch_object($result_Capitale)) {

            if ($dati_Capitale->TIPO == 'A1 - Capitale Sociale alla data iniziale')     {$valore_inizio = $dati_Capitale->CAPITALESOCIALE;      $azioni_inizio = $dati_Capitale->NUMEROAZIONI;  }
            if ($dati_Capitale->TIPO == 'A4 - Capitale Sociale alla data finale')       {$valore_fine   = $dati_Capitale->CAPITALESOCIALE;      $azioni_fine   = $dati_Capitale->NUMEROAZIONI;  }

                echo "<tr>
                        <td><small>".$dati_Capitale->TIPO."</td>
                        <td align='center'><small>".$dati_Capitale->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Capitale->CAPITALESOCIALE,2,',','.')."</td>
                        <td align='right'><small>".number_format($dati_Capitale->NUMEROAZIONI,0,',','.')."</td>
                      </tr>
                    ";

      }
            if (($valore_fine - $valore_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:lightred;"> &#8722;'; }
            if (($azioni_fine - $azioni_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:red;"> &#8722;'; }

                echo "<tr class='table-secondary'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($valore_fine - $valore_inizio),2,',','.')."</b></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($azioni_fine - $azioni_inizio),0,',','.')."</b></td>
                      </tr>
                      </table>";      

echo '</td>
      <td>&nbsp;&nbsp;</td>
      <td>';

      // SOVRAPPREZZO SOCI
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>SOVRAPPREZZO</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Valore Sovrapprezzo</td>
              </tr>';

      $result_Sovrapprezzo = odbc_exec($connect, $select_Sovrapprezzo);
      while($dati_Sovrapprezzo = odbc_fetch_object($result_Sovrapprezzo)) {

            if ($dati_Sovrapprezzo->TIPO == 'B1 - Sovrapprezzo alla data iniziale')     {$valore_inizio = $dati_Sovrapprezzo->VALORE;}
            if ($dati_Sovrapprezzo->TIPO == 'B4 - Sovrapprezzo alla data finale')       {$valore_fine   = $dati_Sovrapprezzo->VALORE;}

                echo "<tr>
                        <td><small>".$dati_Sovrapprezzo->TIPO."</td>
                        <td align='center'><small>".$dati_Sovrapprezzo->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Sovrapprezzo->VALORE,2,',','.')."</td>
                      </tr>
                    ";

      }
            if (($valore_fine - $valore_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:red;"> &#8722;'; }

                echo "<tr class='table-secondary'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($valore_fine - $valore_inizio),0,',','.')."</b></td>
                      </tr>
                      </table>";        

echo '</td>
      <td>&nbsp;&nbsp;</td>
      <td>';

      // QUANTITA' SOCI
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>NUMERO DI SOCI</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Quantità</td>
              </tr>';

      $result_Soci = odbc_exec($connect, $select_Soci);
      while($dati_Soci = odbc_fetch_object($result_Soci)) {

            if ($dati_Soci->TIPO == 'C1 - Soci alla data iniziale')     {$soci_inizio = $dati_Soci->QTA;}
            if ($dati_Soci->TIPO == 'C4 - Soci alla data finale')       {$soci_fine   = $dati_Soci->QTA;}

                echo "<tr>
                        <td><small>".$dati_Soci->TIPO."</td>
                        <td align='center'><small>".$dati_Soci->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Soci->QTA,0,',','.')."</td>
                      </tr>
                    ";

      }
            if (($soci_fine - $soci_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:lightred;"> &#8722;'; }

                echo "<tr class='table-secondary'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($soci_fine - $soci_inizio), 0, ',', '.')."</b></td>
                      </tr>
                      </table>";        


echo '</td>
      </tr>
      </table>';


// Close ODBC
//odbc_close($connect);


// ----------------------------------------------------
// REPORT AMMISSIONI USCITE
// ----------------------------------------------------

// ----------------------------------------------------
// FusionChart - Controllo tema e colorazione
// ----------------------------------------------------
//if ($_SERVER["HTTP_REFERER"] == 'http://10.197.139.22:8080/soci/stats/repcda_prospetto_consiglio.php')
if ($_GET['f'] == 999)
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

/*
echo '
    <div class="alert alert-dismissible alert-warning">
        <h2 class="alert-heading">Nuove Ammissioni Soci</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
    </div>
';
*/

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// --------------------------------------------------------------------------------
// CREAZIONE VISTA TEMPORANEA TMP_SOCI_INOUT2 (Incrementi e Decrementi per Filiale)
// --------------------------------------------------------------------------------
$truncatetable = mysqli_query($dbhandle,"TRUNCATE TMP_SOCI_INOUT2") or die(mysqli_error($dbhandle));;

$createtable_1 = "
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        count(*) as SOCI_INIZIO,
                        0 as  SOCI_INCREMENTO, 0 as SOCI_DECREMENTO, 0 as SOCI_FINE
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_ENTRATA <= '".$_GET['datain']."'
                        AND (DATA_USCITA = '00/00/0000'
                              OR DATA_USCITA > '".$_GET['datain']."')
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE_CAPOFILA, SOCI_INCREMENTO, SOCI_DECREMENTO, SOCI_FINE
                ";

          $result_createtable = odbc_exec($connect, $createtable_1);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'".$dati_createtable->SOCI_INIZIO."'
                ,'".$dati_createtable->SOCI_INCREMENTO."'
                ,'".$dati_createtable->SOCI_DECREMENTO."'
                ,'".$dati_createtable->SOCI_FINE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'                
                )
                ";
                  
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }


$createtable_2 = "
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        0 as SOCI_INIZIO,
                        count(*) as  SOCI_INCREMENTO, 0 as SOCI_DECREMENTO, 0 as SOCI_FINE
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_ENTRATA >= '".$_GET['datain']."'
                        AND DATA_ENTRATA <= '".$_GET['dataout']."'
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE, SOCI_INIZIO, SOCI_DECREMENTO, SOCI_FINE
                ";

          $result_createtable = odbc_exec($connect, $createtable_2);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'".$dati_createtable->SOCI_INIZIO."'
                ,'".$dati_createtable->SOCI_INCREMENTO."'
                ,'".$dati_createtable->SOCI_DECREMENTO."'
                ,'".$dati_createtable->SOCI_FINE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'                
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }


$createtable_3 = "
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        0 as SOCI_INIZIO,
                        0 as  SOCI_INCREMENTO, count(*) as SOCI_DECREMENTO, 0 as SOCI_FINE
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_USCITA >= '".$_GET['datain']."'
                        AND DATA_USCITA <= '".$_GET['dataout']."'
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE, SOCI_INIZIO, SOCI_INCREMENTO, SOCI_FINE
                ";

          $result_createtable = odbc_exec($connect, $createtable_3);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'".$dati_createtable->SOCI_INIZIO."'
                ,'".$dati_createtable->SOCI_INCREMENTO."'
                ,'".$dati_createtable->SOCI_DECREMENTO."'
                ,'".$dati_createtable->SOCI_FINE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'                
                )
                ";

            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }

$createtable_4 = "
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        0 as SOCI_INIZIO,
                        0 as  SOCI_INCREMENTO, 0 as SOCI_DECREMENTO, count(*) as SOCI_FINE
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_ENTRATA <= '".$_GET['dataout']."'
                        AND (DATA_USCITA = '00/00/0000'
                              OR DATA_USCITA > '".$_GET['dataout']."')
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE, SOCI_INIZIO, SOCI_INCREMENTO, SOCI_DECREMENTO
                ";

          $result_createtable = odbc_exec($connect, $createtable_4);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'".$dati_createtable->SOCI_INIZIO."'
                ,'".$dati_createtable->SOCI_INCREMENTO."'
                ,'".$dati_createtable->SOCI_DECREMENTO."'
                ,'".$dati_createtable->SOCI_FINE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'                
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }


// ---------------------------------------------------------------------------------------
// CREAZIONE VISTA TEMPORANEA TMP_SOCI_INOUT3 (Incrementi e Decrementi per Filiale e Mese)
// ---------------------------------------------------------------------------------------
$truncatetable = mysqli_query($dbhandle,"TRUNCATE TMP_SOCI_INOUT3") or die(mysqli_error($dbhandle));;

$createtable_inout3 = "
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        concat( 
                            TO_CHAR(DATA_ENTRATA,'YYYY'), TO_CHAR(DATA_ENTRATA,'MM')
                            ) as ANNOMESE,
                        'IN' as TIPO,
                        count(*) as QTA
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_ENTRATA >= '".$_GET['datain']."'
                        AND DATA_ENTRATA <= '".$_GET['dataout']."'
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE, concat( 
                            TO_CHAR(DATA_ENTRATA,'YYYY'), TO_CHAR(DATA_ENTRATA,'MM')
                            ), TIPO
                UNION
                SELECT  FILIALE_CAPOFILA as FILIALE,
                        concat( 
                            TO_CHAR(DATA_USCITA,'YYYY'), TO_CHAR(DATA_USCITA,'MM')
                            ) as ANNOMESE,
                        'OUT' as TIPO,
                        count(*) as QTA
                        FROM SOCI_ANAGRAFICA, ANAG_NAG
                        WHERE DATA_USCITA >= '".$_GET['datain']."'
                        AND DATA_USCITA <= '".$_GET['dataout']."'
                        AND SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                        ".$condizionefiliale3."
                        GROUP BY FILIALE, concat( 
                            TO_CHAR(DATA_USCITA,'YYYY'), TO_CHAR(DATA_USCITA,'MM')
                            ), TIPO             
                ";

          $result_createtable_inout3 = odbc_exec($connect, $createtable_inout3);
          while($dati_createtable_inout3 = odbc_fetch_object($result_createtable_inout3)) {

          $insert_createtable_inout3 = "
                INSERT INTO TMP_SOCI_INOUT3
                VALUES 
               (
                '".$dati_createtable_inout3->FILIALE."'
                ,'".$dati_createtable_inout3->ANNOMESE."'
                ,'".$dati_createtable_inout3->TIPO."'
                ,'".$dati_createtable_inout3->QTA."'
                ,'0'
                )
                ";
                   
            mysqli_query($connection, $insert_createtable_inout3 )
                        or die("INSERT --- ".mysqli_error($connection));;
          }


// ----------------------------------------------------------------------------------------
// CAPITALE SOCIALE E NUMERO AZIONI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO PER FILIALE
// ----------------------------------------------------------------------------------------
$createtable_5 =   "        SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE_INIZIO
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND cert.DATA_ACQUISTO <= '".$_GET['datain']."'
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['datain']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['datain']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale."
                              GROUP BY ANAG_NAG.FILIALE_CAPOFILA
                              ";

          $result_createtable = odbc_exec($connect, $createtable_5);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'".$dati_createtable->CAPITALESOCIALE_INIZIO."'
                ,'0'
                ,'0'
                ,'0'
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }

$createtable_6 =   "          SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE_INCREMENTO
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO >= '".$_GET['datain']."' AND
                                     cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')
                              ".$condizionefiliale."
                              GROUP BY ANAG_NAG.FILIALE_CAPOFILA
                              ";

          $result_createtable = odbc_exec($connect, $createtable_6);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'".$dati_createtable->CAPITALESOCIALE_INCREMENTO."'
                ,'0'
                ,'0'
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }                              

$createtable_7 =   "        SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE_DECREMENTO
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND ((cert.DATA_ANNULLAMENTO >= '".$_GET['datain']."' AND
                                       cert.DATA_ANNULLAMENTO <= '".$_GET['dataout']."') OR
                                       (cert.DATA_VENDITA >= '".$_GET['datain']."' AND
                                       cert.DATA_VENDITA <= '".$_GET['dataout']."'))
                              AND mov.ctipomov not in
                                       ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')  
                              ".$condizionefiliale."
                              GROUP BY ANAG_NAG.FILIALE_CAPOFILA
                              ";

          $result_createtable = odbc_exec($connect, $createtable_7);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'".$dati_createtable->CAPITALESOCIALE_DECREMENTO."'
                ,'0'
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          }        

$createtable_8 =   "        SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE_FINE
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['dataout']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale."  
                              GROUP BY ANAG_NAG.FILIALE_CAPOFILA
                              ";

          $result_createtable = odbc_exec($connect, $createtable_8);
          while($dati_createtable = odbc_fetch_object($result_createtable)) {

          $insert_createtable = "
                INSERT INTO TMP_SOCI_INOUT2
                VALUES 
               (
                 '".$_GET['datain']."'
                ,'".$_GET['dataout']."'
                ,'".$dati_createtable->FILIALE."'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'0'
                ,'".$dati_createtable->CAPITALESOCIALE_FINE."'
                )
                ";
                   
            mysqli_query($connection, $insert_createtable )
                        or die("INSERT --- ".mysqli_error($connection));;
          } 




// ----------------------------------------------------------------------------------------
// CAPITALE SOCIALE E NUMERO AZIONI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO PER FILIALE E ANNOMESE
// ----------------------------------------------------------------------------------------

$createtable_inout3_cap =   "
                              SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                                concat( 
                                    TO_CHAR(DATA_ACQUISTO,'YYYY'), TO_CHAR(DATA_ACQUISTO,'MM')
                                    ) as ANNOMESE,
                                'IN' as TIPO,
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO >=  '".$_GET['datain']."' AND
                                     cert.DATA_ACQUISTO <=  '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')
                              ".$condizionefiliale."
                              GROUP BY ANAG_NAG.FILIALE_CAPOFILA, concat( 
                                    TO_CHAR(DATA_ACQUISTO,'YYYY'), TO_CHAR(DATA_ACQUISTO,'MM')
                                    ) , TIPO
                              UNION
                              SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                                concat( 
                                    TO_CHAR(DATA_ANNULLAMENTO,'YYYY'), TO_CHAR(DATA_ANNULLAMENTO,'MM')
                                    ) as ANNOMESE,
                                'OUT' as TIPO,
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND ((cert.DATA_ANNULLAMENTO >= '".$_GET['datain']."' AND
                                       cert.DATA_ANNULLAMENTO <=  '".$_GET['dataout']."') )
                              AND mov.ctipomov not in
                                       ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')  
                              ".$condizionefiliale."
                               GROUP BY ANAG_NAG.FILIALE_CAPOFILA, concat( 
                                    TO_CHAR(DATA_ANNULLAMENTO,'YYYY'), TO_CHAR(DATA_ANNULLAMENTO,'MM')
                                    ) , TIPO  
                              UNION
                              SELECT ANAG_NAG.FILIALE_CAPOFILA AS FILIALE, 
                                concat( 
                                    TO_CHAR(DATA_VENDITA,'YYYY'), TO_CHAR(DATA_VENDITA,'MM')
                                    ) as ANNOMESE,
                                'OUT' as TIPO,
                              sum(cert.NAZIONI * 30.33) as CAPITALESOCIALE
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (    (cert.DATA_VENDITA >=  '".$_GET['datain']."' AND
                                       cert.DATA_VENDITA <=  '".$_GET['dataout']."'))
                              AND mov.ctipomov not in
                                       ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')  
                              ".$condizionefiliale."
                               GROUP BY ANAG_NAG.FILIALE_CAPOFILA, concat( 
                                    TO_CHAR(DATA_VENDITA,'YYYY'), TO_CHAR(DATA_VENDITA,'MM')
                                    ) , TIPO                              
                       
                          ";                                    


          $result_createtable_inout3_cap = odbc_exec($connect, $createtable_inout3_cap);
          while($dati_createtable_inout3_cap = odbc_fetch_object($result_createtable_inout3_cap)) {

          $insert_createtable_inout3_cap = "
                INSERT INTO TMP_SOCI_INOUT3
                VALUES 
               (
                '".$dati_createtable_inout3_cap->FILIALE."'
                ,'".$dati_createtable_inout3_cap->ANNOMESE."'
                ,'".$dati_createtable_inout3_cap->TIPO."'
                ,'0'
                ,'".$dati_createtable_inout3_cap->CAPITALESOCIALE."'
                )
                ";

            mysqli_query($connection, $insert_createtable_inout3_cap )
                        or die("INSERT --- ".mysqli_error($connection));;
          }



// Close ODBC
odbc_close($connect);

// -------------------------------------------------------------------------------
// AMMISSIONI TOTALI
// -------------------------------------------------------------------------------
$strQuery2 = "  SELECT cast(tmp_soci_inout2.Filiale as unsigned) as Filiale, desc_Filiale as NomeFiliale,
            sum(soci_Incremento) as qta_entrati, 
            sum(soci_Decremento) as qta_usciti
            FROM tmp_soci_inout2, tab_psw
            WHERE Periodo_inizio >= '".$_GET['datain']."'
            AND tmp_soci_inout2.Filiale < 999
            AND tmp_soci_inout2.Filiale = cast(tab_psw.Filiale as unsigned)
            ".$condizionefiliale2."
            GROUP BY tmp_soci_inout2.Filiale
            ORDER BY 1 ASC
                ";
//echo $strQuery2;
$result2 = $dbhandle->query($strQuery2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result2) {
            
    $arrData2 = array(
        "chart" => array(
            "caption"=> "Ammissioni e Uscite per Filiale dal ".$_GET['datain'],
            "captionFont" => "Arial",
            "captionFontSize" => "24",
            //"captionFontColor" => "#000000",
            "subcaptionFontSize" => "20",
            "xAxisname"=> "Filiale",
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
            "valueFontSize" => "12"
            )
            );

            // creating array for categories object
            $categoryArray=array();
            $dataseries1=array();
            $dataseries2=array();
            
            // pushing category array values
            while($rowResult2 = mysqli_fetch_array($result2)) {              
                    array_push($categoryArray, array(
                      "label" => $rowResult2["Filiale"].' '.$rowResult2["NomeFiliale"]
                    )
                );

                array_push($dataseries1, array(
                    "value" => $rowResult2["qta_entrati"]
                    //"value" => number_format($row["qta_Pulita"], 0, ',', '.')
                    ) 
                );
            
                array_push($dataseries2, array(
                    "value" => $rowResult2["qta_usciti"]
                    //"value" => number_format($row["qta_Passaggio"], 0, ',', '.')
                    )
                );
    
            }
            
        $arrData2["categories"]=array(array("category"=>$categoryArray));

            // creating dataset object
            $arrData2["dataset"] = array(array("seriesName"=> "Soci Entrati", "data"=>$dataseries1), array("seriesName"=> "Soci Usciti", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrData2);
      // chart object
      $msChart = new FusionCharts("msline", "myChart2" , "100%", "400", "amm2", "json", $jsonEncodedData);
      $msChart->render();
             
   }

// -------------------------------------------------------------------------------
// TREND PER MESE/ANNO - QUANTITA'
// -------------------------------------------------------------------------------
$trend = "  SELECT AnnoMeseRichiesta,
            sum(qta_entrati) as qta_entrati, 
            sum(qta_usciti) as qta_usciti
            FROM view_ammissioni_uscite
            WHERE AnnoMeseRichiesta >= 202101
            GROUP BY AnnoMeseRichiesta
            ORDER BY 1 ASC
                ";

$result_trend = $dbhandle->query($trend) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  if ($result_trend) {
            
    $arrDataTrend = array(
        "chart" => array(
            "caption"=> "Ammissioni e Uscite per mese/anno (storico Banca da 2021)",
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
            "theme"=> "".$tema."",
            "bgColor" => "".$bgcolor."",
            //"bgAlpha" => "10",
            "labelFont" => "Arial",  
            "labelFontSize" => "12" ,   
            //"labelFontColor" => "#000000",
            "rotateLabels" => "1",
            "valueFontBold" => "0",
            "rotateValues" => "0",
            "valueFont" => "Arial",
            //"valueFontColor" => "#000000",
            "valueFontColor" => "".$valueFontColor."",
            "valueFontSize" => "12"
            )
            );

            // creating array for categories object
            $categoryArray=array();
            $dataseries1=array();
            $dataseries2=array();
            
            // pushing category array values
            while($rowTrend = mysqli_fetch_array($result_trend)) {              
                    array_push($categoryArray, array(
                      "label" => $rowTrend["AnnoMeseRichiesta"]
                    )
                );

                array_push($dataseries1, array(
                    "value" => $rowTrend["qta_entrati"]
                    //"value" => number_format($row["qta_Pulita"], 0, ',', '.')
                    ) 
                );
            
                array_push($dataseries2, array(
                    "value" => $rowTrend["qta_usciti"]
                    //"value" => number_format($row["qta_Passaggio"], 0, ',', '.')
                    )
                );
    
            }
            
        $arrDataTrend["categories"]=array(array("category"=>$categoryArray));

            // creating dataset object
            $arrDataTrend["dataset"] = array(array("seriesName"=> "Soci Entrati", "data"=>$dataseries1), array("seriesName"=> "Soci Usciti", "data"=>$dataseries2));

      $jsonEncodedData = json_encode($arrDataTrend);
      // chart object
      $msChart = new FusionCharts("msline", "myChart3" , "100%", "400", "amm3", "json", $jsonEncodedData);
      $msChart->render();
             
   }

// -------------------------------------------------------------------------------
// MEDIA PER ANNO
// -------------------------------------------------------------------------------
$media = "  SELECT '2020' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2020
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2021' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2021
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2022' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2022
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2023' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2023
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2024' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2024
            GROUP BY substr(AnnoMeseRichiesta,1,4)
	    UNION
            SELECT '2025' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati,
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) -
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2025
            GROUP BY substr(AnnoMeseRichiesta,1,4)
	    UNION
            SELECT '2026' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati,
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) -
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2026
            GROUP BY substr(AnnoMeseRichiesta,1,4)

            ORDER BY 1 ASC
                ";


$result_media = $dbhandle->query($media) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_media = '<table class="table table-bordered table-hover" border="1" width="20%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="5" align="center">MEDIA BANCA</td>
        </tr>
        <tr class="table-secondary">
          <td>Anno</td>
          <td align="right">Qtà Mesi</td>
          <td align="right">Media Entrati</td>
          <td align="right">Media Usciti</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_media = mysqli_fetch_array($result_media)) {

    if (number_format($row_media['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:lightgreen;"';}

    $tab_media .= "<tr>
            <td>".$row_media['AnnoMeseRichiesta']."</td>
            <td align='right'>".number_format($row_media['MesiCount'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_entrati'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_usciti'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_media['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_media .= '</table>';

// -------------------------------------------------------------------------------
// DETTAGLIO AREE
// -------------------------------------------------------------------------------
$dett_aree = "  SELECT Area, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(soci_Incremento))  as Incremento,
                round(sum(soci_Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff,

                round(sum(CapitaleSociale_inizio)) as CS_Inizio, 
                round(sum(CapitaleSociale_Incremento))  as CS_Incremento,
                round(sum(CapitaleSociale_Decremento))  as CS_Decremento,
                round(sum(CapitaleSociale_fine))  as CS_Fine,
                (round(sum(CapitaleSociale_fine)) - 
                 round(sum(CapitaleSociale_inizio))) as CS_Diff

                FROM tmp_soci_inout2, tab_psw
                WHERE Periodo_inizio >= '".$_GET['datain']."' AND Periodo_fine <= '".$_GET['dataout']."'
                AND tmp_soci_inout2.Filiale < 999
                AND tmp_soci_inout2.Filiale = cast(tab_psw.Filiale as unsigned)
                ".$condizionefiliale2."
                GROUP BY Area WITH ROLLUP
                ";
// echo $dett_aree;
$result_dett_aree = $dbhandle->query($dett_aree) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_aree = '<table class="table table-bordered table-hover" border="1" width="40%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="6" align="center">SITUAZIONE AREA</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_aree = mysqli_fetch_array($result_dett_aree)) {

    if (number_format($row_dett_aree['Diff'],0,',','.') < 0 ) {$coloreA = ' style="color:red;"' ;} else {$coloreA = ' style="color:lightgreen;"';}

    if (number_format($row_dett_aree['CS_Diff'],0,',','.') < 0 ) {$colore2 = ' style="color:red;"' ;} else {$colore2 = ' style="color:lightgreen;"';}


    $tab_dett_aree .= "<tr>
            <td>".$row_dett_aree['Area']."</td>
            <td align='right'>".number_format($row_dett_aree['SociInizio'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_aree['CS_Inizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Incremento'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_aree['CS_Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Decremento'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_aree['CS_Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['SociFine'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_aree['CS_Fine'],0,',','.')."</td>
            <td align='right' ".$coloreA.">".number_format($row_dett_aree['Diff'],0,',','.')."<br>
                <small ".$colore2.">&euro;&nbsp;".number_format($row_dett_aree['CS_Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_aree .= '</table>';     

// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
$dett_fil = "   SELECT Area, cast(tmp_soci_inout2.Filiale as unsigned) as Filiale, 
                desc_Filiale as NomeFiliale, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(soci_Incremento))  as Incremento,
                round(sum(soci_Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff,

                (
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) / round(sum(Soci_inizio))
                ) * 100 as Soci_Perc,

                round(sum(CapitaleSociale_inizio)) as CS_Inizio, 
                round(sum(CapitaleSociale_Incremento))  as CS_Incremento,
                round(sum(CapitaleSociale_Decremento))  as CS_Decremento,
                round(sum(CapitaleSociale_fine))  as CS_Fine,
                (round(sum(CapitaleSociale_fine)) - 
                 round(sum(CapitaleSociale_inizio))) as CS_Diff,

                (
                (round(sum(CapitaleSociale_fine)) - 
                 round(sum(CapitaleSociale_inizio))) / round(sum(CapitaleSociale_inizio))
                ) * 100 as CS_Perc

                FROM tmp_soci_inout2, tab_psw
                WHERE Periodo_inizio >= '".$_GET['datain']."' AND Periodo_fine <= '".$_GET['dataout']."' 
                AND tmp_soci_inout2.Filiale < 999
                AND tmp_soci_inout2.Filiale = cast(tab_psw.Filiale as unsigned)
                ".$condizionefiliale2."
                GROUP BY Area, tmp_soci_inout2.Filiale, desc_Filiale  
                ";
// echo $dett_fil;
$result_dett_fil = $dbhandle->query($dett_fil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_fil = '<table  class="table table-bordered table-hover" border="1" width="50%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="9" align="center">SITUAZIONE FILIALE
          <a href="#inizio"><i class="fas fa-arrow-up fa-1x text-gray-300 col-auto"></i></a>
          </td>
</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td>Filiale</td>
          <td>Nome Filiale</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
          <td align="right">&#177; %</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_fil = mysqli_fetch_array($result_dett_fil)) {

    if (number_format($row_dett_fil['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:lightgreen;"';}

    if (number_format($row_dett_fil['CS_Diff'],0,',','.') < 0 ) {$colore2 = ' style="color:red;"' ;} else {$colore2 = ' style="color:lightgreen;"';}

    $tab_dett_fil .= "<tr>
            <td>".$row_dett_fil['Area']."</td>
            <td>".$row_dett_fil['Filiale']."</td>
            <td>".$row_dett_fil['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_fil['SociInizio'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_fil['CS_Inizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Incremento'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_fil['CS_Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Decremento'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_fil['CS_Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['SociFine'],0,',','.')."<br>
                <small style='color:gray;'>&euro;&nbsp;".number_format($row_dett_fil['CS_Fine'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['Diff'],0,',','.')."<br>
                <small ".$colore2.">&euro;&nbsp;".number_format($row_dett_fil['CS_Diff'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['Soci_Perc'],2,',','.')." %<br>
                <small ".$colore2.">".number_format($row_dett_fil['CS_Perc'],2,',','.')." %</td>
          </tr>
        ";
  }

$tab_dett_fil .= '</table>';     



// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI PER ANNO E MESE
// -------------------------------------------------------------------------------

$select_dett_annomese = " 
SELECT
Area, tmp_soci_inout3.FILIALE as Filiale, desc_Filiale as NomeFiliale, 'Qtà' as Tipo, 
round(SUM(CASE WHEN annomese = ".$adesso_anno."01 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Gen_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."01 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Gen_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."02 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Feb_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."02 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Feb_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."03 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Mar_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."03 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Mar_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."04 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Apr_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."04 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Apr_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."05 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Mag_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."05 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Mag_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."06 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Giu_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."06 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Giu_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."07 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Lug_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."07 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Lug_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."08 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Ago_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."08 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Ago_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."09 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Set_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."09 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Set_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."10 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Ott_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."10 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Ott_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."11 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Nov_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."11 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Nov_O,
round(SUM(CASE WHEN annomese = ".$adesso_anno."12 AND TIPO = 'IN' THEN QTA ELSE 0 END),0) as Dic_I,
round(SUM(CASE WHEN annomese = ".$adesso_anno."12 AND TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Dic_O,
round(SUM(CASE WHEN TIPO = 'IN' THEN QTA ELSE 0 END),0) as Tot_I,
round(SUM(CASE WHEN TIPO = 'OUT' THEN QTA ELSE 0 END),0) as Tot_O,
(round(SUM(CASE WHEN TIPO = 'IN' THEN QTA ELSE 0 END),0) -
round(SUM(CASE WHEN TIPO = 'OUT' THEN QTA ELSE 0 END),0) ) as Diff
from tmp_soci_inout3, tab_psw
WHERE tmp_soci_inout3.Filiale = cast(tab_psw.Filiale as unsigned)
group by area, filiale
";

$tab_dett_annomese = 
'<table  class="table table-bordered table-hover" border="1" width="50%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="28" align="center">ANDAMENTO MENSILE FILIALE
          <a href="#inizio"><i class="fas fa-arrow-up fa-1x text-gray-300 col-auto"></i></a>
          </td>

        </tr>
        <tr class="table-secondary">
          <td rowspan="2" valign="center">Area</td>
          <td rowspan="2" valign="center">Filiale</td>
          <td rowspan="2" valign="center">Nome Filiale</td>
          <td colspan="2" align="center">Gennaio</td>
          <td colspan="2" align="center">Febbraio</td>
          <td colspan="2" align="center">Marzo</td>
          <td colspan="2" align="center">Aprile</td>
          <td colspan="2" align="center">Maggio</td>
          <td colspan="2" align="center">Giugno</td>
          <td colspan="2" align="center">Luglio</td>
          <td colspan="2" align="center">Agosto</td>
          <td colspan="2" align="center">Settembre</td>
          <td colspan="2" align="center">Ottobre</td>
          <td colspan="2" align="center">Novembre</td>
          <td colspan="2" align="center">Dicembre</td>
          <td align="right" rowspan="2">&#177;</td>
        </tr>
        <tr class="table-secondary">
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
          <td align="center"><i style="color:lightgreen;" class="fas fa-arrow-up fa-1x"></i></td>
          <td align="center"><i style="color:red;" class="fas fa-arrow-down fa-1x"></i></td>
        </tr>
        ';

$result_dett_annomese = $dbhandle->query($select_dett_annomese) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  while ($row_dett_annomese = mysqli_fetch_array($result_dett_annomese)) {

    if (number_format($row_dett_annomese['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:lightgreen;"';}

    $tab_dett_annomese .= "<tr>
            <td>".$row_dett_annomese['Area']."</td>
            <td>".$row_dett_annomese['Filiale']."</td>
            <td>".$row_dett_annomese['NomeFiliale']."</td>
            <td align='center'>".number_format($row_dett_annomese['Gen_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Gen_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Feb_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Feb_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Mar_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Mar_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Apr_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Apr_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Mag_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Mag_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Giu_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Giu_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Lug_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Lug_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Ago_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Ago_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Set_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Set_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Ott_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Ott_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Nov_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Nov_O'],0,',','.')."</td>
            <td align='center'>".number_format($row_dett_annomese['Dic_I'],0,',','.')."</td>
            <td align='center' style='color:red;'>".number_format($row_dett_annomese['Dic_O'],0,',','.')."</td>

            <td align='right' ".$colore.">".number_format($row_dett_annomese['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

  $tab_dett_annomese .= '</table>';  

// -------------------------------------------------------------------------------
// IMPIEGHI E RACCOLTA
// -------------------------------------------------------------------------------
$dett_fil_ir = "SELECT Area, view_impieghiraccolta.FILIALE_CAPOFILA as Filiale, 
                desc_Filiale as NomeFiliale, 
                sum(TOT_ACCORDATO) as TOT_ACCORDATO,
                sum(TOT_UTILIZZATO) as TOT_UTILIZZATO,
                sum(TOT_RACCOLTA) as TOT_RACCOLTA,
                sum(TOT_RACCOLTA_DIRETTA) as TOT_RACCOLTA_DIRETTA,
                sum(RACCOLTA_INDIRETTA_AMMINISTRATA) as RACCOLTA_INDIRETTA_AMMINISTRATA,
                sum(RACCOLTA_INDIRETTA_GESTITA) as RACCOLTA_INDIRETTA_GESTITA,
                sum(TOT_RACCOLTA_INDIRETTA) as TOT_RACCOLTA_INDIRETTA,
                sum(QTA_RAPP_IMPIEGHI) as QTA_RAPP_IMPIEGHI,
                sum(QTA_RAPP_RACCOLTA) as QTA_RAPP_RACCOLTA,
                sum(QTA_RAPP_TITOLI) as QTA_RAPP_TITOLI
                FROM view_impieghiraccolta, tab_psw
                WHERE  
                    view_impieghiraccolta.FILIALE_CAPOFILA < 999
                AND view_impieghiraccolta.FILIALE_CAPOFILA  = tab_psw.Filiale
                ".$condizionefiliale3."
                GROUP BY Area, view_impieghiraccolta.FILIALE_CAPOFILA, desc_Filiale  
                ";

$result_dett_ir = $dbhandle->query($dett_fil_ir) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_ir = '<table  class="table table-bordered table-hover" border="1" width="50%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="13" align="center">RACCOLTA e IMPIEGHI
           <a href="#inizio"><i class="fas fa-arrow-up fa-1x text-gray-300 col-auto"></i></a>
          </td>
        </tr>
        <tr class="table-secondary">
          <td rowspan="2">Area</td>
          <td rowspan="2">Filiale</td>
          <td rowspan="2">Nome Filiale</td>
          <td colspan="7" align="center">RACCOLTA</td>
          <td colspan="3" align="center">IMPIEGHI</td>
        </tr>
        <tr class="table-secondary">
          <td align="right">Qtà Rapporti Raccolta</td>
          <td align="right">Qtà Rapporti Titoli</td>
          <td align="right">Totale Raccolta</td>
          <td align="right">Totale Raccolta Diretta</td>
          <td align="right">Totale Raccolta Indiretta</td>
          <td align="right">Raccolta Indiretta Amministrata</td>
          <td align="right">Raccolta Indiretta Gestita</td>

          <td align="right">Qtà Rapporti Impieghi</td>
          <td align="right">Totale Accordato</td>
          <td align="right">Totale Utilizzato</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_ir = mysqli_fetch_array($result_dett_ir)) {

    $tab_dett_ir .= "<tr>
            <td>".$row_dett_ir['Area']."</td>
            <td>".$row_dett_ir['Filiale']."</td>
            <td>".$row_dett_ir['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_ir['QTA_RAPP_RACCOLTA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['QTA_RAPP_TITOLI'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['TOT_RACCOLTA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['TOT_RACCOLTA_DIRETTA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['TOT_RACCOLTA_INDIRETTA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['RACCOLTA_INDIRETTA_AMMINISTRATA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['RACCOLTA_INDIRETTA_GESTITA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['QTA_RAPP_IMPIEGHI'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['TOT_ACCORDATO'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_ir['TOT_UTILIZZATO'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_ir .= '</table>';     



// -------------------------------------------------------------------------------
// COSTRUZIONE LAYOUT
// -------------------------------------------------------------------------------

echo '
<table border="0" align="center" width="65%">
';

if ($rif == 'Filiale') 
{
  echo '       
       <tr>     
       <td colspan="3"><div id="amm3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  '; 
}
elseif ( ($rif == 'Area') OR ($rif == '') )
{
  echo '       
  <tr>     
       <td colspan="3"><div id="amm2"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  <tr>     
       <td colspan="3"><div id="amm3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>
  </table>
  <br>';

    echo '<P style="page-break-before: always">';

 echo '
<table border="0" align="center" width="65%">
 <tr>     
       <td valign="top">'.$tab_media.'</td>
       <td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td valign="top">'.$tab_dett_aree.'</td>
  </tr>
  </table>';
}

    echo '<P style="page-break-before: always">';

?>

<script>
function selectElementContents(el) {
    var body = document.body, range, sel;
    if (document.createRange && window.getSelection) {
        range = document.createRange();
        sel = window.getSelection();
        sel.removeAllRanges();
        try {
            range.selectNodeContents(el);
            sel.addRange(range);
        } catch (e) {
            range.selectNode(el);
            sel.addRange(range);
        }
    } else if (body.createTextRange) {
        range = body.createTextRange();
        range.moveToElementText(el);
        range.select();
    }
}
</script>

<?php

 echo '<a name="sit"></a>
<table border="0" align="center" width="65%" id="dataTable">
  <tr>     
       <td valign="top" colspan="3"><br>'.$tab_dett_fil.'</td>
  </tr>
  </table>';

    echo '<P style="page-break-before: always">';

 echo '<a name="sitmens"></a>
<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" colspan="3"><br>'.$tab_dett_annomese.'</td>
  </tr>
</table>
<br>
';

 echo '<a name="racimp"></a>
<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" colspan="3"><br>'.$tab_dett_ir.'</td>
  </tr>
</table>
<br>
';
//<!-- <center><h4>Aggiungere link per elenco</h4></center> -->

?>

<br><center>
            <a href="../lista_ammissioni.php?filiale=<?php echo $filiale; ?>" style="text-color:white;" target="_blank">Visualizza lista dettaglio Ammissioni</a>

<?php
// ---------------------------------------------------------
// Estrazione su file dettaglio SOCI Filiale
// ---------------------------------------------------------
if ($_GET['f'] < 999) {

$dett_fil_ir2 = "
                SELECT
                    ir.FILIALE_CAPOFILA AS FIL,
                    s.IDSOCIO,
                    ir.NAG ,
                    CONCAT(s.INTESTAZIONE_A, ' ', s.INTESTAZIONE_B) AS NOMINATIVO,
                    ir.DESC_STATUS AS STATUS,
                    s.DATA_NASCITA,
                    s.DATA_ENTRATA,
                    s.SESSO,
                    s.SETTORISTA,
                    s.FILIALE_RAPP,
                    s.NUM_RAPP,
                    CONCAT(s.PROF_ATTIVITA, ' - ',m.DESCRIZIONE) AS PROFESSIONE,
                    s.VIA_RES,
                    s.CAP_RES,
                    s.DESCR_COM_RES,
                    ir.DATA_RIFERIMENTO AS DATA_RIFERIMENTO,
                    sum(ir.N_RAPP_RACC_NO_DT) AS QTA_RAPP_RACCOLTA,
                    sum(ir.N_RAPP_RACC_DOSSIER) AS QTA_RAPP_TITOLI,
                    sum(ir.N_RAPP_IMPIEGHI) AS QTA_RAPP_IMPIEGHI,
                    round(sum(ir.TOT_RACCOLTA),0) AS TOT_RACCOLTA,
                    round(sum(ir.TOT_RACC_DIRETTA),0) AS TOT_RACCOLTA_DIRETTA,
                    round(sum(ir.TOT_RACC_INDIRETTA),0) AS TOT_RACCOLTA_INDIRETTA,
                    round(sum(ir.RACC_IND_AMMINISTRATA),0) AS RACCOLTA_INDIRETTA_AMMINISTRATA,
                    round(sum(ir.RACC_IND_GESTITA),0) AS RACCOLTA_INDIRETTA_GESTITA,
                    round(sum(ir.TOT_ACCORDATO),0) AS TOT_ACCORDATO,
                    round(sum(ir.TOT_UTILIZZATO),0) AS TOT_UTILIZZATO
                FROM
                    sds_soci as s 
                    INNER JOIN sds_soci_impieghiraccolta as ir 
                    ON s.NAG = ir.NAG
                    INNER JOIN sds_soci_merceologico as m
                    ON s.PROF_ATTIVITA = m.TIPO
                WHERE ir.SOCIO_ISTITUTO = 1
                ".$condizionefiliale4."
                AND ir.FILIALE_CAPOFILA < 999
                AND s.TIPO_NAG = 'PF'
                AND m.RIFERIMENTO = 'Professione'
                GROUP BY
                    ir.NAG
                ";

$result_dett_ir2 = $dbhandle->query($dett_fil_ir2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_ir2 = '';
$myfile = fopen("../tmp/ir2.csv", "w");
$tab_dett_ir2 .= "FIL;IDSOCIO;NAG;NOMINATIVO;STATUS;DATA_NASCITA;DATA_ENTRATA;SESSO;SETTORISTA;FIL_CC;NUM_CC;PROFESSIONE;VIA;CAP;COMUNE;DATA_RIF;QTA_RAPP_RACCOLTA;QTA_RAPP_TITOLI;QTA_RAPP_IMPIEGHI;TOT_RACCOLTA;TOT_RACCOLTA_DIRETTA;RACCOLTA_INDIRETTA;RACCOLTA_INDIRETTA_AMMINISTRATA;RACCOLTA_INDIRETTA_GESTITA;TOT_ACCORDATO;TOT_UTILIZZATO\n";


  // iterating over each data and pushing it into $arrData array
  while ($row_dett_ir2 = mysqli_fetch_array($result_dett_ir2)) {


    $tab_dett_ir2 .= 
            $row_dett_ir2['FIL']
            .";".$row_dett_ir2['IDSOCIO']
            .";".$row_dett_ir2['NAG']
            .";".$row_dett_ir2['NOMINATIVO']
            .";".$row_dett_ir2['STATUS']
            .";".$row_dett_ir2['DATA_NASCITA']
            .";".$row_dett_ir2['DATA_ENTRATA']
            .";".$row_dett_ir2['SESSO']
            .";".$row_dett_ir2['SETTORISTA']
            .";".$row_dett_ir2['FILIALE_RAPP']
            .";".$row_dett_ir2['NUM_RAPP']
            .";".$row_dett_ir2['PROFESSIONE']
            .";".$row_dett_ir2['VIA_RES']
            .";".$row_dett_ir2['CAP_RES']
            .";".$row_dett_ir2['DESCR_COM_RES']
            .";".$row_dett_ir2['DATA_RIFERIMENTO']
            .";".number_format($row_dett_ir2['QTA_RAPP_RACCOLTA'],0,',','.')
            .";".number_format($row_dett_ir2['QTA_RAPP_TITOLI'],0,',','.')
            .";".number_format($row_dett_ir2['TOT_RACCOLTA'],0,',','.')
            .";".number_format($row_dett_ir2['TOT_RACCOLTA_DIRETTA'],0,',','.')
            .";".number_format($row_dett_ir2['TOT_RACCOLTA_INDIRETTA'],0,',','.')
            .";".number_format($row_dett_ir2['RACCOLTA_INDIRETTA_AMMINISTRATA'],0,',','.')
            .";".number_format($row_dett_ir2['RACCOLTA_INDIRETTA_GESTITA'],0,',','.')
            .";".number_format($row_dett_ir2['QTA_RAPP_IMPIEGHI'],0,',','.')
            .";".number_format($row_dett_ir2['TOT_ACCORDATO'],0,',','.')
            .";".number_format($row_dett_ir2['TOT_UTILIZZATO'],0,',','.')
            ."\n";

  }

    fwrite($myfile, $tab_dett_ir2);
    fclose($myfile);

echo '  <br><br>
            <a class="btn btn-outline-warning" id="pulsante" href="../tmp/ir2.csv">Scarica l\'elenco delle Persone Fisiche con dettagli</a>
            </center>';
}

// closing database connection      
$dbhandle->close();             
?>


    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>


