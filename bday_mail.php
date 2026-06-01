<?php

// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
/*
    $value = $_GET['filiale'];
    $exploded_value = explode('|', $value);
    $filiale_nome = $exploded_value[0];
    $filiale_mail = $exploded_value[1];
    $cag = $_GET['cag'];
    $nominativo = $_GET['nominativo'];
*/

	//No email sabato o domenica
	if(date('w')=="0" || date('w')=="6") return;
	
	//Parm email
	// ini_set('SMTP', 'smtp.crtnet'); 
	ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
	ini_set('smtp_port', 25); 
	ini_set('sendmail_from','soci@chiantibanca.it');
	
	$adesso = date('d/m');
	
	//Parm others
	//$mail_cc = $filiale_mail ; 
	//$mail_cc = "soci@chiantibanca.it"; 
	$nome_mittente = 'Ufficio Soci';
	$mail_mittente = 'soci@chiantibanca.it' ; 

    // Raggruppamento delle Filiali ed ottengo la mail
    $select2 = "	SELECT lpad(codFil,3,0) as codFil, email_estesa as email, desc_filiale
        			FROM tab_soci_as37 as s, tab_psw as p
        			WHERE s.cag not in (select v.cag from view_richiesteincorso as v)
        			AND dataNasc like '".$adesso."%'
        			AND tipoContropVAL = 11000
        			AND statoVAL not in ('E','S','N')
        			AND lpad(codFil,3,0) = filiale
        			GROUP BY codFil
        			ORDER BY codFil
        			"; 
    $querydati2 = mysqli_query($connection, $select2);
    while($dati2=mysqli_fetch_array($querydati2)){ 
        //$mail_dest    = "alessio.fedi@gmail.com"; // TEST ESTERNO
        $mail_dest    = "alessiofedi@chiantibanca.it"; // TEST INTERNO
        //$mail_dest = $dati2['email']; // sostituire con FILIALI
        $filiale_dest = intval($dati2['codFil']);
        $filiale_desc = $dati2['desc_filiale'];
        
	$mail_oggetto = "Oggi e' il compleanno dei seguenti Soci (Banca) della Filiale di ".$filiale_desc;
 
    $mail_corpo = ' <html><body style="font-family:verdana;font-size:8.5pt;"> 
	                <table border="0" align="center" width="98%">
                    <tr style="background-color:green;">
                      <td colspan="6" align="left" style="color:white;"></td>
                    </tr>
                    <tr style="background-color:#EEEEEE;">
                      <td align="left" style="color:black;"><span style="font-size:10pt;">CAG</span></td>
                      <td align="left" style="color:black;"><span style="font-size:10pt;">SOCIO</span></td>
                      <td align="left" style="color:black;"><span style="font-size:10pt;">ETA\'</span></td>
                      <td align="left" style="color:black;"><span style="font-size:10pt;">FILIALE</span></td>
                      <td align="left" style="color:black;"><span style="font-size:10pt;">TELEFONO</span></td>
                      <td align="left" style="color:black;"><span style="font-size:10pt;">EMAIL</span></td>
                    </tr>
                    <tr style="background-color:green;">
                      <td colspan="6" align="left" style="color:white;"></td>
                    </tr>
                    ';        

    // ESTRAGGO IL DETTAGLIO DEI SOCI BANCA
    $select = "	SELECT * 
    			FROM tab_soci_as37 as s
    			WHERE s.cag not in (select v.cag from view_richiesteincorso as v)
    			AND s.dataNasc like '".$adesso."%'
    			AND s.tipoContropVAL = 11000
    			AND s.statoVAL not in ('E','S','N')
    			AND s.codFil = '".$filiale_dest."'
    			ORDER BY s.int1Socio, s.int2Socio
    			";
    
    $querydati = mysqli_query($connection, $select);
    while($dati=mysqli_fetch_array($querydati)){ 

        $eta = ( date("Y") - substr($dati['dataNasc'],-4) );
        
        $mail_corpo .= 
                "	<tr>
        			<td align='left'><span style='font-size:10pt;'>".$dati['cag']."</span></td>
        			<td align='left'><span style='font-size:10pt;'><a class='text-success' href='http://10.119.192.46:8080/soci/sqldati_schedasocio.php?id=".$dati['prot']."'>".$dati['int1Socio']." ".$dati['int2Socio']."</a></span></td>
        			<td align='center'><span style='font-size:10pt;'>".$eta."</span></td>
        			<td align='center'><span style='font-size:10pt;'>".$dati['codFil']."</span></td>
        			<td align='left'><span style='font-size:10pt;'>".$dati['telefono']."</span></td>
        			<td align='left'><span style='font-size:10pt;'><a href='mailto:".$dati['indirizzoEMail']."?subject=Buon compleanno...'>
        			    ".$dati['indirizzoEMail']."</span></td>
        		</tr>";
        
        }

        	$mail_corpo .= ' 
        	        <tr><td colspan="6"></td></tr>
                    <tr style="background-color:green;">
                      <td align="left"><a class="nav-link" style="color:white;font-size:8pt;text-decoration:none;" href="http://10.119.192.46:8080/soci/index.php">PORTALE SOCI</a></td>
                      <td colspan="5" align="right" style="color:white;font-size:8pt;">Ufficio Soci - 055 82553264 - soci@chiantibanca.it
                      </td>
                    </tr>
                    </table>';




			// link facilitato per risposta
			$mail_corpo .= "<br><small>Questa mail viene generata in maniera automatica. In caso di problemi, contattare Ufficio Soci.</small>";
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


// FINE CICLO ESTRAZIONE DATI + EMAIL
}

?>