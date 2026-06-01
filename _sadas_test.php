<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'ANAG_NAG';
$TITOLO = 'Trasferimenti Soci';

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

// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------
if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/2022';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }

$adesso = date("d.m.Y");

echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">Trasferimenti tra Soci e Soci/Non Soci</h2>
            <p class="mb-0 justify-content-between align-items-left">Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
      </div>
';
      // QUERY DI RICERCA
      $select_query =   "
                        SELECT
                               SOCI_ANAGRAFICA_01.FILIALE AS FILIALE,
                               SOCI_ANAGRAFICA_01.NAG AS NAG_TRASFERENTE ,
                               SOCI_MOVIMENTI_01.IDSOCIO AS IDSOCIO_TRASFERENTE ,
                               ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B AS SOCIO_TRASFERENTE,
                               SOCI_ANAGRAFICA_02.NAG AS NAG_RICEVENTE ,
                               SOCI_MOVIMENTI_01.CSOCIO_TRASF AS IDSOCIO_RICEVENTE,
                               ANAG_NAG_02.INTESTAZIONE_A + ' ' + ANAG_NAG_02.INTESTAZIONE_B AS SOCIO_RICEVENTE,
                               SOCI_MOVIMENTI_01.DATA_MOVIMENTO AS DATA_MOVIMENTO ,
                               abs(SOCI_MOVIMENTI_01.IMPORTO / 30.33)  as AZIONI,
                               abs(SOCI_MOVIMENTI_01.IMPORTO) AS IMPORTO ,
                               abs(SOCI_MOVIMENTI_01.ISOVRAPPREZZO) AS ISOVRAPPREZZO ,
                               SOCI_MOVIMENTI_01.CTIPOMOV AS CTIPOMOV
                        FROM
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN SOCI_MOVIMENTI AS SOCI_MOVIMENTI_01  ON (SOCI_ANAGRAFICA_02.IDSOCIO = SOCI_MOVIMENTI_01.CSOCIO_TRASF ) ,
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN ANAG_NAG AS ANAG_NAG_02  ON (SOCI_ANAGRAFICA_02.NAG = ANAG_NAG_02.NAG ) ,
                              SOCI_MOVIMENTI  AS SOCI_MOVIMENTI_01 INNER JOIN SOCI_ANAGRAFICA AS SOCI_ANAGRAFICA_01  ON (SOCI_MOVIMENTI_01.IDSOCIO = SOCI_ANAGRAFICA_01.IDSOCIO ) ,
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_01 INNER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_ANAGRAFICA_01.NAG = ANAG_NAG_01.NAG )  
                        WHERE
                              SOCI_MOVIMENTI_01.CTIPOMOV IN ( 'TR','CO','FU','DO','SU' ) 
                        AND
                              SOCI_MOVIMENTI_01.DATA_MOVIMENTO >=  '".$_GET['datain']."'
                        AND
                              SOCI_MOVIMENTI_01.DATA_MOVIMENTO <=  '".$_GET['dataout']."'
                        ORDER BY SOCI_ANAGRAFICA_01.FILIALE, SOCI_ANAGRAFICA_01.NAG
                        "; 

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-secondary">        
          <td align="left" rowspan="2"><small>Filiale</td>
          <td align="center" colspan="3" style="background-color:#F79DB7;color:black;">TRASFERENTE <b>&#8595;</td>
          <td align="center" colspan="3" style="background-color:lightgreen;color:black;">RICEVENTE <b>&#8593;</td>
          <td align="center" colspan="5" style="background-color:lightgray;color:black;">DATI OPERAZIONE</td>
        </tr>
        <tr class="table-secondary">
          <td align="left"><small>NAG Trasf</td>
          <td align="left"><small>IDSocio Trasf</td>
          <td align="left"><small>Socio Trasferente</td>
          <td align="left"><small>NAG Ricev</td>
          <td align="left"><small>IDSocio Ricev</td>
          <td align="left"><small>Socio Ricevente</td>
          <td align="left"><small>Data Movimento</td>
          <td align="left"><small>Tipo Movimento</td>
          <td align="left"><small>Azioni</td>
          <td align="left"><small>Importo</td>
          <td align="left"><small>Sovrapprezzo</td>
        </tr>';


      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/trasferimenti.csv", "w");
      $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
      $contenutoOutput .= "FILIALE;NAG_TRASFERENTE;IDSOCIO_TRASFERENTE;SOCIO_TRASFERENTE;NAG_RICEVENTE;IDSOCIO_RICEVENTE;SOCIO_RICEVENTE;DATA_MOVIMENTO;CTIPOMOV;AZIONI;IMPORTO;ISOVRAPPREZZO\n";


      $result = odbc_exec($connect, $select_query);
      while($dati = odbc_fetch_object($result)) {

            $linksocio_trasf = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati->IDSOCIO_TRASFERENTE."'>".$dati->SOCIO_TRASFERENTE."</a>";
            $linksocio_ricev = "<a class='text-green-light' href='sqldati_schedasocio.php?id=".$dati->IDSOCIO_RICEVENTE."'>".$dati->SOCIO_RICEVENTE."</a>";

            if ($dati->CTIPOMOV == 'TR') {$tipo = 'TR - Trasferimento (old)'; }
            elseif ($dati->CTIPOMOV == 'CO') {$tipo = 'CO - Compravendita'; }
            elseif ($dati->CTIPOMOV == 'SU') {$tipo = 'SU - Successione'; }
            elseif ($dati->CTIPOMOV == 'DO') {$tipo = 'DO - Donazione'; }
            elseif ($dati->CTIPOMOV == 'FU') {$tipo = 'FU - Fusione'; }
            else $tipo = '';

                echo "<tr>
                        <td><small>".$dati->FILIALE."</td>
                        <td><small>".$dati->NAG_TRASFERENTE."</td>
                        <td><small>".$dati->IDSOCIO_TRASFERENTE."</td>
                        <td><small>".$linksocio_trasf."</td>
                        <td><small>".$dati->NAG_RICEVENTE."</td>
                        <td><small>".$dati->IDSOCIO_RICEVENTE."</td>
                        <td><small>".$linksocio_ricev."</td>
                        <td><small>".$dati->DATA_MOVIMENTO."</td>
                        <td><small>".$tipo."</td>
                        <td align='right'><small>".number_format($dati->AZIONI,0,',','.')."</td>
                        <td align='right'><small>".number_format($dati->IMPORTO,2,',','.')."</td>
                        <td align='right'><small>".number_format($dati->ISOVRAPPREZZO,2,',','.')."</td>
                      </tr>
                    ";

                  $contenutoOutput .= 
                         $dati->FILIALE.";"
                        .$dati->NAG_TRASFERENTE.";"
                        .$dati->IDSOCIO_TRASFERENTE.";"
                        .$dati->SOCIO_TRASFERENTE.";"
                        .$dati->NAG_RICEVENTE.";"
                        .$dati->IDSOCIO_RICEVENTE.";"
                        .$dati->SOCIO_RICEVENTE.";"
                        .$dati->DATA_MOVIMENTO.";"
                        .$tipo.";"
                        .number_format($dati->AZIONI,0,',','.').";"
                        .number_format($dati->IMPORTO,2,',','.').";"
                        .number_format($dati->ISOVRAPPREZZO,2,',','.')."\n";

      //	echo $dati->NAG_TRASFERENTE . " " . $dati->IDSOCIO_TRASFERENTE . " " . $dati->SOCIO_TRASFERENTE . " " . $dati->NAG_RICEVENTE  . " " . $dati->IDSOCIO_RICEVENTE  . " " . $dati->SOCIO_RICEVENTE  . " " . $dati->DATA_MOVIMENTO  . " " . $dati->IMPORTO . " " . $dati->ISOVRAPPREZZO . " " . $dati->CTIPOMOV ;
      //$SQL->Query($insert_query);
          
      }
      
      echo '</table>';        

      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);

echo '<br><center>
            <a href="tmp/trasferimenti.csv" style="text-color:white;" target="_blank">Scarica il tracciato dei Trasferimenti</a>
            </center>';



// Close ODBC
odbc_close($connect);


?>
