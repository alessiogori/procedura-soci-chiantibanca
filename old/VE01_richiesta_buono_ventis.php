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

if ($_GET['richiesta'] == 'si')
{ 

$value = $_GET['filiale'];
$exploded_value = explode('|', $value);
$filiale_nome = $exploded_value[0];
$filiale_mail = $exploded_value[1];
$cag = $_GET['cag'];
$nominativo = $_GET['nominativo'];


	//No email sabato o domenica
	if(date('w')=="0" || date('w')=="6") return;
	
	//Parm email
	ini_set('SMTP', 'smtp.crtnet'); 
	ini_set('smtp_port', 25); 
	ini_set('sendmail_from','comunitaeterritori@chiantibanca.it');
	
	//Parm others
	//$mail_cc = $filiale_mail ; 
	$mail_cc = "alessiofedi@chiantibanca.it"; 
	$mail_dest = "comunitaeterritori@chiantibanca.it"; // sostituire con DCT
	$nome_mittente = $filiale_nome;
	$mail_mittente = $filiale_mail ; 

	$mail_oggetto = "";
			$mail_corpo = "<html><body>\r\n";
			$mail_corpo .= "<style type='text/css'>  body{font-family:'Courier New',Courier,monospace;font-size:9pt;} </style>";
	$mail_oggetto = "Richiesta Buono VENTIS da parte della Filiale ". $filiale_nome ." per ".$nominativo;
			$mail_corpo .= "E' stato richiesto di inviare un buono Ventis per il seguente nuovo Socio Under 30 :<br />\r\n";
			$mail_corpo .= "<br><br><h5>".$nominativo."</h5><br /><br />\r\n";
			$mail_corpo .= "<br><h6>CAG ".$cag."</h6><br /><br />\r\n";
			$mail_corpo .= "<br><br>La richiesta viene inviata alla Direzione Comunità e Territori, che provvederà a reinviare via mail alla Filiale il buono in formato PDF.<br>\r\n";
			$mail_corpo .= "<b>Sarà cura della Filiale stamparlo in doppia copia, consegnando la prima al Socio e reinviando - per posta interna - alla Direzione Comunità e Territori la seconda, firmata per ricezione dal Socio</b>.<br><br>\r\n";
  
			// link facilitato per risposta
			$mail_corpo .= "<br>--------------";
			$mail_corpo .= "<br>Riservato DCT: <a href='http://10.119.192.46:8080/soci/modulistica/VE01_invio_buono_ventis.php?filiale=".$filiale_nome."&cag=".$cag."&nominativo= ".$nominativo."'>Rispondi con invio buono</a>";

			$mail_corpo .= "</body></html>\r\n";
	
	$mail_headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
	$mail_headers .= "Reply-To: " . $mail_mittente . "\r\n";
	$mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
	$mail_headers .= "MIME-Version: 1.0\r\n";
	$mail_headers .= "Content-type: text/html; charset=UTF-8\r\n";
	$mail_headers .= "Content-Transfer-Encoding: base64";
	$mail_headers .= "CC: ".$mail_cc."\r\n";
	
	//$mail_oggetto_encoded = '=?UTF-8?B?' . base64_encode($mail_oggetto) . '?=';
	//$mail_corpo_encoded = base64_encode($mail_corpo);

	if (mail($mail_dest, $mail_oggetto, $mail_corpo, $mail_headers)) {
	  echo "<center>Messaggio inviato a " . $mail_dest . "<br />\r\n";
	} else {
	  echo "<center>Errore. Nessun messaggio inviato. <br />\r\n";
	}
	
}

else
{
// ********************************************************
// CREO IL FORM
// ********************************************************
echo '<form action="VE01_richiesta_buono_ventis.php" method="GET" onsubmit="return ray.ajax()">';
echo '<table border="0" align="center" cellpadding="0" cellspacing="0" width="30%" >
	<tr class="table-success">
		<td valign="top" align="center" ><h1>UNDER 30<br><img src="img/ventis.png"></td>
	</tr>
	<tr>
		<td valign="top" align="left"><br>CAG
		<input type="text" name="cag" id="cag" size="10" required class="form-control"></td>
	</tr>
	<tr>
		<td valign="top" align="left">Cognome e Nome
		<input type="text" name="nominativo" id="nominativo" size="60" required class="form-control"></td>
	</tr>
	<tr>
		<td valign="top" align="left">Filiale
		<select name="filiale" class="custom-select">
						<option></option>
	';


	// ********************************************************
	// CREO ELENCO A TENDINA DELLE FILIALI
	// ********************************************************
	$selectfiliale=	"SELECT CONCAT (Filiale,' ',Desc_Filiale) as Filiale, email_estesa as Email
					 FROM tab_psw
					 WHERE desc_filiale not like '%chiusa%' AND Filiale <> '099'
					 ORDER by 1
					 ";

	//logquery ($selectfiliale); 

	$querydatifiliale = mysqli_query($connection, $selectfiliale);	
	while($datifiliale=mysqli_fetch_array($querydatifiliale)){ 
		echo "<option value='".$datifiliale['Filiale']."|".$datifiliale['Email']."'>".$datifiliale['Filiale']."</option>";
	}

	echo '    </select>
		</td>
	</tr>
	';

echo '<tr>
		<td align="center"><br><br>
			<button type="submit" class="btn btn-success mb-2">Richiedi Buono Ventis per Under 30</button><br>
	  		<input type="hidden" class="form-control" name="richiesta" id="richiesta" value="si">
	  	</td>
	  </tr>
	  </table>

      </form>';

}



echo '    <center><br>
La richiesta viene inviata alla Direzione Comunità e Territori, che provvederà a reinviare via mail alla Filiale il buono in formato PDF.<br>
Sarà cura della Filiale stamparlo in doppia copia, consegnando la prima al Socio e reinviando - per posta interna - alla Direzione Comunità e Territori la seconda, firmata per ricezione dal Socio.
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png">
    </center>
 </body>
		</html>';

?>
