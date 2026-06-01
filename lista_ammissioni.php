<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Ammissioni Soci';

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
     $condizionefiliale = ' AND s1.Filiale_Capofila in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND s1.Filiale_Capofila in ('.$_GET['filiale'].')';
     $condizionefiliale2 = ' AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }



      // QUERY DI CONTEGGIO
      $select_qta =   "
                        SELECT count(*) as QTA
                        FROM
                        sds_soci as s1
                        LEFT JOIN sds_soci_certificati as sc ON s1.IDSOCIO = sc.IDSOCIO
                        LEFT JOIN sds_soci as s2 ON s1.IDSOCIO_SUB = s2.IDSOCIO 
                        WHERE
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta = mysqli_query($connection, $select_qta);
      while ($dati_qta = mysqli_fetch_array($result_qta)) {
        $qta_soci = $dati_qta['QTA'];
        }

      // QUERY DI CONTEGGIO ETA MEDIA 
      $select_qtaE =   "
                        SELECT round(avg(s1.eta)) as ETAMEDIA
                        FROM
                        sds_soci as s1
                        LEFT JOIN sds_soci_certificati as sc ON s1.IDSOCIO = sc.IDSOCIO
                        LEFT JOIN sds_soci as s2 ON s1.IDSOCIO_SUB = s2.IDSOCIO 
                        WHERE s1.TIPO_NAG = 'PF'
                        AND
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qtaE = mysqli_query($connection, $select_qtaE);
      while ($dati_qtaE = mysqli_fetch_array($result_qtaE)) {
        $eta_media = $dati_qtaE['ETAMEDIA'];
        }
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">Ammissioni a Socio</h2>
            <p class="mb-0 justify-content-between align-items-left">'.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].' - <b>Qtà Soci ammessi: '.$qta_soci.'</b> (Età media : '.$eta_media.')</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
      </div>
';
      // QUERY DI RICERCA
      // #MZ 06/02/25 - Check date errate
      function isValidDate($date, $format = 'd/m/Y') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
      }
      if(!isValidDate($_GET['datain']) || !isValidDate($_GET['dataout'])) die("date non corrette.");
      // #MZ 06/02/25 - FINE Check date errate
      $select_query =   "
                        SELECT
                        CAST(s1.FILIALE_CAPOFILA AS UNSIGNED) as FILIALE_CAPOFILA,
                        s1.IDSOCIO,
                        s1.NAG,
                        concat(s1.INTESTAZIONE_A, ' ', s1.INTESTAZIONE_B) as SOCIO_AMMESSO,
                        s1.DATA_ENTRATA,
                        sc.NUMERO_AZIONI,
                        sc.VALORE_AZIONI,
                        s1.ACQUISTO_PERIOD,
                        s1.IDSOCIO_SUB,
                        s2.NAG as NAG_DEFUNTO,
                        concat(s2.INTESTAZIONE_A, ' ', s2.INTESTAZIONE_B) as SOCIO_DEFUNTO,
                        s2.DATA_DECESSO,
                        CONCAT(cc.FILIALE_RAPP, '/', cc.NUM_RAPP) AS CONTO,
                        CONCAT(cc.COD_CLASSE, ' - ', cc.DESCRIZIONE) AS PRODOTTOCC
                        FROM
                        sds_soci as s1
                        LEFT JOIN sds_soci_certificati as sc ON s1.IDSOCIO = sc.IDSOCIO
                        LEFT JOIN sds_soci as s2 ON s1.IDSOCIO_SUB = s2.IDSOCIO 
                        LEFT JOIN sds_soci_prodotto_cc AS cc 
                            ON s1.COD_RAPP = cc.COD_RAPP
                            AND s1.FILIALE_RAPP = cc.FILIALE_RAPP
                            and s1.NUM_RAPP = cc.NUM_RAPP
                        WHERE
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                        ".$condizionefiliale."         
                        group by s1.NAG
                        ORDER BY 1, 4
                        "; 

                       // echo $select_query;

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-secondary">
          <td style="position: sticky;top: 0" align="left"><small>Filiale</td>
          <td style="position: sticky;top: 0" align="left"><small>IDSocio</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">U35</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio Ammesso</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Entrata</td>
          <td style="position: sticky;top: 0" align="left"><small>Azioni</td>
          <td style="position: sticky;top: 0" align="left"><small>Importo</td>
          <td style="position: sticky;top: 0" align="left"><small>Pack</td>
          <td style="position: sticky;top: 0" align="left"><small>IDSocio Defunto</td>
          <td style="position: sticky;top: 0" align="left"><small>NAG Defunto</td>
          <td style="position: sticky;top: 0" align="left"><small>Socio Defunto</td>
          <td style="position: sticky;top: 0" align="left"><small>Data Decesso</td>
          <td style="position: sticky;top: 0" align="left"><small>C/C Proc.Soci</td>
          <td style="position: sticky;top: 0" align="left"><small>Prodotto C/C</td>
        </tr>';


      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/ammissioni.csv", "w");
      $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
      $contenutoOutput .= "FILIALE;IDSOCIO;NAG;U35;SOCIO_AMMESSO;DATA_ENTRATA;AZIONI;IMPORTO;PACK;IDSOCIO_DEFUNTO;NAG_DEFUNTO;SOCIO_DEFUNTO;DATA_DECESSO;CONTO;PRODOTTOCC\n";

      $num_U35 = 0;

      $result = mysqli_query($connection, $select_query);
      while ($dati = mysqli_fetch_array($result)) {


          // QUERY RICERCA MOTIVAZIONI INSERITE
          $select_motiv =   "
                            SELECT count(*) as qta
                            FROM
                            tab_motivazioni 
                            WHERE
                            nag = ".$dati['NAG']."
                            "; 

          $result_motiv = mysqli_query($connection, $select_motiv);

          while ($dati_motiv = mysqli_fetch_array($result_motiv)) {
            
            if ($dati_motiv['qta'] > 0) 
            {
                 // VISUALIZZA MOTIVAZIONE
                $ico_motivazione = '
                <a href="motivazioni.php?start=IN&filiale='.$dati['FILIALE_CAPOFILA'].'&nag='.$dati['NAG'].'&data_domanda=&nome='.$dati['SOCIO_AMMESSO'].'" target="_blank">
                <i class="fas fa-sticky-note" style="color:lightgreen;" title="Motivazione ammissione PRESENTE"></i>
                </a>
                ';
            }
            else
            {
                // FORM DI INSERIMENTO MOTIVAZIONE
                $ico_motivazione = '
                <a href="motivazioni_form.php?action=&start=IN&filiale='.$dati['FILIALE_CAPOFILA'].'&nag='.$dati['NAG'].'&data_domanda=&nome='.$dati['SOCIO_AMMESSO'].'" target="_blank">
                <i class="fas fa-sticky-note" style="color:#FACB7E;" title="Motivazione ammissione DA INSERIRE"></i>
                </a>';
            }
          }

          //------------------

                  // Verifica se il richiedente è Under 35
            $select_u35 =   "
                            SELECT
                                DATA_NASCITA                             
                            FROM
                                ANAG_PERSONE_FISICHE
                            WHERE
                                NAG =  ".$dati['NAG']."
                            UNION
                            SELECT
                                '' AS DATA_NASCITA                             
                            FROM
                                ANAG_PERSONE_GIURIDICHE
                            WHERE
                                NAG =  ".$dati['NAG'];

            $result_u35 = odbc_exec($connect, $select_u35);
            while ($dati_u35 = odbc_fetch_object($result_u35)) {

            if ($dati_u35->DATA_NASCITA > $DataLimiteU30)
            {$u35 = 'U35'; 
             $coloreU35 = 'color:white; background-color: green;' ;
             $num_U35++;}
            else
            {$u35 = '--'; 
             $coloreU35 = '';
             }

            $data_nascita_formattata = substr($dati_u35->DATA_NASCITA,6,2).'-'.
                                        substr($dati_u35->DATA_NASCITA,4,2).'-'.
                                        substr($dati_u35->DATA_NASCITA,0,4); 


            if ($dati['IDSOCIO_SUB'] == 0) {$IDSOCIO_SUB = '';} else {$IDSOCIO_SUB  = $dati['IDSOCIO_SUB'];}

            $linksocio_ammesso = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati['IDSOCIO']."'>".$dati['SOCIO_AMMESSO']."</a>";
            $linksocio_defunto = "<a class='text-green-light' href='sqldati_schedasocio.php?id=".$IDSOCIO_SUB."'>".$dati['SOCIO_DEFUNTO']."</a>";

            if ($dati['ACQUISTO_PERIOD'] == 'Y') {$pack = 'SI';} else {$pack = '';}

                echo "<tr>
                        <td><small>".$dati['FILIALE_CAPOFILA']."</td>
                        <td><small>".$dati['IDSOCIO']."</td>
                        <td><small>".$dati['NAG']."</td>
                        <td><small style='font-size:11px;".$coloreU35."'
                            title='".$data_nascita_formattata."'>".$u35."</td>
                        <td><small>".$linksocio_ammesso."</td>
                        <td><small>".$dati['DATA_ENTRATA'].'&nbsp;&nbsp;'.$ico_motivazione."</td>
                        <td align='right'><small>".number_format($dati['NUMERO_AZIONI'],0,',','.')."</td>
                        <td align='right'><small>".number_format($dati['VALORE_AZIONI'],2,',','.')."</td>
                        <td><small>".$pack."</td>
                        <td><small>".$IDSOCIO_SUB."</td>
                        <td><small>".$dati['NAG_DEFUNTO']."</td>
                        <td><small>".$linksocio_defunto."</td>
                        <td><small>".$dati['DATA_DECESSO']."</td>
                        <td><small>".$dati['CONTO']."</td>
                        <td><small>".$dati['PRODOTTOCC']."</td>
                      </tr>
                    ";

                  $contenutoOutput .= 
                         $dati['FILIALE_CAPOFILA'].";"
                        .$dati['IDSOCIO'].";"
                        .$dati['NAG'].";"
                        .$u35.";"
                        .$dati['SOCIO_AMMESSO'].";"
                        .$dati['DATA_ENTRATA'].";"
                        .number_format($dati['NUMERO_AZIONI'],0,',','.').";"
                        .number_format($dati['VALORE_AZIONI'],2,',','.').";"
                        .$pack.";"
                        .$IDSOCIO_SUB.";"
                        .$dati['NAG_DEFUNTO'].";"
                        .$dati['SOCIO_DEFUNTO'].";"
                        .$dati['DATA_DECESSO'].";"
                        .$dati['CONTO'].";"
                        .$dati['PRODOTTOCC']."\n";

          }
              
      }
      
      echo '</table>';        

      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);

echo '<br><center>Under 35 nr.'.$num_U35.'<br>
            <a href="tmp/ammissioni.csv" style="text-color:white;" target="_blank">Scarica il tracciato delle Ammissioni</a>
            </center>';



// Close ODBC
odbc_close($connect);


?>
