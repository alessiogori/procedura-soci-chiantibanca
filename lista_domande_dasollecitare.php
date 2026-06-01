<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$TITOLO = 'Domande a Socio (da sollecitare)';

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

if  (!isset($_GET['inviamail'])) {

// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------
$adesso = date("d.m.Y");

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
     $condizionefiliale = ' AND SDS_SOCI_DOMANDE_NOPDF.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = ' AND SDS_SOCI_DOMANDE_NOPDF.FILIALE_DOMANDA in ('.$_GET['filiale'].')';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }


      // QUERY DI CONTEGGIO
      $select_qta_tot =   "
                        SELECT count(*) as QTA
                        FROM
                            SDS_SOCI_DOMANDE_NOPDF 
                        WHERE
                            FILIALE_DOMANDA <> 990
                        ".$condizionefiliale."
                        "; 

        $result_qta_tot = mysqli_query($connection, $select_qta_tot);
        while($dati_qta_tot = mysqli_fetch_array($result_qta_tot))
        {    
            $dati_qta_totale = $dati_qta_tot['QTA'];
        }


      // QUERY DI CONTEGGIO DOMANDA PIU' VECCHIA
      $select_min =   "
                        SELECT min(DATA_DOMANDA) as MIN_DATA_DOMANDA
                        FROM
                            SDS_SOCI_DOMANDE_NOPDF 
                        WHERE
                            FILIALE_DOMANDA <> 990
                        ".$condizionefiliale."
                        "; 

        $result_min = mysqli_query($connection, $select_min);
        while($dati_min = mysqli_fetch_array($result_min))
        {    
             $min_data_domanda = $dati_min['MIN_DATA_DOMANDA'];
        }

      
echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO.'</h2>
            <p align="left">
            '.$rif.' '.$filiale.'<br>
                <b>Ancora senza archiviazione nr. '.$dati_qta_totale.'<br>
                <b>Domanda più vecchia presente: '.$min_data_domanda.'
            </p>
            Richiedere a Assistenza Software il log da FormaDoc dei contratti SOCICN02 prodotti
      </div>

';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> </center><br>';

      // QUERY DI RICERCA
      $select_query =   "
                        SELECT 
                        FILIALE_DOMANDA,
                        P.NAG,
                        XNOME AS NOMINATIVO,
                        DATA_DOMANDA,
                        LEFT(DATASTAMPA,16) AS DATA_STAMPA,
                        UTENTE AS UTENTE_STAMPA
                        FROM sds_soci_domande_nopdf AS P 
                            LEFT OUTER JOIN tmp_formadoc_log AS F
                        ON P.NAG = cast(F.NAG as unsigned)
                        WHERE 
                            FILIALE_DOMANDA <> 990
                        ".$condizionefiliale."
                        GROUP BY
                        FILIALE_DOMANDA,
                        P.NAG,
                        XNOME,
                        DATA_DOMANDA,
                        UTENTE

                        ORDER BY cast(filiale_domanda as unsigned),3
                        "; 

                        //echo $select_query;

      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0"  >
        <tr class="table-secondary">
          <td align="left"><small style="font-size:14px;">Filiale</td>
          <td align="left"><small style="font-size:14px;">NAG Domanda</td>
          <td align="left"><small style="font-size:14px;">Intestazione Domanda</td>
          <td align="left"><small style="font-size:14px;">Data Domanda</td>
          <td align="left"><small style="font-size:14px;">Data Stampa</td>
          <td align="left"><small style="font-size:14px;">Utente Stampa</td>
          <td align="left"><small style="font-size:14px;">Mail</td>
        </tr>';

        $result = mysqli_query($connection, $select_query);
        $id = 1;
        while($dati = mysqli_fetch_array($result))
        {  
            $utente = str_replace(".", " ", $dati['UTENTE_STAMPA']);
            $utente = strtoupper($utente);

            if ($dati['UTENTE_STAMPA'] == "") 
                {
                    $mail_utente = '';
                }
            else 
                {
                    $mail_utente = $dati['UTENTE_STAMPA']."@chiantibanca.it";
                }

        $data_domanda_formattata = substr($dati['DATA_DOMANDA'],6,4).'-'.
                                    substr($dati['DATA_DOMANDA'],3,2).'-'.
                                    substr($dati['DATA_DOMANDA'],0,2);
        $data_adesso = date("Y-m-d");

        if (diff_date_ingiorni($data_domanda_formattata, $data_adesso) > 90 ) 
            {$coloreTimeGG = 'color:white; background-color: coral;' ; 
             $blink = '<img src="img/allarme.gif" height="20">';}
        else {$coloreTimeGG = '' ; 
             $blink = '';}

           echo "<tr>
                    <td align='left'><small style='font-size:14px;'>".$dati['FILIALE_DOMANDA']."</td>
                    <td align='left'><small style='font-size:14px;'>".$dati['NAG']."</td>
                    <td align='left'><small style='font-size:14px;'>".$dati['NOMINATIVO']."</td>
                    <td align='left'><small style='font-size:14px;".$coloreTimeGG."'>".$dati['DATA_DOMANDA']."</td>
                    <td align='left'><small style='font-size:14px;'>".$dati['DATA_STAMPA']."</td>
                    <td align='left'><small style='font-size:14px;'>".$utente."</td>
                    <td align='left'><small style='font-size:14px;'>".$blink."<a href='lista_domande_dasollecitare.php?id=".$id."&mail_utente=".$mail_utente."&nag=".$dati['NAG']."&nominativo=".$dati['NOMINATIVO']."&data_domanda=".$dati['DATA_DOMANDA']."&data_stampa=".$dati['DATA_STAMPA']."&inviamail=si' target='_blank'>".$mail_utente."</td>
                </tr>";

            $id++;
        }

  
      echo '</table>';        
//lisa.pieri

    }
elseif 
    ($_GET['inviamail'] == "si")
    {
        global $debug;
        //No email sabato o domenica
        if(date('w')=="0" || date('w')=="6") return;
        
        //Parm email
        //ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
        ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
        ini_set('smtp_port', 25); 
        ini_set('sendmail_from','noreply@chiantibanca.it');
        
        //Parm others
        $mail_cc = "";  
        $mail_dest = $_GET['mail_utente'].",soci@chiantibanca.it"; 
        $nome_mittente = "Ufficio Soci"; 
        $mail_mittente = 'soci@chiantibanca.it' ;
        
        $mail_oggetto = "Soci - DOMANDA PRESENTE SENZA PDF IN ISIDOC - ".$_GET['nag']." - ".$_GET['nominativo'];
        
        $mail_corpo = "<html><body><br>";
        $mail_corpo .= "<style type='text/css'>  body{font-family:'Courier New',Courier,monospace;font-size:11pt;} </style>";
            
        $mail_corpo .= "Ti prego di controllare la presenza di questa domanda, che ancora risulta non presente in PDF nel documentale ISIDOC:<br> <b style='color:brown;'>".$_GET['nag']." - ".$_GET['nominativo']."</b> <br><br>
            Se deve essere eliminata, puoi procedere in autonomia da <br>
            <i>Sistemi Guida > Soci > Domande Soci</i><br>
            selezionado la riga interessata e poi premendo il cestino.";

        $mail_corpo .= "<hr><br>";

        $mail_corpo .= "Messaggio automatico da Portale Soci<br>";
        //$mail_corpo .= $_POST['dipendente']."<br>";

        $mail_corpo .= "</body></html>\r\n";
        
        $mail_headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
        $mail_headers .= "Reply-To: " . $mail_mittente . "\r\n";
        $mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $mail_headers .= "MIME-Version: 1.0\r\n";
        $mail_headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $mail_headers .= "Content-Transfer-Encoding: base64";
        
        if(!empty($mail_cc)) $mail_headers .= "CC: ".$mail_cc."\r\n";
        
        $mail_oggetto_encoded = '=?UTF-8?B?' . base64_encode($mail_oggetto) . '?=';
        $mail_corpo_encoded = base64_encode($mail_corpo);

        if (mail($mail_dest, $mail_oggetto_encoded, $mail_corpo_encoded, $mail_headers)) {
          if($debug) echo "Messaggio inviato a " . $mail_dest . "<br /> Puoi chiudere questa finestra.\r\n";
        } else {
          if($debug) echo "Errore. Nessun messaggio inviato. <br> \r\n";
        }

    }

?>
