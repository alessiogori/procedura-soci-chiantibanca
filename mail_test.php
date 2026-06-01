<?php

 invia_email("ok_inserito","TEST");

	//email invio
	function invia_email($tipo_email, $opt){
		global $debug;
		
		//No email sabato o domenica
		if(date('w')=="0" || date('w')=="6") return;
		
		//Parm email
		ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
		ini_set('smtp_port', 25); 
		ini_set('sendmail_from','alessio.fedi@chiantibanca.it');
		
		//Parm others
		$mail_cc = "alessio.fedi@chiantibanca.it";  
		$mail_dest = "alessio.fedi@gmail.com"; 
		$nome_mittente = "Alessio Fedi by SMTP BCCSI";
		$mail_mittente = "alessio.fedi@chiantibanca.it";
		
		$mail_oggetto = "";
		$mail_corpo = "<html><body>\r\n";
		$mail_corpo .= "<style type='text/css'>  body{font-family:'Courier New',Courier,monospace;font-size:9pt;} </style>";
		switch ($tipo_email) {
			case "ok_inserito":
				$mail_oggetto = "Elenco Soci ChiantiMutua by API - Caricamento avvenuto per ".$opt." records";
				$mail_corpo .= "Sono stati inseriti nr.".$opt." records \r\n";
				break;
		}
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
		  if($debug) echo "Messaggio inviato a " . $mail_dest . "<br />\r\n";
		} else {
		  if($debug) echo "Errore. Nessun messaggio inviato. <br />\r\n";
		}
		
	}

?>