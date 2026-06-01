<?php
//////////////////////////////////////////////////////////////////
// INVIA I MOVIMENTI GIORNALIERI DI C/C
// Author: Alessio Fedi - 27.04.2023
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'MOV_CC';
$TITOLO = 'Movimenti Conto Corrente';

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

$adesso = date("d.m.Y");
$anno   = date("Y");

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
      $mail_oggetto  = "Movimenti C/C 61.514442-611241-611240-914914 del giorno ".$ieri;
      $mail_corpo    = '<html>      
                              <body style="font-family:verdana;font-size:8.5pt;"> 
                                    <table border="1" celpadding="1" cellspacing="1">
                                          <tr style="border: 1px solid white; border-collapse: collapse;background-color: #EAF0F0;">
                                                <td>Conto</td>
                                                <td>Data Contabile</td>
                                                <td>Data Valuta</td>
                                                <td>Segno</td>
                                                <td>Importo</td>
                                                <td>Saldo</td>
                                                <td>Causale</td>
                                                <td>Descrizione</td>
                                          </tr>
                          ';  
       
// -------------------------------------
// LETTURA MOVIMENTI SADAS
// -------------------------------------

      // Conteggio records presenti su Sadas pre-importazione
      $select_cnt_Sadas_A =   "
            SELECT
                   CG_MOVIMENTI_CONTABILI.NUM_RAPP AS CONTO ,
                   CG_MOVIMENTI_CONTABILI.DATA_CONT AS DATA_CONT ,
                   CG_MOVIMENTI_CONTABILI.DATA_VAL AS DATA_VAL ,
                   CG_MOVIMENTI_CONTABILI.SEGNO AS SEGNO ,
                   CG_MOVIMENTI_CONTABILI.IMP_DIVISA_CON_SEGNO/100 AS IMPORTO ,
                   CG_MOVIMENTI_CONTABILI.SALDO_DIVISA/100 as SALDO,
                   CG_MOVIMENTI_CONTABILI.CAUSMOV AS CAUSMOV ,
                   CG_MOVIMENTI_CONTABILI.CAUSALF_1 +
                   CG_MOVIMENTI_CONTABILI.CAUSALF_2 +
                   CG_MOVIMENTI_CONTABILI.CAUSALF_2 +
                   CG_MOVIMENTI_CONTABILI.CAUSALF_2 AS CAUSDES, CG_MOVIMENTI_CONTABILI.NUM_OPERAZ
            FROM
                  CG_MOVIMENTI_CONTABILI  
            WHERE
                  CG_MOVIMENTI_CONTABILI.COD_RAPP = 2 
            AND
                  CG_MOVIMENTI_CONTABILI.FILIALE = 61 
            AND
                  CG_MOVIMENTI_CONTABILI.NUM_RAPP in (514442, 611241, 611240, 914914)
            AND 
                  CG_MOVIMENTI_CONTABILI.DATA_CONT = '".$ieri."'
            ORDER BY CG_MOVIMENTI_CONTABILI.NUM_RAPP, CG_MOVIMENTI_CONTABILI.NUM_OPERAZ
                  ";
      //echo $select_cnt_Sadas_A;
      $result_cnt_Sadas_A = odbc_exec($connect, $select_cnt_Sadas_A);
      while($dati_cnt_Sadas_A = odbc_fetch_object($result_cnt_Sadas_A)) {

      if ($dati_cnt_Sadas_A->SEGNO == 'A') 
            {$style = '<tr style="border: 1px solid white; border-collapse: collapse;background-color: #C6FAC6;">';}
      else 
            {$style = '<tr style="border: 1px solid white; border-collapse: collapse;background-color: #F7D6D6;">';}

      $mail_corpo .=
                                          $style."
                                                <td><b>".$dati_cnt_Sadas_A->CONTO."</b></td>
                                                <td>".$dati_cnt_Sadas_A->DATA_CONT."</td>
                                                <td>".$dati_cnt_Sadas_A->DATA_VAL."</td>
                                                <td align='center'>".$dati_cnt_Sadas_A->SEGNO."</td>
                                                <td align='right'><b>".$dati_cnt_Sadas_A->IMPORTO."</b></td>
                                                <td align='right'><b>".$dati_cnt_Sadas_A->SALDO."</b></td>
                                                <td>".$dati_cnt_Sadas_A->CAUSMOV."</td>
                                                <td>".$dati_cnt_Sadas_A->CAUSDES."</td>
                                          </tr>
                  ";

      }


// ----------------------
// Close ODBC
// ----------------------
odbc_close($connect);


// ----------------------
// Chiusura ed invio Mail
// ----------------------
$mail_corpo .= "<br><small>Questa mail viene generata in maniera automatica. In caso di problemi, contattare Alessio Fedi.</small>";
$mail_corpo .= "</body></html>\r\n";

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
