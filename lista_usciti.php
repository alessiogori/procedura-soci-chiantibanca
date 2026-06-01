<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Soci Usciti';

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
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
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
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qtaE = mysqli_query($connection, $select_qtaE);
      while ($dati_qtaE = mysqli_fetch_array($result_qtaE)) {
        $eta_media = $dati_qtaE['ETAMEDIA'];
        }
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">Soci Usciti</h2>
            <p class="mb-0 justify-content-between align-items-left">
            Il conto indicato (e relativo prodotto) è quello presente in anagrafica socio. Verificare se esistono altri C/C con condizioni socio.<br>
            '.$rif.' '.$filiale.' - Dal '.$_GET['datain'].' al '.$_GET['dataout'].' - <b>Qtà Soci usciti: '.$qta_soci.'</b> (Età media : '.$eta_media.')</p>
            <p class="mb-0 justify-content-between align-items-left">Parametri: ?datain=gg/mm/aaaa (eventuale &dataout=gg/mm/aaaa)</p>
      </div>
';
      // QUERY DI RICERCA
      $select_query =   "
                        SELECT
                        CAST(s1.FILIALE_CAPOFILA AS UNSIGNED) as FILIALE_CAPOFILA,
                        s1.IDSOCIO,
                        s1.NAG,
                        concat(s1.INTESTAZIONE_A, ' ', s1.INTESTAZIONE_B) as SOCIO_AMMESSO,
                        s1.STATO_NAG,
                        s1.SOCIO_ISTITUTO,
                        s1.DATA_ENTRATA,
                        s1.DATA_USCITA,
                        sc.NUMERO_AZIONI,
                        sc.VALORE_AZIONI,
                        s1.CTIPMOVUSCITA,
                        CONCAT(cc.FILIALE_RAPP, '/', cc.NUM_RAPP) AS CONTO,
                        CONCAT(cc.COD_CLASSE, ' - ', cc.DESCRIZIONE) AS PRODOTTOCC,
                        CASE cc.STATO
                        WHEN 0 THEN 'Attivo'
                        WHEN 1 THEN 'Estinto'
                        END as STATO
                        FROM
                        sds_soci as s1
                        LEFT JOIN sds_soci_certificati as sc ON s1.IDSOCIO = sc.IDSOCIO
                        LEFT JOIN sds_soci_prodotto_cc AS cc 
                            ON s1.COD_RAPP = cc.COD_RAPP
                            AND s1.FILIALE_RAPP = cc.FILIALE_RAPP
                            and s1.NUM_RAPP = cc.NUM_RAPP
                        WHERE
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                        ".$condizionefiliale."         
                        group by s1.NAG
                        ORDER BY 1, 4
                        "; 

                       // echo $select_query;

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
        <tr class="table-secondary">
          <td align="left"><small>Filiale</td>
          <td align="left"><small>IDSocio</td>
          <td align="left"><small>NAG</td>
          <td align="left"><small style="font-size:11px;">U35</td>
          <td align="left"><small>Socio Ammesso</td>
          <td align="left"><small>Data Entrata</td>
          <td align="left"><small>Data Uscita</td>
          <td align="left"><small>Azioni</td>
          <td align="left"><small>Importo</td>
          <td align="left"><small>Motivo Uscita</td>
          <td align="left"><small>Stato NAG</td>
          <td align="left"><small>C/C Proc.Soci</td>
          <td align="left"><small>Prodotto C/C</td>
          <td align="left"><small>Stato C/C</td>
        </tr>';


      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/usciti.csv", "w");
      $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
      $contenutoOutput .= "FILIALE;IDSOCIO;NAG;U35;SOCIO_AMMESSO;DATA_ENTRATA;DATA_USCITA;AZIONI;IMPORTO;MOTIVOUSCITA;STATONAG;CONTO;PRODOTTOCC;STATO\n";

      $num_U35 = 0;

      $result = mysqli_query($connection, $select_query);
      while ($dati = mysqli_fetch_array($result)) {


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


            $linksocio_ammesso = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati['IDSOCIO']."'>".$dati['SOCIO_AMMESSO']."</a>";


                if ($dati['SOCIO_ISTITUTO'] == 9)
                {
                    switch ($dati['CTIPMOVUSCITA']) {
                      case "MO":    // morte
                       $uscita = 'USCITO PER MORTE';
                       $motivo = $dati['CTIPMOVUSCITA'];
                          break;
                      case "ES":    // esclusione
                       $uscita = 'USCITO PER ESCLUSIONE';
                       $motivo = $dati['CTIPMOVUSCITA'];
                          break;
                      case "RE":    // recesso
                       $uscita = 'USCITO PER RECESSO';
                       $motivo = $dati['CTIPMOVUSCITA'];
                          break;
                      case "  ":    // cessione a banca
                       $uscita = 'USCITO PER CESSIONE QUOTE A BANCA';
                       $motivo = 'CB';
                          break;

                      default:
                      $uscita = '';
                      exit;
                    }
                } 

                    switch ($dati['STATO_NAG']) {
                      case "0":    // morte
                       $statonag = 'POT';
                       $statonag_csv = 'POT';
                          break;
                      case "1":    // esclusione
                       $statonag = 'CLI';
                       $statonag_csv = 'CLI';
                          break;
                      case "2":    // recesso
                       $statonag = '<span style="color:red;">EX</span>';
                       $statonag_csv = 'EX';
                          break;

                      default:
                      $statonag = '';
                      exit;
                    }
                
                echo "<tr>
                        <td><small>".$dati['FILIALE_CAPOFILA']."</td>
                        <td><small>".$dati['IDSOCIO']."</td>
                        <td><small>".$dati['NAG']."</td>
                        <td><small style='font-size:11px;".$coloreU35."'
                            title='".$data_nascita_formattata."'>".$u35."</td>
                        <td><small>".$linksocio_ammesso."</td>
                        <td><small>".$dati['DATA_ENTRATA']."</td>
                        <td><small>".$dati['DATA_USCITA']."</td>
                        <td align='right'><small>".number_format($dati['NUMERO_AZIONI'],0,',','.')."</td>
                        <td align='right'><small>".number_format($dati['VALORE_AZIONI'],2,',','.')."</td>
                        <td><small title='".$uscita."''>".$motivo."</td>
                        <td><small>".$statonag."</td>
                        <td><small>".$dati['CONTO']."</td>
                        <td><small>".$dati['PRODOTTOCC']."</td>
                        <td><small>".$dati['STATO']."</td>
                      </tr>
                    ";

                  $contenutoOutput .= 
                         $dati['FILIALE_CAPOFILA'].";"
                        .$dati['IDSOCIO'].";"
                        .$dati['NAG'].";"
                        .$u35.";"
                        .$dati['SOCIO_AMMESSO'].";"
                        .$dati['DATA_ENTRATA'].";"
                        .$dati['DATA_USCITA'].";"
                        .number_format($dati['NUMERO_AZIONI'],0,',','.').";"
                        .number_format($dati['VALORE_AZIONI'],2,',','.').";"
                        .$dati['CTIPMOVUSCITA'].";"
                        .$statonag_csv.";"
                        .$dati['CONTO'].";"
                        .$dati['PRODOTTOCC'].";"
                        .$dati['STATO']."\n";

          }
              
      }
      
      echo '</table>';        

      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);

echo '<br><center>Under 35 nr.'.$num_U35.'<br>
            <a href="tmp/usciti.csv" style="text-color:white;" target="_blank">Scarica il tracciato delle Ammissioni</a>
            </center>';



// Close ODBC
odbc_close($connect);


?>
