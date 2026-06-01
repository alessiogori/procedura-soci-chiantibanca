<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Deceduti';

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
            $_GET['datain'] = '01/01/2026'; // <-- anno da aggiornare ad inizio anno
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


      // QUERY DI CONTEGGIO DECEDUTI TOTALI
      $select_qta =   "
                        SELECT count(*) as QTA, sum(ValoreTotaleAzioni) as importo
                        FROM view_decessi
                        WHERE
                        str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta = mysqli_query($connection, $select_qta);
      while ($dati_qta = mysqli_fetch_array($result_qta)) {
        $qta_soci_dec = $dati_qta['QTA'];
        $imp_soci_dec = $dati_qta['importo'];
        }

      // DECEDUTI CON INTESTAZIONE A EREDI GIA' ESEGUITE
      $select_qta1 =   "
                        SELECT count(*) as QTA, sum(ValoreTotaleAzioni) as importo
                        FROM view_decessi
                        LEFT JOIN sds_soci_domande ON view_decessi.IDSOCIO = sds_soci_domande.DEFUNTO_IDSOCIO
                        WHERE 
                        DATA_DELIBERA != 0
                        AND CTIPODOM != 'DL'
                        AND str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta1 = mysqli_query($connection, $select_qta1);
      while ($dati_qta1 = mysqli_fetch_array($result_qta1)) {
        $qta_soci_dec1 = $dati_qta1['QTA'];
        // $imp_soci_dec1 = $dati_qta1['importo'];
        }

      // DECEDUTI SENZA DOMANDE IN CORSO DA PARTE DEGLI EREDI
      $select_qta2 =   "
                        SELECT count(*) as QTA, sum(ValoreTotaleAzioni) as importo
                        FROM view_decessi
                        LEFT JOIN sds_soci_domande ON view_decessi.IDSOCIO = sds_soci_domande.DEFUNTO_IDSOCIO
                        WHERE 
                        DATA_DOMANDA is null
                        AND str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta2 = mysqli_query($connection, $select_qta2);
      while ($dati_qta2 = mysqli_fetch_array($result_qta2)) {
        $qta_soci_dec2 = $dati_qta2['QTA'];
        $imp_soci_dec2 = $dati_qta2['importo'];
        }


      // DECEDUTI CON DOMANDE DI INTESTAZIONE IN CORSO
      $select_qta3 =   "
                        SELECT count(*) as QTA, sum(ValoreTotaleAzioni) as importo
                        FROM view_decessi
                        LEFT JOIN sds_soci_domande ON view_decessi.IDSOCIO = sds_soci_domande.DEFUNTO_IDSOCIO
                        WHERE 
                        DATA_DELIBERA = 0
                        AND CTIPODOM != 'DL'
                        AND str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta3 = mysqli_query($connection, $select_qta3);
      while ($dati_qta3 = mysqli_fetch_array($result_qta3)) {
        $qta_soci_dec3 = $dati_qta3['QTA'];
        $imp_soci_dec3 = $dati_qta3['importo'];
        }


      // QUERY DI CONTEGGIO DECEDUTI TOTALI - CON RICHIESTE DI LIQUIDAZIONE AVANZATA
      $select_qta4 =   "
                        SELECT count(*) as QTA, sum(ValoreTotaleAzioni) as importo
                        FROM view_decessi
                        LEFT JOIN sds_soci_domande ON view_decessi.IDSOCIO = sds_soci_domande.DEFUNTO_IDSOCIO
                        WHERE 
                        CTIPODOM = 'DL'
                        AND str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        "; 

      $result_qta4 = mysqli_query($connection, $select_qta4);
      while ($dati_qta4 = mysqli_fetch_array($result_qta4)) {
        $qta_soci_dec4 = $dati_qta4['QTA'];
        $imp_soci_dec4 = $dati_qta4['importo'];
        }


echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO.'</h2>
            <p align="left">
            '.$rif.' '.$filiale.'<br>
            <b>SOCI DECEDUTI IN TOTALE DAL '.$_GET['datain'].' nr. '.$qta_soci_dec.'<br>

            <table border=0>
                <tr>
                    <td>Domande di intestazione a eredi già eseguite nr.</td><td align="right">
                    '.$qta_soci_dec1.'</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                    <td align="right"></td>
                </tr>
                <tr>
                    <td>Senza nessuna domande da eredi avanzata nr.</td><td align="right">
                    '.$qta_soci_dec2.'</td>
                    <td>&nbsp;&nbsp;&nbsp;&euro;</td>
                    <td align="right">'.number_format($imp_soci_dec2,0,',','.').'</td>
                </tr>
                <tr>
                    <td>Domande di intestazione a eredi in corso nr.</td><td align="right">
                    '.$qta_soci_dec3.'</td>
                    <td>&nbsp;&nbsp;&nbsp;&euro;</td>
                    <td align="right">'.number_format($imp_soci_dec3,0,',','.').'</td>
                </tr>
                <tr>
                    <td>Domande di Liquidazioni avanzate nr.</td><td align="right">
                    '.$qta_soci_dec4.'</td>
                    <td>&nbsp;&nbsp;&nbsp;&euro;</td>
                    <td align="right">'.number_format($imp_soci_dec4,0,',','.').'</td>
                </tr>
            </table>

            </p>
      </div>

';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> </center><br>';

      // QUERY DI DETTAGLIO DECEDUTI NON LIQUIDATI
      $select_dett =   "
                        SELECT 
                            view_decessi.IDSOCIO,
                            view_decessi.NAG,
                            Nominativo,
                            NumeroAzioni,
                            ValoreTotaleAzioni,
                            Data_Decesso,
                            Data_Uscita,
                            Filiale_Capofila,
                            Desc_Filiale,
                            Area,
                            CASE
                        WHEN CTIPODOM = 'DL' THEN
                            '(DL) Liquidazione'
                        WHEN CTIPODOM = 'DS' THEN
                            '(DS) Subentro già Socio'
                        WHEN CTIPODOM = 'DA' THEN
                            '(DA) Subentro nuovo Socio'
                        ELSE
                            ''
                        END AS TipoDomanda,
                         DATA_DOMANDA,
                         DATA_DELIBERA,
                         sds_soci_domande.NAG AS NAG_EREDE
                        FROM
                            view_decessi
                        LEFT JOIN sds_soci_domande ON view_decessi.IDSOCIO = sds_soci_domande.DEFUNTO_IDSOCIO
                        WHERE str_to_date(Data_Uscita,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        ".$condizionefiliale."
                        ORDER BY FILIALE_CAPOFILA, Nominativo
                        "; 

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0"  >
        <tr class="table-secondary">
          <td align="left"><small style="font-size:13px;">IDSocio</td>
          <td align="left"><small style="font-size:13px;">NAG</td>
          <td align="left"><small style="font-size:13px;">Intestazione</td>
          <td align="left"><small style="font-size:13px;">Filiale</td>
          <td align="left"><small style="font-size:13px;">Data Decesso</td>
          <td align="left"><small style="font-size:13px;">Data Uscita</td>
          <td align="left"><small style="font-size:13px;">Quote</td>
          <td align="left"><small style="font-size:13px;">Valore</td>
          <td align="left"><small style="font-size:13px;">Tipo Domanda</td>
          <td align="left"><small style="font-size:13px;">NAG Erede</td>
          <td align="left"><small style="font-size:13px;">Data Domanda</td>
          <td align="left"><small style="font-size:13px;">Data Delibera</td>
        </tr>';


      $result_dett = mysqli_query($connection, $select_dett);
      while ($dati_dett = mysqli_fetch_array($result_dett)) {

        $data_decesso_form =    substr($dati_dett['Data_Decesso'],6,2).'/'.
                                substr($dati_dett['Data_Decesso'],4,2).'/'.
                                substr($dati_dett['Data_Decesso'],0,4);

        if ($data_decesso_form == '  /  /    ') 
             {$data_decesso = '<span style="color:orange;">Manca Data Decesso in ANAGPF</span>';}
        else {$data_decesso = $data_decesso_form;}

        $linksocio = "<a class='text-red-light' href='sqldati_schedasocio.php?id=".$dati_dett['IDSOCIO']."'>".$dati_dett['Nominativo']."</a>";

        echo "<tr>
                <td><small style='font-size:13px;'>".$dati_dett['IDSOCIO']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['NAG']."</td>
                <td><small style='font-size:13px;'>".$linksocio."</td>
                <td><small style='font-size:13px;'>".$dati_dett['Filiale_Capofila']."</td>
                <td><small style='font-size:13px;'>".$data_decesso."</td>
                <td><small style='font-size:13px;'>".$dati_dett['Data_Uscita']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['NumeroAzioni']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['ValoreTotaleAzioni']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['TipoDomanda']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['NAG_EREDE']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['DATA_DOMANDA']."</td>
                <td><small style='font-size:13px;'>".$dati_dett['DATA_DELIBERA']."</td>
              </tr>
            ";


        }


      // Preparo l'esportazione su file
     // $myfilectrasf = fopen("tmp/ammissioni.csv", "w");
     // $contenutoOutput = "File aggiornato al ".$_GET['dataout']."\n";
     // $contenutoOutput .= "FILIALE;IDSOCIO;NAG;SOCIO_AMMESSO;DATA_ENTRATA;AZIONI;IMPORTO;PACK;IDSOCIO_DEFUNTO;NAG_DEFUNTO;SOCIO_DEFUNTO;DATA_DECESSO\n";

      echo '</table>';        

      // fwrite($myfilectrasf, $contenutoOutput);
      // fclose($myfilectrasf);



?>
