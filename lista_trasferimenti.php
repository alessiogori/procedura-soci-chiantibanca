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
          @import "css/fontawesome-free/css/all.min.css";
        </style> 

        <body><br><br>
        ';

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');
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

// Calcolo data di partenza per Under 30
// *****************************************************************************
$date = new DateTime();             
$date->modify('- 35 years');                 // 35 anni indietro da oggi
$DataLimiteU30 = $date->format('Ymd');     // formato output AAAAMMDD


if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = $inizioanno;
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
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
     $condizionefiliale = ' AND SOCI_ANAGRAFICA_01.FILIALE in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND SOCI_ANAGRAFICA_01.FILIALE in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }



echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">Trasferimenti tra Soci e Soci/Non Soci</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].'</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
      </div>
';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> &nbsp;&nbsp; <a href="#u30" style="text-decoration:none;">&dArr;</a></center><br>';

      // QUERY DI RICERCA
      $select_query =   "
                        SELECT
                               ANAG_NAG_01.FILIALE_CAPOFILA AS FILIALE_CAPOFILA,
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
                              ".$condizionefiliale."
                        ORDER BY ANAG_NAG_01.FILIALE_CAPOFILA, SOCI_ANAGRAFICA_01.FILIALE, ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B
                        "; 

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-secondary">        
          <!-- <td align="left" rowspan="2"><small>Area</td> -->
          <td style="position: sticky;top: 0" align="left"><small></td>
          <td align="center" colspan="4" style="position: sticky;top: 0;background-color:#F79DB7;color:black;">TRASFERENTE <b>&#8595;</td>
          <td align="center" colspan="4" style="position: sticky;top: 0;background-color:lightgreen;color:black;">RICEVENTE <b>&#8593;</td>
          <td align="center" colspan="5" style="position: sticky;top: 0;background-color:lightgray;color:black;">DATI OPERAZIONE</td>
        </tr>
        <tr class="table-secondary">
          <td style="position: sticky;top: 0" align="left"><small>Filiale</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG Trasf</td>
          <td style="position: sticky;top: 0" align="left"><small>IDSocio Trasf</td>
          <td style="position: sticky;top: 0" align="left"><small>Az.Residue</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio Trasferente</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG Ricev</td>
          <td style="position: sticky;top: 0" align="left"><small>IDSocio Ricev</td>
          <td style="position: sticky;top: 0" align="left"><small>Under 35</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio Ricevente</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Movimento</td>
          <td style="position: sticky;top: 0" align="left"><small>Tipo Movimento</td>
          <td style="position: sticky;top: 0" align="left"><small>Azioni</td>
          <td style="position: sticky;top: 0" align="left"><small>Importo</td>
          <td style="position: sticky;top: 0" align="left"><small>Sovrapprezzo</td>
        </tr>';


      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/trasferimenti.csv", "w");
      $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
      $contenutoOutput .= "FILIALE;NAG_TRASFERENTE;IDSOCIO_TRASFERENTE;AZ_RESIDUE;SOCIO_TRASFERENTE;NAG_RICEVENTE;IDSOCIO_RICEVENTE;UNDER35;SOCIO_RICEVENTE;DATA_MOVIMENTO;CTIPOMOV;AZIONI;IMPORTO;ISOVRAPPREZZO\n";


      $result = odbc_exec($connect, $select_query);
      while($dati = odbc_fetch_object($result)) {

          // QUERY RICERCA MOTIVAZIONI INSERITE
          $select_motiv =   "
                            SELECT count(*) as qta
                            FROM
                            tab_motivazioni 
                            WHERE
                            nag = ".$dati->NAG_RICEVENTE."
                            "; 

          $result_motiv = mysqli_query($connection, $select_motiv);

          while ($dati_motiv = mysqli_fetch_array($result_motiv)) {
            
            if ($dati_motiv['qta'] > 0) 
            {
                 // VISUALIZZA MOTIVAZIONE
                $ico_motivazione = '
                <a href="motivazioni.php?start=IN&filiale='.$dati->FILIALE.'&nag='.$dati->NAG_RICEVENTE.'&data_domanda=&nome='.$dati->SOCIO_RICEVENTE.'" target="_blank">
                <i class="fas fa-sticky-note" style="color:lightgreen;" title="Motivazione ammissione PRESENTE"></i>
                </a>
                ';
            }
            else
            {
                $ico_motivazione = '';
            }
          }

          // --------------------------------------------------------------------------------------

            // Verifica se il richiedente è Under 35

            $select_u35 =   "
                            SELECT
                                DATA_NASCITA                             
                            FROM
                                sds_soci
                            WHERE
                            --    TIPO_NAG = 'PF'
                            -- AND
                                NAG =  ".$dati->NAG_RICEVENTE;                                

            $result_u35 = mysqli_query($connection, $select_u35);
            while ($dati_u35 = mysqli_fetch_array($result_u35)) {

            $data_movimento_formattata = substr($dati->DATA_MOVIMENTO,6,4).
                                        substr($dati->DATA_MOVIMENTO,4,2).
                                        substr($dati->DATA_MOVIMENTO,0,2); 

            $startDate = new DateTime($data_movimento_formattata);
            $endDate = new DateTime($dati_u35['DATA_NASCITA']);
            $difference = $startDate->diff($endDate);

            if ($difference->y <= 35) 
                {$u35 = 'SI'; 
                 $coloreU35 = 'color:white; background-color: green;' ;} 
            else 
                {$u35 = '--'; 
                $coloreU35 = '';}

            /*
            if ($dati_u35['DATA_NASCITA'] > $DataLimiteU30)
            {$u35 = 'SI'; 
             $coloreU35 = 'color:white; background-color: green;' ;}
            else
            {$u35 = '--'; 
             $coloreU35 = '';}
            */

            $data_nascita_formattata = substr($dati_u35['DATA_NASCITA'],6,2).'-'.
                                        substr($dati_u35['DATA_NASCITA'],4,2).'-'.
                                        substr($dati_u35['DATA_NASCITA'],0,4); 
            }

          // --------------------------------------------------------------------------------------

            // Controllo il numero di quote possedute dall'eventuale TRASFERENTE
            $select_trasferente =   "
                            SELECT 
                                    IDSOCIO, NUMERO_AZIONI, VALORE_AZIONI
                            FROM
                                    sds_soci_certificati 
                            WHERE
                                    IDSOCIO = ".$dati->IDSOCIO_TRASFERENTE;

            $qry_trasferente = mysqli_query($connection, $select_trasferente);

            if (mysqli_num_rows($qry_trasferente) <> 0) {

            while($dati_trasferente = mysqli_fetch_array($qry_trasferente)){ 

            $Differenza_Azioni = $dati_trasferente['NUMERO_AZIONI'] ;

                if  (($Differenza_Azioni < 33) && ($u35 == '--'))
                {$titolo_trasferente = 'CHI CEDE RESTA CON MENO DI 33 AZIONI !!'; 
                 $coloreTrasferente = 'color:white; background-color: gray;' ;}
                else
                {$titolo_trasferente = ''; 
                 $coloreTrasferente = '';}

                  }
            }
            else
                {$Differenza_Azioni = 0;
                 $titolo_trasferente = ''; 
                 $coloreTrasferente = '';}


          // --------------------------------------------------------------------------------------



            $linksocio_trasf = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati->IDSOCIO_TRASFERENTE."'>".$dati->SOCIO_TRASFERENTE."</a>";
            $linksocio_ricev = "<a class='text-green-light' href='sqldati_schedasocio.php?id=".$dati->IDSOCIO_RICEVENTE."'>".$dati->SOCIO_RICEVENTE."</a>";

            if ($dati->CTIPOMOV == 'TR') {$tipo = 'Trasferimento (old)'; $regolamento='<small>&nbsp;(Verificare se necessario regolamento)</small>';}
            elseif ($dati->CTIPOMOV == 'CO') {$tipo = 'Compravendita'; $regolamento='<small>&nbsp;(Verificare se necessario regolamento)</small>';}
            elseif ($dati->CTIPOMOV == 'SU') {$tipo = 'Successione'; $regolamento='&nbsp;Successione';}
            elseif ($dati->CTIPOMOV == 'DO') {$tipo = 'Donazione'; $regolamento='<small>&nbsp;(Verificare se necessario regolamento)</small>';}
            elseif ($dati->CTIPOMOV == 'FU') {$tipo = 'Fusione'; $regolamento='&nbsp;Fusione';}
            else $tipo = '';

                      //  <!-- <td><small>".Area($dati->FILIALE)."</td> -->

                echo "<tr>
                        <td><small>".$dati->FILIALE_CAPOFILA."</td>
                        <td><small>".$dati->NAG_TRASFERENTE."</td>
                        <td><small>".$dati->IDSOCIO_TRASFERENTE."</td>
                        <td style='font-size:11px;".$coloreTrasferente."'
                            title='".$titolo_trasferente."'>".$Differenza_Azioni."</td>
                        <td><small>".$linksocio_trasf."</td>
                        <td><small>".$dati->NAG_RICEVENTE."</td>
                        <td><small>".$dati->IDSOCIO_RICEVENTE."</td>
                        <td style='font-size:11px;".$coloreU35."'
                            title='".$data_nascita_formattata."'>".$u35."</td>
                        <td><small>".$linksocio_ricev."</td>
                        <td><small>".$dati->DATA_MOVIMENTO.$ico_motivazione."</td>
                        <td><small title=".$tipo.">".$dati->CTIPOMOV.$regolamento."</td>
                        <td align='right'><small>".number_format($dati->AZIONI,0,',','.')."</td>
                        <td align='right'><small>".number_format($dati->IMPORTO,2,',','.')."</td>
                        <td align='right'><small>".number_format($dati->ISOVRAPPREZZO,2,',','.')."</td>
                      </tr>
                    ";

                  $contenutoOutput .= 
                         $dati->FILIALE.";"
                        .$dati->NAG_TRASFERENTE.";"
                        .$dati->IDSOCIO_TRASFERENTE.";"
                        .$Differenza_Azioni.";"
                        .$dati->SOCIO_TRASFERENTE.";"
                        .$dati->NAG_RICEVENTE.";"
                        .$dati->IDSOCIO_RICEVENTE.";"
                        .$u35.";"
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
