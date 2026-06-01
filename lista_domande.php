<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Domande a Socio (da esaminare)';

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

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


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

      // QUERY DI CONTEGGIO DECEDUTI
      $select_qta =   "
                        SELECT count(*) as QTA
                        FROM
                        sds_soci 
                        JOIN sds_soci_movinout 
                        ON sds_soci.IDSOCIO = sds_soci_movinout.IDSOCIO
                        WHERE
                        sds_soci.CTIPMOVUSCITA = 'MO'
                        AND CTIPOMOV = 'ID'
                        AND str_to_date(sds_soci_movinout.DATA_MOVIMENTO,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')

                        "; 

      $result_qta = mysqli_query($connection, $select_qta);
      while ($dati_qta = mysqli_fetch_array($result_qta)) {
        $qta_soci_dec = $dati_qta['QTA'];
        }



      // QUERY DI CONTEGGIO
      $select_qta =   "
                        SELECT count(*) as QTA, I.PRESENZA_DOCUMENTO as PDF
                        FROM
                            SOCI_DOMANDE as D LEFT OUTER JOIN ISIDOC_CONTRATTI as I ON (D.NAG = I.NAG)  
                        WHERE
                            D.CTIPOESITO =  'DE'  
                        AND
                            I.COD_CONTRATTO =  'SOCICN02' 
                        ".$condizionefiliale2."
                        GROUP BY
                            I.PRESENZA_DOCUMENTO         "; 

      $result_qta = odbc_exec($connect, $select_qta);
      while ($dati_qta = odbc_fetch_object($result_qta)) {
            
            if ($dati_qta->PDF == 'S') { $qta_soci_si_pdf = $dati_qta->QTA;}
        }

      $select_qta_tot =   "
                        SELECT count(*) as QTA
                        FROM
                            SOCI_DOMANDE as D LEFT OUTER JOIN ISIDOC_CONTRATTI as I ON (D.NAG = I.NAG)  
                        WHERE
                            D.CTIPOESITO =  'DE'  
                        AND
                            I.COD_CONTRATTO =  'SOCICN02' 
                        ".$condizionefiliale2."
                        "; 

      $result_qta_tot = odbc_exec($connect, $select_qta_tot);
      while ($dati_qta_tot = odbc_fetch_object($result_qta_tot)) {
            
            $dati_qta_totale = $dati_qta_tot->QTA;
        }

      $qta_soci_no_pdf = $dati_qta_totale - $qta_soci_si_pdf ;


      // QUERY DI CONTEGGIO DOMANDA PIU' VECCHIA
      $select_min =   "
                        SELECT min(DATA_DOMANDA) as MIN_DATA_DOMANDA
                        FROM
                            SOCI_DOMANDE as D INNER JOIN ISIDOC_CONTRATTI as I ON (D.NAG = I.NAG)  
                        WHERE
                            D.CTIPOESITO =  'DE'  
                        AND
                            I.COD_CONTRATTO =  'SOCICN02' 
                        AND 
                            I.PRESENZA_DOCUMENTO = 'S'
                        ".$condizionefiliale2."
                        "; 


      $result_min = odbc_exec($connect, $select_min);
      while ($dati_min = odbc_fetch_object($result_min)) {
            
             $min_data_domanda = $dati_min->MIN_DATA_DOMANDA;
        }

echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO.'</h2>
            <p align="left">
            '.$rif.' '.$filiale.'<br>
                <b>con PDF presente nr. '.$qta_soci_si_pdf.' / ancora senza archiviazione nr. '.$qta_soci_no_pdf.'<br>
                <b>SOCI DECEDUTI DAL '.$_GET['datain'].' nr. '.$qta_soci_dec.'<br>
                <b>Domanda più vecchia presente: '.$min_data_domanda.'
            </p>
      </div>

';



      // Griglia delle domande presenti per Anno/Mese
      $select_giacenza =   "
                SELECT 
                concat(SUBSTR(DATA_DOMANDA,7,4),SUBSTR(DATA_DOMANDA,4,2)) as AnnoMese, count(*) as QTA
                FROM sds_soci_domande
                where DATA_DELIBERA = '0'
                group by concat(SUBSTR(DATA_DOMANDA,7,4),SUBSTR(DATA_DOMANDA,4,2))
                order by concat(SUBSTR(DATA_DOMANDA,7,4),SUBSTR(DATA_DOMANDA,4,2))
                        "; 
      echo '<select>';
      $result_giacenza = mysqli_query($connection, $select_giacenza);
      while ($dati_giacenza = mysqli_fetch_array($result_giacenza)) {
         echo '<option>'.$dati_giacenza['AnnoMese']. ' - '.$dati_giacenza['QTA'].'</option>';
        }
      echo '    </select>';


echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> </center><br>';

      // QUERY DI RICERCA
      $select_query =   "
                        SELECT
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO AS ISIDOC ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE as NOTE,
                             SOCI_DOMANDE_01.NAG AS NAG_DOMANDA ,
                             SOCI_DOMANDE_01.XNOME AS INTESTAZIONE_DOMANDA ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
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
                             SOCI_DOMANDE_01.NUM_RAPP,
                             ISIDOC_CONTRATTI_01.DATA_ACQUISIZIONE
                        FROM
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ISIDOC_CONTRATTI AS ISIDOC_CONTRATTI_01  ON (SOCI_DOMANDE_01.NAG = ISIDOC_CONTRATTI_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_DOMANDE_01.NAG_RIC = ANAG_NAG_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN SOCI_ANAGRAFICA AS SOCI_ANAGRAFICA_01  ON (SOCI_DOMANDE_01.IDSOCIO_SUB = SOCI_ANAGRAFICA_01.IDSOCIO ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ANAG_NAG AS ANAG_NAG_03  ON (SOCI_DOMANDE_01.NAG = ANAG_NAG_03.NAG ) ,
                            SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_01 INNER JOIN ANAG_NAG AS ANAG_NAG_02  ON (SOCI_ANAGRAFICA_01.NAG = ANAG_NAG_02.NAG )  
                        WHERE
                            SOCI_DOMANDE_01.CTIPOESITO =  'DE'  
                        AND
                            ISIDOC_CONTRATTI_01.COD_CONTRATTO =  'SOCICN02' 
                        ".$condizionefiliale."
                        GROUP BY
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO  ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE ,
                             SOCI_DOMANDE_01.NAG  ,
                             SOCI_DOMANDE_01.XNOME ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
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
                             SOCI_DOMANDE_01.NUM_RAPP,
                             ISIDOC_CONTRATTI_01.DATA_ACQUISIZIONE
                        ORDER BY 1 desc, 8, 4
                        "; 

                        //echo $select_query;

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0"  >
        <tr class="table-warning">
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Motivaz</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">PDF</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Note</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">NAG Domanda</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Intestazione Domanda</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Data Domanda</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Filiale</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Soglia</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Tipo</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Azioni</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Fondo</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Motivo Recesso</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Tipo Trasf</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">IDSocio Trasferente</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">IDSocio Ricevente</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">NAG Ricevente</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Int.Ricevente</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">IDSocio Subentro</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">NAG Subentro</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Int.Subentro</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Prof</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Res.Zona</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">TipoNAG</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Piazza</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Residenza</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Professione Presso</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Immobili Presso</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Fil</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Conto</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">U35</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;" title="Segnala con quante quote resta il cedente con questa domanda">Rimanenza</td>
          <td style="position: sticky;top: 0" align="left"><small style="font-size:11px;">Data Acquis</td>

        </tr>';


      // Preparo l'esportazione su file
     // $myfilectrasf = fopen("tmp/ammissioni.csv", "w");
     // $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
     // $contenutoOutput .= "FILIALE;IDSOCIO;NAG;SOCIO_AMMESSO;DATA_ENTRATA;AZIONI;IMPORTO;PACK;IDSOCIO_DEFUNTO;NAG_DEFUNTO;SOCIO_DEFUNTO;DATA_DECESSO\n";

      $result = odbc_exec($connect, $select_query);
      while ($dati = odbc_fetch_object($result)) {


          // QUERY RICERCA MOTIVAZIONI INSERITE
          $select_motiv =   "
                            SELECT nag, data_domanda, count(*) as qta
                            FROM
                            tab_motivazioni 
                            WHERE
                            nag = ".$dati->NAG_DOMANDA."
                            AND 
                            data_domanda = '".$dati->DATA_DOMANDA."'
                            "; 

          $result_motiv = mysqli_query($connection, $select_motiv);

          while ($dati_motiv = mysqli_fetch_array($result_motiv)) {
            $motiv_nag          = $dati_motiv['nag'];
            $motiv_datadomanda  = $dati_motiv['data_domanda'];

            if ( ($motiv_nag == $dati->NAG_DOMANDA) && ($motiv_datadomanda == $dati->DATA_DOMANDA) )
            {
                // VISUALIZZA MOTIVAZIONE
                $ico_motivazione = '
                <a href="motivazioni.php?start=IN&filiale='.$dati->FILIALE_DOMANDA.'&nag='.$motiv_nag.'&data_domanda='.$motiv_datadomanda.'&nome='.$dati->INTESTAZIONE_DOMANDA.'" target="_blank">
                <i class="fas fa-sticky-note" style="color:lightgreen;" title="Motivazione ammissione PRESENTE"></i>
                </a>
                ';
            }

            else
            {
                // FORM DI INSERIMENTO MOTIVAZIONE
                $ico_motivazione = '
                <a href="motivazioni_form.php?action=&start=IN&filiale='.$dati->FILIALE_DOMANDA.'&nag='.$dati->NAG_DOMANDA.'&data_domanda='.$dati->DATA_DOMANDA.'&nome='.$dati->INTESTAZIONE_DOMANDA.'" target="_blank">
                <i class="fas fa-sticky-note" style="color:#FACB7E;" title="Motivazione ammissione DA INSERIRE"></i>
                </a>
                ';
            }
            }

        $data_domanda_formattata = substr($dati->DATA_DOMANDA,6,4).'-'.
                                    substr($dati->DATA_DOMANDA,3,2).'-'.
                                    substr($dati->DATA_DOMANDA,0,2);
        $data_adesso = date("Y-m-d");

        if (diff_date_ingiorni($data_domanda_formattata, $data_adesso) > 90 ) 
            {$coloreTimeGG = 'color:white; background-color: coral;' ; }
        else {$coloreTimeGG = '' ; }


            // Verifica se il richiedente è Under 35
            $select_u35 =   "
                            SELECT
                                DATA_NASCITA                             
                            FROM
                                ANAG_PERSONE_FISICHE
                            WHERE
                                NAG =  ".$dati->NAG_DOMANDA."
                            UNION
                            SELECT
                                '' AS DATA_NASCITA                             
                            FROM
                                ANAG_PERSONE_GIURIDICHE
                            WHERE
                                NAG =  ".$dati->NAG_DOMANDA;
                            


            $result_u35 = odbc_exec($connect, $select_u35);
            while ($dati_u35 = odbc_fetch_object($result_u35)) {

            if ($dati_u35->DATA_NASCITA > $DataLimiteU30)
            {$u35 = 'U35'; 
             $coloreU35 = 'color:white; background-color: green;' ;}
            else
            {$u35 = '--'; 
             $coloreU35 = '';}

            $data_nascita_formattata = substr($dati_u35->DATA_NASCITA,6,2).'-'.
                                        substr($dati_u35->DATA_NASCITA,4,2).'-'.
                                        substr($dati_u35->DATA_NASCITA,0,4); 


                echo "<tr>
                        <td align='center'><small style='font-size:11px;'>".$ico_motivazione."</td>
                        <td><small style='font-size:11px;'>".$dati->ISIDOC."</td>
                        <td><small style='font-size:11px;'>".$dati->NOTE."</td>
                        <td><small style='font-size:11px;'>".$dati->NAG_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati->INTESTAZIONE_DOMANDA."</td>
                        <td><small style='font-size:11px;".$coloreTimeGG."'>".$dati->DATA_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati->FILIALE_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati->SOGLIA."</td>
                        <td><small style='font-size:11px;'>".$dati->CTIPODOM."</td>
                        <td><small style='font-size:11px;'>".$dati->NAZIONI."</td>
                        <td><small style='font-size:11px;'>".$dati->FONDO_RIACQUISTO."</td>
                        <td><small style='font-size:11px;'>".$dati->MOTIVO_RECESSO."</td>
                        <td><small style='font-size:11px;'>".$dati->TIPO_TRASF."</td>
                        <td><small style='font-size:11px;'>".$dati->IDSOCIO_TRASFERENTE."</td>
                        <td><small style='font-size:11px;'>".$dati->IDSOCIO_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$dati->NAG_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$dati->INTESTAZIONE_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$dati->IDSOCIO_SUBENTRO."</td>
                        <td><small style='font-size:11px;'>".$dati->NAG_SUBENTRO."</td>
                        <td><small style='font-size:11px;'>".$dati->INTESTAZIONE_SUBENTRO."</td>
                        <td><small style='font-size:11px;'>".$dati->PROFESSIONE."</td>
                        <td><small style='font-size:11px;'>".$dati->FLAG_RES_ZONA_BANCA."</td>
                        <td><small style='font-size:11px;'>".$dati->TIPO_NAG."</td>
                        <td><small style='font-size:11px;'>".$dati->PIAZZA."</td>
                        <td><small style='font-size:11px;'>".$dati->COMUNE_RESIDENZA." ".$dati->PROVINCIA_RESIDENZA."</td>
                        <td><small style='font-size:11px;'>".$dati->PROFESSIONE_NEL_COMUNE." ".$dati->PROFESSIONE_NEL_COMUNE_PRESSO."</td>
                        <td><small style='font-size:11px;'>".$dati->IMMOBILI_NEL_COMUNE." ".$dati->IMMOBILI_NEL_COMUNE_PRESSO."</td>
                        <td><small style='font-size:11px;'>".$dati->FILIALE_RAPP."</td>
                        <td><small style='font-size:11px;'>".$dati->NUM_RAPP."</td>
                        <td style='font-size:11px;".$coloreU35."'
                            title='".$data_nascita_formattata."'>".$u35."</td>
                            ";


            // Controllo il numero di quote possedute dall'eventuale TRASFERENTE
            if (($dati->IDSOCIO_RICEVENTE <> 0) && ($dati->CTIPODOM == 'DA') ) {

            $select_trasferente =   "
                            SELECT 
                                    IDSOCIO, NUMERO_AZIONI, VALORE_AZIONI
                            FROM
                                    sds_soci_certificati as c, sds_soci_domande as d
                            WHERE
                                    TRASFERIMENTO_DA_IDSOCIO = ".$dati->IDSOCIO_RICEVENTE."
                            AND 
                                    IDSOCIO = TRASFERIMENTO_DA_IDSOCIO";


            $qry_trasferente = $dbhandle->query($select_trasferente) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
            while($dati_trasferente = mysqli_fetch_array($qry_trasferente)){ 

            $Differenza_Azioni = $dati_trasferente['NUMERO_AZIONI'] - $dati->NAZIONI;

            if ($Differenza_Azioni < 33)
            {$titolo_trasferente = 'CHI CEDE RESTEREBBE CON MENO DI 33 AZIONI !!'; 
             $coloreTrasferente = 'color:black; background-color: yellow;' ;}
            else
            {$titolo_trasferente = ''; 
             $coloreTrasferente = '';}

            }
            }
            else 
            {$Differenza_Azioni = '';
             $titolo_trasferente = ''; 
             $coloreTrasferente = '';}


            echo "      <td style='font-size:11px;".$coloreTrasferente."'
                            title='".$titolo_trasferente."'>".$Differenza_Azioni."</td>
                        <td><small style='font-size:11px;'>".$dati->DATA_ACQUISIZIONE."</td>
                            
                      </tr>
                    ";
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
            
             
            // closing database connection      
            //$dbhandle->close();         

        }

    }
      
      echo '</table>';        

      // fwrite($myfilectrasf, $contenutoOutput);
      // fclose($myfilectrasf);


/*
echo '<a name="u30"></a><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella Under35 per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTableU30\') );"> &nbsp;&nbsp; <a href="#lista" style="text-decoration:none;">&uArr;</a></center><br>';


      // QUERY PER UNDER 35
      $select_u30 =   "
                        SELECT
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO AS ISIDOC ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE as NOTE,
                             SOCI_DOMANDE_01.NAG AS NAG_DOMANDA ,
                             SOCI_DOMANDE_01.XNOME AS INTESTAZIONE_DOMANDA ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
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
                             (ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B) AS INTESTAZIONE_RICEVENTE,
                             ANAG_PF.DATA_NASCITA                             
                        FROM
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ISIDOC_CONTRATTI AS ISIDOC_CONTRATTI_01  ON (SOCI_DOMANDE_01.NAG = ISIDOC_CONTRATTI_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 LEFT OUTER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_DOMANDE_01.NAG_RIC = ANAG_NAG_01.NAG ) ,
                            SOCI_DOMANDE  AS SOCI_DOMANDE_01 RIGHT OUTER JOIN ANAG_PERSONE_FISICHE AS ANAG_PF  ON (SOCI_DOMANDE_01.NAG = ANAG_PF.NAG ) 
                        WHERE
                            SOCI_DOMANDE_01.CTIPOESITO =  'DE'  
                        AND
                            ISIDOC_CONTRATTI_01.COD_CONTRATTO =  'SOCICN02' 
                        AND 
                            ANAG_PF.DATA_NASCITA >=  '".$DataLimiteU30."' 
                        ".$condizionefiliale."

                        GROUP BY
                             ISIDOC_CONTRATTI_01.PRESENZA_DOCUMENTO ,
                             ISIDOC_CONTRATTI_01.PRESENZA_NOTE ,
                             SOCI_DOMANDE_01.NAG  ,
                             SOCI_DOMANDE_01.XNOME  ,
                             SOCI_DOMANDE_01.DATA_DOMANDA  ,
                             SOCI_DOMANDE_01.FILIALE_DOMANDA ,
                             SOCI_DOMANDE_01.SOGLIA  ,
                             SOCI_DOMANDE_01.CTIPODOM  ,
                             SOCI_DOMANDE_01.NAZIONI  ,
                             SOCI_DOMANDE_01.RIACQUISTO_AZ ,
                             SOCI_DOMANDE_01.MOTIVO_RECESSO  ,
                             SOCI_DOMANDE_01.TIPO_TRASF  ,
                             SOCI_DOMANDE_01.IDSOCIO_DOM  ,
                             SOCI_DOMANDE_01.IDSOCIO_RIC  ,
                             SOCI_DOMANDE_01.NAG_RIC ,
                            (ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B) ,
                             ANAG_PF.DATA_NASCITA   

                        ORDER BY 1 desc, 8, 4

                        "; 

      echo ' <table class="table table-bordered table-hover" id="dataTableU30" width="90%" cellspacing="0"  >
        <tr class="table-secondary">
          <td align="left" colspan="17">ELENCO UNDER 35 IN ESAME</td>
        </tr>
        <tr class="table-secondary">
          <td align="left"><small style="font-size:11px;">PDF</td>
          <td align="left"><small style="font-size:11px;">Note</td>
          <td align="left"><small style="font-size:11px;">NAG Domanda</td>
          <td align="left"><small style="font-size:11px;">Intestazione Domanda</td>
          <td align="left"><small style="font-size:11px;">Data Domanda</td>
          <td align="left"><small style="font-size:11px;">Filiale</td>
          <td align="left"><small style="font-size:11px;">Soglia</td>
          <td align="left"><small style="font-size:11px;">Tipo</td>
          <td align="left"><small style="font-size:11px;">Azioni</td>
          <td align="left"><small style="font-size:11px;">Fondo</td>
          <td align="left"><small style="font-size:11px;">Motivo Recesso</td>
          <td align="left"><small style="font-size:11px;">Tipo Trasf</td>
          <td align="left"><small style="font-size:11px;">IDSocio Trasferente</td>
          <td align="left"><small style="font-size:11px;">IDSocio Ricevente</td>
          <td align="left"><small style="font-size:11px;">NAG Ricevente</td>
          <td align="left"><small style="font-size:11px;">Int.Ricevente</td>
          <td align="left"><small style="font-size:11px;">Data Nascita</td>
        </tr>';

      $result_u30 = odbc_exec($connect, $select_u30);
      while ($dati_u30 = odbc_fetch_object($result_u30)) {

        $data_domanda_formattata = substr($dati_u30->DATA_DOMANDA,6,4).'-'.
                                    substr($dati_u30->DATA_DOMANDA,3,2).'-'.
                                    substr($dati_u30->DATA_DOMANDA,0,2);
        $data_adesso = date("Y-m-d");

        if (diff_date_ingiorni($data_domanda_formattata, $data_adesso) > 90 ) 
            {$coloreTimeGG = 'color:white; background-color: coral;' ; }
        else {$coloreTimeGG = '' ; }

        $data_nascita_formattata = substr($dati_u30->DATA_NASCITA,6,2).'-'.
                                    substr($dati_u30->DATA_NASCITA,4,2).'-'.
                                    substr($dati_u30->DATA_NASCITA,0,4);        
                echo "<tr>
                        <td><small style='font-size:11px;'>".$dati_u30->ISIDOC."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->NOTE."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->NAG_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->INTESTAZIONE_DOMANDA."</td>
                        <td><small style='font-size:11px;".$coloreTimeGG."'>".$dati_u30->DATA_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->FILIALE_DOMANDA."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->SOGLIA."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->CTIPODOM."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->NAZIONI."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->FONDO_RIACQUISTO."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->MOTIVO_RECESSO."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->TIPO_TRASF."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->IDSOCIO_TRASFERENTE."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->IDSOCIO_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->NAG_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$dati_u30->INTESTAZIONE_RICEVENTE."</td>
                        <td><small style='font-size:11px;'>".$data_nascita_formattata."</td>
                      </tr>
                    ";
          
    }
      
      echo '</table>';        
*/

// Close ODBC
odbc_close($connect);


?>
