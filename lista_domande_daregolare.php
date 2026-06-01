<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Domande a Socio (da regolare)';

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
$adesso = date("d/m/Y");

// Calcolo data di partenza per Unnder 30
// *****************************************************************************
$date = new DateTime();             
$date->modify('- 30 years');                 // 30 anni indietro da oggi
$DataLimiteU30 = $date->format('Ymd');     // formato output AAAAMMDD



if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/2022';
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
     $condizionefiliale = ' AND SOCI_DOMANDE_01.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND SOCI_DOMANDE.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND SOCI_DOMANDE_01.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND SOCI_DOMANDE.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
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
            <small>Ordinato per SOGLIA e INTESTAZIONE</small>
            </p>
      </div>

';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> &nbsp;&nbsp; <a href="#u30" style="text-decoration:none;">&dArr;</a></center><br>';

      // QUERY DI RICERCA
      $select_query =   "
                        SELECT
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO AS ISIDOC ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE as NOTE,
                             SOCI_DOMANDE_01.NAG AS NAG_DOMANDA ,
                             SOCI_DOMANDE_01.XNOME AS INTESTAZIONE_DOMANDA ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
                             SOCI_DOMANDE_01.DATA_DELIBERA  ,
                             SOCI_DOMANDE_01.FILIALE_DOMANDA ,
                             SOCI_DOMANDE_01.SOGLIA  ,
                             SOCI_DOMANDE_01.CTIPODOM  ,
                             SOCI_DOMANDE_01.NAZIONI  ,
                             SOCI_DOMANDE_01.RIACQUISTO_AZ AS FONDO_RIACQUISTO ,
                             SOCI_DOMANDE_01.MOTIVO_RECESSO  ,
                             SOCI_DOMANDE_01.TIPO_TRASF  ,
                             SOCI_DOMANDE_01.IDSOCIO_DOM AS IDSOCIO_TRASFERENTE ,
                             SOCI_DOMANDE_01.IDSOCIO_RIC AS IDSOCIO_RICEVENTE ,
                             SOCI_DOMANDE_01.NAG_RIC AS NAG_RICEVENTE ,
                             (ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B) AS INTESTAZIONE_RICEVENTE ,
                             SOCI_DOMANDE_01.IDSOCIO_SUB AS IDSOCIO_SUBENTRO ,
                             ANAG_NAG_02.NAG AS NAG_SUBENTRO ,
                             (ANAG_NAG_02.INTESTAZIONE_A + ' ' + ANAG_NAG_02.INTESTAZIONE_B) AS INTESTAZIONE_SUBENTRO ,
                             ANAG_NAG_03.PROF_ATTIVITA AS PROFESSIONE ,
                             ANAG_NAG_03.TIPO_NAG,
                             SOCI_DOMANDE_01.FLAG_RES_ZONA_BANCA ,
                             ANAG_NAG_03.PA_3 AS PIAZZA ,
                             ANAG_NAG_03.DESCR_COM_RES AS COMUNE_RESIDENZA ,
                             ANAG_NAG_03.PROVINCIA_RES AS PROVINCIA_RESIDENZA ,
                             SOCI_DOMANDE_01.PROF_COMUNE AS PROFESSIONE_NEL_COMUNE ,
                             SOCI_DOMANDE_01.PROF_PRESSO AS PROFESSIONE_NEL_COMUNE_PRESSO ,
                             SOCI_DOMANDE_01.IMM_COMUNE AS IMMOBILI_NEL_COMUNE ,
                             SOCI_DOMANDE_01.IMM_PRESSO AS IMMOBILI_NEL_COMUNE_PRESSO ,
                             SOCI_DOMANDE_01.FILIALE_RAPP,
                             SOCI_DOMANDE_01.NUM_RAPP
                        FROM
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ISIDOC_CONTRATTI AS ISIDOC_CONTRATTI_01  ON (SOCI_DOMANDE_01.NAG = ISIDOC_CONTRATTI_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_DOMANDE_01.NAG_RIC = ANAG_NAG_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN SOCI_ANAGRAFICA AS SOCI_ANAGRAFICA_01  ON (SOCI_DOMANDE_01.IDSOCIO_SUB = SOCI_ANAGRAFICA_01.IDSOCIO ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ANAG_NAG AS ANAG_NAG_03  ON (SOCI_DOMANDE_01.NAG = ANAG_NAG_03.NAG ) ,
                            SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_01 INNER JOIN ANAG_NAG AS ANAG_NAG_02  ON (SOCI_ANAGRAFICA_01.NAG = ANAG_NAG_02.NAG )  
                        WHERE
                            SOCI_DOMANDE_01.CTIPOESITO =  'DR'  
                        AND
                            ISIDOC_CONTRATTI_01.COD_CONTRATTO =  'SOCICN02' 
                        AND 
                            SOCI_DOMANDE_01.CTIPODOM not in ('DL','DR')
                        ".$condizionefiliale."
                        GROUP BY
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE ,
                             SOCI_DOMANDE_01.NAG  ,
                             SOCI_DOMANDE_01.XNOME ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
                             SOCI_DOMANDE_01.DATA_DELIBERA  ,
                             SOCI_DOMANDE_01.FILIALE_DOMANDA ,
                             SOCI_DOMANDE_01.SOGLIA  ,
                             SOCI_DOMANDE_01.CTIPODOM  ,
                             SOCI_DOMANDE_01.NAZIONI  ,
                             SOCI_DOMANDE_01.RIACQUISTO_AZ ,
                             SOCI_DOMANDE_01.MOTIVO_RECESSO  ,
                             SOCI_DOMANDE_01.TIPO_TRASF  ,
                             SOCI_DOMANDE_01.IDSOCIO_DOM  ,
                             SOCI_DOMANDE_01.IDSOCIO_RIC  ,
                             SOCI_DOMANDE_01.NAG_RIC  ,
                             (ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B)  ,
                             SOCI_DOMANDE_01.IDSOCIO_SUB ,
                             ANAG_NAG_02.NAG  ,
                             (ANAG_NAG_02.INTESTAZIONE_A + ' ' + ANAG_NAG_02.INTESTAZIONE_B)  ,
                             ANAG_NAG_03.PROF_ATTIVITA ,
                             ANAG_NAG_03.TIPO_NAG,
                             SOCI_DOMANDE_01.FLAG_RES_ZONA_BANCA ,
                             ANAG_NAG_03.PA_3  ,
                             ANAG_NAG_03.DESCR_COM_RES ,
                             ANAG_NAG_03.PROVINCIA_RES  ,
                             SOCI_DOMANDE_01.PROF_COMUNE  ,
                             SOCI_DOMANDE_01.PROF_PRESSO  ,
                             SOCI_DOMANDE_01.IMM_COMUNE  ,
                             SOCI_DOMANDE_01.IMM_PRESSO  ,
                             SOCI_DOMANDE_01.FILIALE_RAPP,
                             SOCI_DOMANDE_01.NUM_RAPP
                        ORDER BY 4
                        "; 

                        //echo $select_query;

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0"  >
        <tr class="table-secondary">
          <td align="left"><small style="font-size:11px;">Nr.</td>
          <td align="left"><small style="font-size:11px;">PDF</td>
          <td align="left"><small style="font-size:11px;">Note</td>
          <td align="left"><small style="font-size:11px;">NAG Domanda</td>
          <td align="left"><small style="font-size:11px;">Intestazione Domanda</td>
          <td align="left"><small style="font-size:11px;">Fil</td>
          <td align="left"><small style="font-size:11px;">Conto</td>
          <td align="right"><small style="font-size:11px;">Saldo</td>
          <td align="left"><small style="font-size:11px;">Data Domanda</td>
          <td align="left"><small style="font-size:11px;">Data Delibera</td>
          <td align="left"><small style="font-size:11px;">Time GG</td>
          <td align="left"><small style="font-size:11px;">Filiale</td>
          <td align="left"><small style="font-size:11px;">Soglia</td>
          <td align="left"><small style="font-size:11px;">Tipo</td>
          <td align="left"><small style="font-size:11px;">Azioni</td>
          <td align="left"><small style="font-size:11px;">Res.Zona</td>
          <td align="left"><small style="font-size:11px;">TipoNAG</td>
          <td align="left"><small style="font-size:11px;">Piazza</td>
          <td align="left"><small style="font-size:11px;">Residenza</td>
          <td align="left"><small style="font-size:11px;">Professione Presso</td>
          <td align="left"><small style="font-size:11px;">Immobili Presso</td>

        </tr>';

    $progressivo = 1;

      // Preparo l'esportazione su file
     // $myfilectrasf = fopen("tmp/ammissioni.csv", "w");
     // $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
     // $contenutoOutput .= "FILIALE;IDSOCIO;NAG;SOCIO_AMMESSO;DATA_ENTRATA;AZIONI;IMPORTO;PACK;IDSOCIO_DEFUNTO;NAG_DEFUNTO;SOCIO_DEFUNTO;DATA_DECESSO\n";

      $result = odbc_exec($connect, $select_query);
      while ($dati = odbc_fetch_object($result)) {

        $select_query_saldo =   "
                    SELECT  
                     CG_SALDI.SALDO_DIV_CONTO/100 AS SALDO_CONTO , 
                     CC_SALDI_DISPON.SALDO_DISPONIBILE/100 AS SALDO_DISPONIBILE
                    FROM    
                    CG_SALDI INNER JOIN CC_SALDI_DISPON ON (CG_SALDI.NUM_RAPP = CC_SALDI_DISPON.RAPPORTO    
                    AND 
                    CG_SALDI.FILIALE = CC_SALDI_DISPON.FILIALE      
                    AND 
                    CG_SALDI.COD_RAPP = CC_SALDI_DISPON.COD_RAPPORTO ) 
                    WHERE   
                    CG_SALDI.COD_RAPP = 2     
                    AND 
                     CG_SALDI.FILIALE = ".$dati->FILIALE_RAPP."
                    AND 
                    CG_SALDI.NUM_RAPP =  ".$dati->NUM_RAPP."
                    ";

        $result_saldo = odbc_exec($connect, $select_query_saldo);
        while ($dati_saldo = odbc_fetch_object($result_saldo)) {

            $saldo_conto = $dati_saldo->SALDO_CONTO;
            $saldo_disponibile = $dati_saldo->SALDO_DISPONIBILE;

            if ($saldo_disponibile >= (($dati->NAZIONI*30.33)+($dati->NAZIONI*1))) {$colore = ' color:lightgreen;';} 
                else {$colore = '';}
            // if ($saldo_disponibile >= 1026) {$colore = ' color:lightgreen;';} 
            //    else {$colore = '';}
        }

            if ($dati->NAZIONI < 33) {$colore2 = ' color:cyan;';} 
                else {$colore2 = '';}

            if ($dati->CTIPODOM <> "DA") {$colore2 = ' color:cyan;';} 
                else {$colore2 = '';}

        $data_delibera_formattata = substr($dati->DATA_DELIBERA,6,4).'-'.
                                    substr($dati->DATA_DELIBERA,3,2).'-'.
                                    substr($dati->DATA_DELIBERA,0,2);
        $data_adesso = date("Y-m-d");

        if (diff_date_ingiorni($data_delibera_formattata, $data_adesso) > 60 ) 
            {$coloreTimeGG = 'color:red;' ; }
        elseif ( (diff_date_ingiorni($data_delibera_formattata, $data_adesso) >= 55 ) 
                 &&
                 (diff_date_ingiorni($data_delibera_formattata, $data_adesso) <= 60 ) )
            {$coloreTimeGG = 'color:orange;' ; }
        else {$coloreTimeGG = '' ; }

                echo "<tr>
                        <td><small style='font-size:12px;'>".$progressivo."</td>
                        <td><small style='font-size:12px;'>".$dati->ISIDOC."</td>
                        <td><small style='font-size:12px;'>".$dati->NOTE."</td>
                        <td><small style='font-size:12px;'>".$dati->NAG_DOMANDA."</td>
                        <td><small style='font-size:12px;'>".$dati->INTESTAZIONE_DOMANDA."</td>
                        <td><small style='font-size:12px;'>".$dati->FILIALE_RAPP."</td>
                        <td><small style='font-size:12px;'>".$dati->NUM_RAPP."</td>
                        <td align='right'><small style='font-size:12px;".$colore."'>
                                ".$saldo_conto."&nbsp;Cont<br>
                                ".$saldo_disponibile."&nbsp;Disp</td>
                        <td><small style='font-size:12px;'>".$dati->DATA_DOMANDA."</td>
                        <td><small style='font-size:12px;'>".$dati->DATA_DELIBERA."</td>
                        <td><small style='font-size:12px;".$coloreTimeGG."'>".diff_date_ingiorni($data_delibera_formattata, $data_adesso)."</td>
                        <td><small style='font-size:12px;'>".$dati->FILIALE_DOMANDA."</td>
                        <td><small style='font-size:12px;'>".$dati->SOGLIA."</td>
                        <td><small style='font-size:12px;".$colore2."'>".$dati->CTIPODOM."</td>
                        <td><small style='font-size:12px;".$colore2."'>".$dati->NAZIONI."</td>
                        <td><small style='font-size:12px;'>".$dati->FLAG_RES_ZONA_BANCA."</td>
                        <td><small style='font-size:12px;'>".$dati->TIPO_NAG."</td>
                        <td><small style='font-size:12px;'>".$dati->PIAZZA."</td>
                        <td><small style='font-size:12px;'>".$dati->COMUNE_RESIDENZA." ".$dati->PROVINCIA_RESIDENZA."</td>
                        <td><small style='font-size:12px;'>".$dati->PROFESSIONE_NEL_COMUNE." ".$dati->PROFESSIONE_NEL_COMUNE_PRESSO."</td>
                        <td><small style='font-size:12px;'>".$dati->IMMOBILI_NEL_COMUNE." ".$dati->IMMOBILI_NEL_COMUNE_PRESSO."</td>
                      </tr>
                    ";

                $progressivo++;
/*
                  $contenutoOutput .= 
                         $dati['FILIALE_CAPOFILA'].";"
                        .$dati['IDSOCIO'].";"
                        .$dati['NAG'].";"
                        .$dati['SOCIO_AMMESSO'].";"
                        .$dati['DATA_ENTRATA'].";"
                        .number_format($dati['NUMERO_AZIONI'],0,',','.').";"
                        .number_format($dati['VALORE_AZIONI'],2,',','.').";"
                        .$pack.";"
                        .$IDSOCIO_SUB.";"
                        .$dati['NAG_DEFUNTO'].";"
                        .$dati['SOCIO_DEFUNTO'].";"
                        .$dati['DATA_DECESSO']."\n";
*/
          
    }
      
      echo '</table>';        

      // fwrite($myfilectrasf, $contenutoOutput);
      // fclose($myfilectrasf);


// Close ODBC
odbc_close($connect);


?>
