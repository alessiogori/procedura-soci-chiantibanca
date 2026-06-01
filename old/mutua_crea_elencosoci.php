<?php
// http://10.119.192.46:8080/mutua/testAPIelenco.php?dataRiferimento=
$ver = "v1.00 (21/05/2021) - Prima versione.";
ini_set('max_execution_time', 0); 

//Prendi dati
try {		
					
		/*
		 *  0. Inizializza OAuth COMIPA
		 */
		require('oauth/http.php');
		require('oauth/oauth_client.php');
		$client = new oauth_client_class;
		$client->debug = 1;
		$client->debug_http = 1;
		$client->server = 'comipa';
		$application_line = __LINE__;
		$client->client_id = 'COMIPA_08673'; 
		$client->client_secret = 'V6YYWZRL64YNZT79CDEVZ7M1R2SN1V3B';
		$codicemutua = 'chiantimutua';
		
		
		// ***************************
		//  1. Ricevo dataRiferimento
		// ***************************

		//$dataRiferimento = $_GET['dataRiferimento'];
		$dataRiferimento = date('Y-m-d');

		// ***************************
		//  2. Inizializza Connessione
		// ***************************

		$success = $client->Initialize();
		if($success){
			$success = $client->Process();
			if($success){
				//$client->ResetAccessToken();
				//exit;
				if(strlen($client->access_token)){
					//echo "Access Token:".$client->access_token." ";
					//echo "";
					
					// ***************************
					//  3. Chiamate API
					// ***************************
					 
					// 3.1 Richiesta Informazioni SOCIO
					//echo "&nbsp;>&nbsp;API elencosoci(): ";
					$success = $client->CallAPI(
						'https://services.sinergia.bcc.it/WiSeHub/rest/comipa/v1/elencosoci', 
						'GET', array(
							'abi'=>'08673',
							'codiceMutua'=> $codicemutua,
							'dataRiferimento'=> $dataRiferimento 
						), array('FailOnAccessError'=>true), $result);
					if(!$success) {
						echo "<b>ERRORE nel colloquio via API:</b>";
						echo "Chiamata: elencosoci()";
						echo "Result:";
						var_dump($result);
						echo "Client:";
						var_dump($client);
						throw new Exception("ERRORE nell'esecuzione delle chiamate API! (Vedere Log)");
					} else {
						//echo $result->codiceEsito . "";				
					}
					if(is_null($result->elencoSoci)){
							$elencoSoci = null; 
					} else { 
							$elencoSoci = $result->elencoSoci;
					}
					
					// 3.2 Altre eventuali richieste
					
					
					
				} else {
					echo "ERROR Access Token not found!";
					var_dump($client);
					throw new Exception("ERRORE nell'esecuzione delle chiamate API! (Vedere Log)");
				}
			} else {
				echo "ERROR Success var set to false after client->Process()!";
				var_dump($client);
				throw new Exception("ERRORE nell'esecuzione delle chiamate API! (Vedere Log)");
			}
			
			// Finalizza Connessione
			$success = $client->Finalize(true);
			
		} else {
			echo "ERROR Success var set to false after $client->Initialize()!";
			var_dump($client);
			throw new Exception("ERRORE nell'esecuzione delle chiamate API! (Vedere Log)");
		}
		
		
		if(strlen($client->authorization_error))
		{
			$client->error = $client->authorization_error;
			$success = false;
			throw new Exception("oAuth AUTH ERROR:".$client->error);
		}
		
		if(strlen($client->error)){
			throw new Exception("oAuth ERROR:".$client->error);
		}

} catch (Exception $e) {
	
	echo "----------- <br> Errore durante l'esecuzione dello script. <br> Messaggio restituito: ".  $e->getMessage(). " <br>";
	
	echo "Dump Dati ricevuti:";
	var_dump($dati_sib);
	echo "Dump Dati inviati:";
	var_dump($dati);

	exit;
}

//Usa Dati
//html..
//var_dump($elencoSoci);
//echo $elencoSoci->nome."<br>";


/*
foreach( $elencoSoci as $socio) {
	echo $socio->nome." ";
	echo $socio->cognome."<br>";
}
*/	
	

// Mi connetto al database
include("config/_config.php");
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// ---- Sego via tutti i records nella tabella 
$truncatetabella=mysqli_query($connection,"TRUNCATE tab_mutua_elencosoci") or die(mysql_error());;

foreach( $elencoSoci as $socio) {
    
    // Estraggo il sesso
    if (substr($socio->codiceFiscale, 9, 2) < 32)
    		{	$sesso = 'M'; 	}
    else 	
    		{	$sesso = 'F'; 	}
    		
    // Adeguo la lunghezza del CAG a 8 caratteri con zeri davanti
    $lunghezzacag = 8;
    if (strlen($socio->nag) < 8 ) {
     $cag = substr(str_repeat(0,$lunghezzacag).$socio->nag, - $lunghezzacag)  ; 
    }    
    else
    {
        $cag = $socio->nag;
    }
    echo $cag.'<br>';

		$insert_socio = '	INSERT into tab_mutua_elencosoci
		                    (
		                     idClasseTariffaria, 		
							 nomeClasseTariffaria,		
							 idSocio,		
							 cognome, 					
							 nome,					
							 codiceFiscale, 			
							 dataNascita,				
							 numeroSocio, 				
							 socioDal,					
							 numeroCartaMutuaSalus, 	
							 scadenzaCartaMutuaSalus,	
							 cag,
							 nag,
							 sesso
							) 
							VALUES 
							(
							"'.$socio->idClasseTariffaria.'", 		
							"'.$socio->nomeClasseTariffaria.'",		
							"'.$socio->idSocio.'",		
							"'.$socio->cognome.'", 					
							"'.$socio->nome.'",					
							"'.$socio->codiceFiscale.'", 			
							"'.$socio->dataNascita.'",				
							"'.$socio->numeroSocio.'", 				
							"'.$socio->socioDal.'",					
							"'.$socio->numeroCartaMutuaSalus.'", 	
							"'.$socio->scadenzaCartaMutuaSalus.'",	
							"'.$cag.'",	
							"'.$socio->nag.'",	
							"'.$sesso.'"
							)

						' ;

        //echo $insert_socio;
		mysqli_query($connection,$insert_socio) or die(mysql_error());;

}

    // Conto i records inseriti in tabella
    $select_count = "SELECT count(*) as qta from tab_mutua_elencosoci";
    $querydati = mysqli_query($connection, $select_count);
	while($conteggio=mysqli_fetch_array($querydati)){ 
        $records = $conteggio['qta'];
	}

    invia_email("ok_inserito",$records);

	//email invio
	function invia_email($tipo_email, $opt){
		global $debug;
		
		//No email sabato o domenica
		if(date('w')=="0" || date('w')=="6") return;
		
		//Parm email
		ini_set('SMTP', 'smtp.crtnet'); 
		ini_set('smtp_port', 25); 
		ini_set('sendmail_from','alessio.fedi@chiantibanca.it');
		
		//Parm others
		$mail_cc = "";  
		$mail_dest = "alessio.fedi@chiantibanca.it"; 
		$nome_mittente = "Alessio Fedi by Mutua";
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
	
// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento=mysqli_query($connection,"update tab_ultimo_caricamento set caricamento=now(), fonte='tab_mutua_elencosoci' WHERE fonte='tab_mutua_elencosoci'") or die(mysql_error());;
	

?>