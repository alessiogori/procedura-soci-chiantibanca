<?php
//////////////////////////////////////////////////////////////////
// INVIA LE DATE NASCITA DEI DIPENDENTI PER MAIL BDAY
// Author: Alessio Fedi - 04.12.2023
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'SEGNALAZIONE MUTUA';
$TITOLO = 'Elenco Soci entrati e usciti per aggiornamento ChiantiMutua';

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

// Includo i dati di connessione
include("../config/_config.php");
include("../config/_functions.php");

echo '<html>
        <head>
        <title>'.$TITOLO.'</title>
        </head>

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Connessione a MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

$oggi = date("Ymd");
$adesso = date("d.m.Y");
$anno   = date("Y");

$currentDate = date("md"); 
$bday = (string) $currentDate; 

// Calcolo data di ieri
// *****************************************************************************
$date = new DateTime();             // empty for now or pass any date string as param
$date->modify('- 1 days');          // 1 giorno indietro da oggi
//$ieri = $date->format('Ymd');       // formato output AAAAMMDD
$ieri = $date->format('d/m/Y');       


// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------


// -------------------------------------
// MAIL - Parametri invio Mail
// -------------------------------------

      //No email sabato o domenica
      if(date('w')=="0" || date('w')=="6") return;
      
      //Parm email
      // ini_set('SMTP', 'smtp.crtnet'); 
      ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
      ini_set('smtp_port', 25); 
      ini_set('sendmail_from','soci@chiantibanca.it');
      
      $nome_mittente = 'Ufficio Soci';
      $mail_dest     = 'info@chiantimutua.it' ; 
      $mail_mittente = 'soci@chiantibanca.it' ; 
      $mail_oggetto  = "SOCI CHIANTIBANCA - Aggiornamento del giorno ".$ieri;
      

      $mail_corpo    = '<html>      
                              <body style="font-family:verdana;font-size:10pt;"> 
                              <br><b>Risultano AMMESSE le seguenti posizioni </b><br><br>
                                    <table border="1" celpadding="1" cellspacing="1"
                                          style="font-family:verdana;font-size:10pt;background-color:lightgreen;"
                                          width="70%">
                                          <tr style="border: 1px solid white; border-collapse: collapse;background-color: #DEF7DE;">
                                                <td>Filiale</td>
                                                <td>NAG</td>
                                                <td>Socio</td>
                                                <td>Data Nascita</td>
                                                <td>Data Entrata</td>
                                          </tr>
                          ';  
       
// -------------------------------------
// LETTURA MOVIMENTI 
// -------------------------------------

// SOCI ENTRATI

      $select_in =   "
                        SELECT
                        CAST(s1.FILIALE_CAPOFILA AS UNSIGNED) as FILIALE_CAPOFILA,
                        s1.NAG,
                        concat(s1.INTESTAZIONE_A, ' ', s1.INTESTAZIONE_B) as SOCIO_AMMESSO,
                        s1.DATA_NASCITA, s1.DATA_ENTRATA
                        FROM
                        sds_soci as s1
                        WHERE
                        str_to_date(s1.DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$ieri."','%d/%m/%Y')
                        group by s1.NAG
                        ORDER BY 3
                        "; 
      $result_in = mysqli_query($connection, $select_in);
      while ($dati_in = mysqli_fetch_array($result_in)) {

      $mail_corpo .=
            "<tr>
                  <td align='left'>".$dati_in['FILIALE_CAPOFILA']."</td>
                  <td align='left'>".$dati_in['NAG']."</td>
                  <td align='left'>".$dati_in['SOCIO_AMMESSO']."</td>
                  <td align='left'>".$dati_in['DATA_NASCITA']."</td>
                  <td align='left'>".$dati_in['DATA_ENTRATA']."</td>
            </tr>
                  ";

      }

      $mail_corpo   .= "</table>
                        <table><br>";

// SOCI USCITI
      $mail_corpo   .= '<br><b>Risultano USCITE le seguenti posizioni </b><br><br>
                                    <table border="1" celpadding="1" cellspacing="1"
                                          style="font-family:verdana;font-size:10pt;background-color:lightgreen;"
                                          width="100%">
                                          <tr style="border: 1px solid white; border-collapse: collapse;background-color: #FFEDF5;">
                                                <td>Filiale</td>
                                                <td>NAG</td>
                                                <td>Socio</td>
                                                <td>Data Nascita</td>
                                                <td>Data Uscita</td>
                                          </tr>
                          '; 
      $select_out =   "
                        SELECT
                        CAST(s1.FILIALE_CAPOFILA AS UNSIGNED) as FILIALE_CAPOFILA,
                        s1.NAG,
                        concat(s1.INTESTAZIONE_A, ' ', s1.INTESTAZIONE_B) as SOCIO_USCITO,
                        s1.DATA_NASCITA, s1.DATA_USCITA
                        FROM
                        sds_soci as s1
                        WHERE
                        str_to_date(s1.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('".$ieri."','%d/%m/%Y')
                        group by s1.NAG
                        ORDER BY 3
                        "; 

      $result_out = mysqli_query($connection, $select_out);
      while ($dati_out = mysqli_fetch_array($result_out)) {

      $mail_corpo .=
            "<tr>
                  <td align='left'>".$dati_out['FILIALE_CAPOFILA']."</td>
                  <td align='left'>".$dati_out['NAG']."</td>
                  <td align='left'>".$dati_out['SOCIO_USCITO']."</td>
                  <td align='left'>".$dati_out['DATA_NASCITA']."</td>
                  <td align='left'>".$dati_out['DATA_USCITA']."</td>
            </tr>
                  ";

      }

      $mail_corpo .= "</table><br><br>";


// ----------------------
// Close ODBC
// ----------------------
odbc_close($connect);


// ----------------------
// Chiusura ed invio Mail
// ----------------------
$mail_corpo .= "<br><small>Questa mail viene generata in maniera automatica. In caso di problemi, contattare Ufficio Soci.</small>";
$mail_corpo .= "</body></html>\r\n";

//echo $mail_corpo;

      $mail_headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
      $mail_headers .= "Reply-To: " . $mail_mittente . "\r\n";
      $mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
      $mail_headers .= "MIME-Version: 1.0\r\n";
      $mail_headers .= "Content-type: text/html; charset=UTF-8\r\n";
      $mail_headers .= "Content-Transfer-Encoding: base64";
      //$mail_headers .= "CC: ".$mail_cc."\r\n";
      
      //$mail_oggetto_encoded = '=?UTF-8?B?' . base64_encode($mail_oggetto) . '?=';
      $mail_corpo_encoded = base64_encode($mail_corpo);

      if (mail($mail_dest, $mail_oggetto, $mail_corpo_encoded, $mail_headers)) {
        echo "<center>Messaggio inviato a " . $mail_dest . "<br />\r\n";
      } else {
        echo "<center>Errore. Nessun messaggio inviato. <br />\r\n";
      }

//echo $mail_corpo;

?>
<script type="text/javascript">
self.close();
</script>

