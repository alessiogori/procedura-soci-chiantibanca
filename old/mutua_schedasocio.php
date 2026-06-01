<?php
// http://10.119.192.46:8080/mutua/testAPIsocio.php?cag=3033148
$ver = "v1.00 (21/05/2021) - Prima versione.";

// *****************************************************************************
// Portale ChiantiMutua
// Sviluppo e realizzazione: Alessio Fedi (2019)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

if (get_browser_name($_SERVER['HTTP_USER_AGENT']) == "Internet Explorer")
	{$imgext = "jpg";}
else
	{$imgext = "png";}

error_reporting (0);
ini_set('display_error', '0');

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

/*
object(stdClass)[5]
  public 'idClasseTariffaria' => string '2' (length=1)
  public 'nomeClasseTariffaria' => string 'Soci BCC' (length=8)
  public 'idSocio' => string '12433' (length=5)
  public 'cognome' => string 'FEDI' (length=4)
  public 'nome' => string 'ALESSIO' (length=7)
  public 'codiceFiscale' => string 'FDELSS71L30D612M' (length=16)
  public 'dataNascita' => string '1971-07-30' (length=10)
  public 'numeroSocio' => string '11536' (length=5)
  public 'socioDal' => string '2020-03-12' (length=10)
  public 'numeroCartaMutuaSalus' => string 'T2FI117349' (length=10)
  public 'scadenzaCartaMutuaSalus' => null
  public 'nag' => string '3033148' (length=7)
  
  public 'statusSocio' => string 'Attivo' (length=6)
  public 'socioAl' => null
  public 'cartaMutuaSalus' => null
  public 'indirizzoResidenza' => string 'VIA DEL GIRONE 9/A' (length=18)
  public 'capResidenza' => string '51100' (length=5)
  public 'comuneResidenza' => string 'PISTOIA' (length=7)
  public 'provinciaResidenza' => string 'PT' (length=2)
  public 'familiari' => 
    array (size=1)
      0 => 
        object(stdClass)[6]
          public 'idRelazione' => string '2' (length=1)
          public 'nomeRelazione' => string 'Figlio/a' (length=8)
          public 'cognome' => string 'FEDI' (length=4)
          public 'nome' => string 'REBECCA' (length=7)
          public 'dataNascita' => string '2015-03-26' (length=10)
*/

// ---------------------------------
// Estraggo le info dall'elenco soci
// ---------------------------------
$select = "	SELECT 
			idClasseTariffaria,
			nomeClasseTariffaria,
			idSocio, 
			cognome,
			nome, 
			codiceFiscale,
			date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') as dataNascita, 
			numeroSocio,
			date_format(STR_TO_DATE(socioDal, '%Y-%m-%d'),'%d/%m/%Y') as socioDal, 
			numeroCartaMutuaSalus, 
			scadenzaCartaMutuaSalus, 
			cag, 
			nag, 
			sesso 
			FROM tab_mutua_elencosoci
			WHERE cag = '".$_GET['cag']."'
			";

logquery ($select);  
//echo $select;
$querydati = mysqli_query($connection, $select);

while($dati=mysqli_fetch_array($querydati)){ 

	// -----------------------------
	// Controllo se è il compleanno
	// -----------------------------	
	$adesso = date('d/m'); 
	if (substr($dati['dataNascita'],0,5) == $adesso) 
		{$bday = '<i class="fas fa-birthday-cake fa-1x text-gray-300 col-auto"></i>';}
	else
		{$bday = '';}

	// ------------------------------
	// Presento la maschera del Socio
	// ------------------------------
	

	// ------------------------------------------------------------------------------------------------
	//   CHIAMATA API VERSO COMIPA
	// ------------------------------------------------------------------------------------------------

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
			//  1. Ricevo in GET il cag
			// ***************************

			$cag = ltrim($_GET['cag'], "0");

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
						//echo "&nbsp;>&nbsp;API richiestainformazionisocio(): ";
						$success = $client->CallAPI(
							'https://services.sinergia.bcc.it/WiSeHub/rest/comipa/v1/richiestainformazionisocio', 
							'GET', array(
								'abi'=>'08673',
								'codiceMutua'=> $codicemutua,
								'nag'=> $dati['nag']
							), array('FailOnAccessError'=>true), $result);
						if(!$success) {
							echo "<b>ERRORE nel colloquio via API:</b>";
							echo "Chiamata: richiestainformazionisocio()";
							echo "Result:";
							var_dump($result);
							echo "Client:";
							var_dump($client);
							throw new Exception("ERRORE nell'esecuzione delle chiamate API! (Vedere Log)");
						} else {
							//echo $result->codiceEsito . "";				
						}
						if(is_null($result->dettaglioSocio)){
								$dettaglioSocio = null; 
						} else { 
								$dettaglioSocio = $result->dettaglioSocio;
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
	//var_dump($dettaglioSocio);

	// --------------------- FINE CHIAMATA API --------------------

    $indirizzoResidenza = $dettaglioSocio->indirizzoResidenza ;
    $capResidenza = $dettaglioSocio->capResidenza ;
    $comuneResidenza = $dettaglioSocio->comuneResidenza ;
    $provinciaResidenza = $dettaglioSocio->provinciaResidenza ;

	$indirizzo = '<tr height="20">
		            <td width="15%" style="color:#A0B7CE;">Indirizzo</td><td colspan="3">'.$dettaglioSocio->indirizzoResidenza. ' - '.$dettaglioSocio->capResidenza. ' - '.$dettaglioSocio->comuneResidenza. ' - '.$dettaglioSocio->provinciaResidenza. '&nbsp;
			<a href="https://www.google.com/maps/search/?api=1&query='.$dettaglioSocio->indirizzoResidenza.'+'.$dettaglioSocio->capResidenza.'+'.$dettaglioSocio->comuneResidenza.'+'.$dettaglioSocio->provinciaResidenza.'" target="_blank">
			<i class="fas fa-map-marker-alt fa-1x text-gray-300 col-auto" alt="Vedi in Google Maps" title="Vedi in Google Maps"></i></a>
			</td>
		       </tr>';


// ---------------------------------
// se esistono, presento i Familiari
// ---------------------------------
//echo $dettaglioSocio->familiari[0]->nome."<br>";
$familiari = array_filter($dettaglioSocio->familiari);

/*
  public 'idRelazione' => string '2' (length=1)
  public 'nomeRelazione' => string 'Figlio/a' (length=8)
  public 'cognome' => string 'FEDI' (length=4)
  public 'nome' => string 'REBECCA' (length=7)
  public 'dataNascita' => string '2015-03-26' (length=10)
*/

if (!empty($familiari)) {

	$parenti = '<tr><td width="15%" style="border-bottom:dotted 1px;color:white;"></td><td colspan="3" style="border-bottom:dotted 1px;color:white;"><br></td></tr>'; // riga separazione

	$parenti .= '<tr height="20">
			<td colspan="4 style="color:#A0B7CE;""><br><h6>Sono presenti familiari</h6></td>
		  <tr>
		  <tr>
			<td colspan="2" style="color:#A0B7CE;">Cognome e Nome</td>
			<td style="color:#A0B7CE;">Relazione</td>
			<td style="color:#A0B7CE;">Data Nascita</td>
		  </tr>';

	foreach( $dettaglioSocio->familiari as $familiare) {
	    
		$etaf = ( date("Y") - substr($familiare->dataNascita,0,4) ); 

	    $parenti .= "<tr>";
		$parenti .= "<td colspan=2>".$familiare->cognome." ".$familiare->nome."</td>";
		$parenti .= "<td>".$familiare->nomeRelazione."</td>";
		$parenti .= "<td>".$familiare->dataNascita. "&nbsp;&nbsp;(".$etaf." anni)</td>";
	    $parenti .= "</tr>";
	}
}	
else
{
	$parenti .= '';
}
	

		// -------------------------------------------------------
		// SCHEDA ANAGRAFICA
		// -------------------------------------------------------
		    
        $datisocionominativo = urlencode($dati['cognome'].' '.$dati['nome']); 
        
		echo '<center>';
		echo "<fieldset style='width:900px;text-align:left;'>
				<!-- <legend style='color:#00478C;'>Dati Socio ChiantiMutua</legend> -->";
		echo '<div class="p-1 mb-2 text-left h5 bg-success text-white">DATI SOCIO CHIANTIMUTUA</div>';

		echo '<table width="100%" border="0" style="background-color:#222222;">';
		echo '<tr height="20">
				<td colspan="3"><b style="font-size:18px;">' .$dati['cognome'].' '.$dati['nome'].' '.$pallino.' '.$bday.'</b></td>
	    	    <td align="right" valign="top">
	    	    <a href="modulistica_mutua.php?mutua=si&tessera='.$dati['numeroCartaMutuaSalus'].'&cag='.$dati['cag'].'&socio='.urlencode(stripslashes($datisocionominativo)).'&idsocio='.$dati['idSocio'].'&ind='.urlencode(stripslashes($indirizzoResidenza)).'&cap='.$capResidenza.'&com='.urlencode(stripslashes($comuneResidenza)).'&prov='.$provinciaResidenza.'">
	    	    <i class="fas fa-file-signature fa-2x col-auto" style="color:#9FE2BF;" title="Modelli precompilati '.$datisocionominativo.'"></i></a></td>	
	    	    
				<td align="right" valign="top" rowspan="16" width="150">
				<img src="img/mutua_logo_bianco_full.png" width="150"><br><img src="img/icohome_casa.png" width="150"><br>';

		echo '	</td></tr>';
		echo '<tr height="20">
		            <td width="15%" style="color:#A0B7CE;">Numero Socio</td><td width="25%">'.$dati['numeroSocio']. '</td>
		            <td width="15%" style="color:#A0B7CE;">Socio dal</td><td width="25%">'.$dati['socioDal']. '</td>
		       </tr>';
		echo '<tr height="20">
		            <td width="15%" style="color:#A0B7CE;">Tessera</td><td width="25%">'.$dati['numeroCartaMutuaSalus']. '</td>
		            <td width="15%" style="color:#A0B7CE;">CAG</td><td width="25%">'.$dati['cag']. '</td>
		       </tr>';
		echo '<tr height="20">
		            <td width="15%" style="color:#A0B7CE;">Classe Tariffaria</td><td colspan="3">'.$dati['nomeClasseTariffaria'].' (id '.$dati['idClasseTariffaria'].')</td>
		       </tr>';

			$eta = ( date("Y") - substr($dati['dataNascita'],6,4) ); 

		echo '<tr height="20">
		            <td width="15%" style="color:#A0B7CE;">Codice Fiscale</td><td width="25%">'.$dati['codiceFiscale']. '</td>
		            <td width="15%" style="color:#A0B7CE;">Data di Nascita</td><td width="25%">'.$dati['dataNascita']. '&nbsp;&nbsp;('.$eta.' anni)</td>
		       </tr>';


	echo $indirizzo;
	echo $parenti;

	echo '</table>';
	echo '</fieldset></center><br>';

}
?>