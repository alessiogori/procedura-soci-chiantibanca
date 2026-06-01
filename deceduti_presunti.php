<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Deceduti Presunti';

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
// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------
$adesso = date("d.m.Y");

// Calcolo data di partenza per Under 35
// *****************************************************************************
$date = new DateTime();             
$date->modify('- 35 years');                 // 35 anni indietro da oggi
$DataLimiteU30 = $date->format('Ymd');     // formato output AAAAMMDD



if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/2025'; // <-- variare anno
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

// Controllo se la richiesta arriva   
if (!isset($_GET['filiale']))
    {$condizionefiliale = '';
     $condizionefiliale2 = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
     $rif = 'BANCA';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']) OR ($_GET['area']) == "")   
     {    
     $condizionefiliale = ' AND FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND sds_soci.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND sds_soci.FILIALE_CAPOFILA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }


echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO.'</h2>
            <p align="left">
            '.$rif.' '.$filiale.'<br>
            Si tratta di posizione <u>attive come Socio</u> ma con presenza di documentazione in Isidoc che presume stato di morte. E\' necessaria la verifica da parte della Filiale e la segnalazione eventuale all\'Ufficio Soci.
            </p>
      </div>

';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> </center><br>';

      // QUERY DI DETTAGLIO DECEDUTI NON LIQUIDATI
      $select_dett =   "
                        SELECT 
                            A.FILIALE_CAPOFILA,
                            I.NAG AS NAG,
                            S.IDSOCIO AS IDSOCIO,
                            A.INTESTAZIONE_A + ' ' + A.INTESTAZIONE_B as INTESTAZIONE, 
                            CASE A.STATO_NAG
                            WHEN '0' THEN 'POTENZIALE'
                            WHEN '1' THEN 'EFFETTIVO'
                            WHEN '2' THEN 'EX CLIENTE'
                            END AS STATONAG, 
                            CASE A.SOCIO_ISTITUTO
                            WHEN '1' THEN 'SOCIO'
                            WHEN '9' THEN 'EX SOCIO'
                            END AS STATOSOCIO,      
                            PF.DATA_NASCITA,
                            PF.DATA_DECESSO,
                            COD_TIPO_DOCUMENTO,
                            DESCR_TIPO_DOCUMENTO,
                            DATA_DOCUMENTO,
                            DATA_ACQUISIZIONE,
                            PRESENZA_DOCUMENTO AS PDF,
                            COD_USER_INS AS MATRICOLA,
                            PRESENZA_NOTE AS NOTE
                        FROM ISIDOC_DOCUMENTI_PERSONALE AS I, SOCI_ANAGRAFICA AS S, 
                             ANAG_NAG as A, ANAG_PERSONE_FISICHE as PF
                        WHERE 
                        A.SOCIO_ISTITUTO = '1'
                        AND COD_TIPO_DOCUMENTO IN 
                        ('DI000006TP000006',
                         'DI000006TP000003',
                         'DI000006TP000004',
                         'DI000006TP000007',
                         'DI000006TP000001'
                        )                     
                        AND A.TIPO_NAG = 'PF'  
                        AND S.NAG = I.NAG
                        AND S.NAG = A.NAG
                        AND S.NAG = PF.NAG
                        ".$condizionefiliale."
                        ORDER BY FILIALE_CAPOFILA, INTESTAZIONE
                        "; 

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0"  >
        <tr class="table-secondary">
          <td align="left"><small style="font-size:13px;">Filiale</td>
          <td align="left"><small style="font-size:13px;">NAG</td>
          <td align="left"><small style="font-size:13px;">IDSocio</td>
          <td align="left"><small style="font-size:13px;">Intestazione</td>
          <td align="left"><small style="font-size:13px;">Stato NAG</td>
          <td align="left"><small style="font-size:13px;">Stato Socio</td>
          <td align="left"><small style="font-size:13px;">Data Nascita</td>
          <td align="left"><small style="font-size:13px;">Data Decesso</td>
          <td align="left"><small style="font-size:13px;">Tipo Documento</td>
          <td align="left"><small style="font-size:13px;">Data Documento</td>
          <td align="left"><small style="font-size:13px;">Data Acquisizione</td>
          <td align="left"><small style="font-size:13px;">Matricola</td>
          <td align="left"><small style="font-size:13px;">Note</td>
        </tr>';


      $result_dett = odbc_exec($connect, $select_dett);
      while ($dati_dett = odbc_fetch_object($result_dett)) {

        $linksocio = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati_dett->IDSOCIO."'>".$dati_dett->INTESTAZIONE."</a>";

        if ($dati_dett->STATONAG == 'EX CLIENTE') {$colore = 'color:cyan;';} else {$colore ='';}

        echo "<tr>
                <td><small style='font-size:13px;'>".$dati_dett->FILIALE_CAPOFILA."</td>
                <td><small style='font-size:13px;'>".$dati_dett->NAG."</td>
                <td><small style='font-size:13px;'>".$dati_dett->IDSOCIO."</td>
                <td><small style='font-size:13px;'>".$linksocio."</td>
                <td><small style='font-size:13px;".$colore."'>".$dati_dett->STATONAG."</td>
                <td><small style='font-size:13px;'>".$dati_dett->STATOSOCIO."</td>
                <td><small style='font-size:13px;'>".$dati_dett->DATA_NASCITA."</td>
                <td><small style='font-size:13px;'>".$dati_dett->DATA_DECESSO."</td>
                <td><small style='font-size:13px;'>".$dati_dett->DESCR_TIPO_DOCUMENTO."</td>
                <td><small style='font-size:13px;'>".$dati_dett->DATA_DOCUMENTO."</td>
                <td><small style='font-size:13px;'>".$dati_dett->DATA_ACQUISIZIONE."</td>
                <td><small style='font-size:13px;'>".$dati_dett->MATRICOLA."</td>
                <td><small style='font-size:13px;'>".$dati_dett->NOTE."</td>
              </tr>
            ";


        }


      echo '</table>';        


?>
