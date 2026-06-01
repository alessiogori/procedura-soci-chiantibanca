<?php
//////////////////////////////////////////////////////////////////
// INVIA LE DATE NASCITA DEI DIPENDENTI PER MAIL BDAY
// Author: Alessio Fedi - 04.12.2023
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'BDAY';
$TITOLO = 'Birthday Dipendenti';

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
      ini_set('sendmail_from','alessio.fedi@chiantibanca.it');
      
      $nome_mittente = 'Alessio';
      $mail_dest     = 'alessio.fedi@chiantibanca.it' ; 
      $mail_mittente = 'alessio.fedi@chiantibanca.it' ; 
      $mail_oggetto  = "BDAY Dipendenti del giorno ".$ieri;
      $mail_corpo    = '<html>      
                              <body style="font-family:verdana;font-size:10pt;"> 
                              <br><b>Oggi è il COMPLEANNO di </b><br><br>
                                    <table border="1" celpadding="1" cellspacing="1"
                                          style="font-family:verdana;font-size:10pt;background-color:lightgreen;"
                                          width="100%">
                                          <tr style="border: 1px solid white; border-collapse: collapse;background-color: #EAF0F0;">
                                                <td>NAG</td>
                                                <td>Matricola</td>
                                                <td>Dipendente</td>
                                                <td>Data Nascita</td>
                                                <td>Eta</td>
                                                <td>Mansione</td>
                                          </tr>
                          ';  
       
// -------------------------------------
// LETTURA MOVIMENTI SADAS
// -------------------------------------

      // -------------------
      // Il compleanno oggi
      // -------------------
      $select_cnt_Sadas_B =   "
            SELECT
                   TAB_UTENTI.NAG AS NAG ,
                   TAB_UTENTI.COD_USE AS COD_USE ,
                   TAB_UTENTI.COD_USE_NUMERICO AS COD_USE_NUMERICO ,
                   TAB_UTENTI.NOME_UTENTE AS NOME_UTENTE ,
                   SUBSTRING(ANAG_PERSONE_FISICHE.DATA_NASCITA,5,4) AS MMGG ,
                   ANAG_PERSONE_FISICHE.DATA_NASCITA AS DATA_NASCITA ,
                   TAB_UTENTI.DESCR_MANSIONE_WPROF AS MANSIONE 
            FROM
                  TAB_UTENTI INNER JOIN ANAG_PERSONE_FISICHE 
                  ON (TAB_UTENTI.NAG = ANAG_PERSONE_FISICHE.NAG ) 
            WHERE 
                   TAB_UTENTI.COD_USE_NUMERICO < 3000
            AND 
                   TAB_UTENTI.DESCR_MANSIONE_WPROF != ''
            AND 
                   SUBSTRING(ANAG_PERSONE_FISICHE.DATA_NASCITA,5,4) = 
                   '".$bday."'
            GROUP BY 
                   TAB_UTENTI.NAG ,
                   TAB_UTENTI.COD_USE ,
                   TAB_UTENTI.COD_USE_NUMERICO ,
                   TAB_UTENTI.NOME_UTENTE  ,
                   SUBSTRING(ANAG_PERSONE_FISICHE.DATA_NASCITA,5,4),
                   ANAG_PERSONE_FISICHE.DATA_NASCITA ,
                   TAB_UTENTI.DESCR_MANSIONE_WPROF 
            ORDER BY TAB_UTENTI.NOME_UTENTE

                  ";

      //echo $select_cnt_Sadas_B;
      $result_cnt_Sadas_B = odbc_exec($connect, $select_cnt_Sadas_B);
      while($dati_cnt_Sadas_B = odbc_fetch_object($result_cnt_Sadas_B)) {

      // Date in formato Ydm
      $date1_Ydm = $dati_cnt_Sadas_B->DATA_NASCITA; 
      $date2_Ydm = $oggi;

      // Converti le date in formato Ydm a Y-m-d
      $date1_Ymd = DateTime::createFromFormat('Ymd', $date1_Ydm)->format('Y-m-d');
      $date2_Ymd = DateTime::createFromFormat('Ymd', $date2_Ydm)->format('Y-m-d');

      // Calcola l'intervallo di tempo tra le date
      $datetime1 = new DateTime($date1_Ymd);
      $datetime2 = new DateTime($date2_Ymd);
      $interval = $datetime1->diff($datetime2);

      // Stampa l'intervallo
      $eta = $interval->format('%y');


      $mail_corpo .=
            "
                  <td align='left'>".$dati_cnt_Sadas_B->NAG."</td>
                  <td align='left'>".$dati_cnt_Sadas_B->COD_USE."</td>
                  <td align='left'>
                        <a href='mailto:".$dati_cnt_Sadas_B->NOME_UTENTE."'>
                        <b>".$dati_cnt_Sadas_B->NOME_UTENTE."</a></b></td>
                  <td align='left'>".$dati_cnt_Sadas_B->DATA_NASCITA."</td>
                  <td align='left'><b>".$eta."</b></td>
                  <td align='left'>".$dati_cnt_Sadas_B->MANSIONE."</td>
            </tr>
                  ";

      }

      $mail_corpo .= "</table><br><br>";

      // -------------------
      // Tutti gli utenti
      // -------------------

      $mail_corpo    .= '   <table border="1" celpadding="1" cellspacing="1"
                              style="font-family:verdana;font-size:10pt;"
                              width="100%">
                                    <tr style="border: 1px solid white; border-collapse: collapse;background-color: #EAF0F0;">
                                                <td>NAG</td>
                                                <td>Matricola</td>
                                                <td>Dipendente</td>
                                                <td>Data Nascita</td>
                                                <td>Eta</td>
                                                <td>Mansione</td>
                                          </tr>
                          ';  

      $select_cnt_Sadas_A =   "
            SELECT
                   TAB_UTENTI.NAG AS NAG ,
                   TAB_UTENTI.COD_USE AS COD_USE ,
                   TAB_UTENTI.COD_USE_NUMERICO AS COD_USE_NUMERICO ,
                   TAB_UTENTI.NOME_UTENTE AS NOME_UTENTE ,
                   ANAG_PERSONE_FISICHE.DATA_NASCITA AS DATA_NASCITA ,
                   TAB_UTENTI.DESCR_MANSIONE_WPROF AS MANSIONE 
            FROM
                  TAB_UTENTI INNER JOIN ANAG_PERSONE_FISICHE 
                  ON (TAB_UTENTI.NAG = ANAG_PERSONE_FISICHE.NAG ) 
            WHERE 
                   TAB_UTENTI.COD_USE_NUMERICO < 3000
            AND 
                   TAB_UTENTI.DESCR_MANSIONE_WPROF != ''
            GROUP BY 
                   TAB_UTENTI.NAG ,
                   TAB_UTENTI.COD_USE ,
                   TAB_UTENTI.COD_USE_NUMERICO ,
                   TAB_UTENTI.NOME_UTENTE  ,
                   ANAG_PERSONE_FISICHE.DATA_NASCITA ,
                   TAB_UTENTI.DESCR_MANSIONE_WPROF 
            ORDER BY TAB_UTENTI.NOME_UTENTE

                  ";

      //echo $select_cnt_Sadas_A;
      $result_cnt_Sadas_A = odbc_exec($connect, $select_cnt_Sadas_A);
      while($dati_cnt_Sadas_A = odbc_fetch_object($result_cnt_Sadas_A)) {

      // Date in formato Ydm
      $date1_Ydm = $dati_cnt_Sadas_A->DATA_NASCITA; 
      $date2_Ydm = $oggi;

      // Converti le date in formato Ydm a Y-m-d
      $date1_Ymd = DateTime::createFromFormat('Ymd', $date1_Ydm)->format('Y-m-d');
      $date2_Ymd = DateTime::createFromFormat('Ymd', $date2_Ydm)->format('Y-m-d');

      // Calcola l'intervallo di tempo tra le date
      $datetime1 = new DateTime($date1_Ymd);
      $datetime2 = new DateTime($date2_Ymd);
      $interval = $datetime1->diff($datetime2);

      // Stampa l'intervallo
      $eta = $interval->format('%y');


      $mail_corpo .=
            "
                  <td align='left'>".$dati_cnt_Sadas_A->NAG."</td>
                  <td align='left'>".$dati_cnt_Sadas_A->COD_USE."</td>
                  <td align='left'>
                        <a href='mailto:".$dati_cnt_Sadas_A->NOME_UTENTE."'>
                        <b>".$dati_cnt_Sadas_A->NOME_UTENTE."</a></b></td>
                  <td align='left'>".$dati_cnt_Sadas_A->DATA_NASCITA."</td>
                  <td align='left'><b>".$eta."</b></td>
                  <td align='left'>".$dati_cnt_Sadas_A->MANSIONE."</td>
            </tr>
                  ";

      }

      $mail_corpo .= "</table>";



// ----------------------
// Close ODBC
// ----------------------
odbc_close($connect);


// ----------------------
// Chiusura ed invio Mail
// ----------------------
$mail_corpo .= "<br><small>Questa mail viene generata in maniera automatica. In caso di problemi, contattare Alessio Fedi.</small>";
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

