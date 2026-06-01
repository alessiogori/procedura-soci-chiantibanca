<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
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

function current_url()
{
    $url      = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $validURL = str_replace("&", "&amp", $url);
    return $validURL;
}
// echo "page URL is : ".current_url();
// echo $_SERVER['HTTP_REFERER'];
error_reporting (0);
ini_set('display_error', '0');

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// ------------------------------------
// Modelli relativi ai soci MUTUA
// ------------------------------------
    
// Estrazione dei modelli 
$select = "	SELECT rif, Codice, Descrizione, NomeFile
			FROM tab_modelli
			WHERE status = 'S'
			AND rif = 'MUTUA'
			ORDER BY Codice ";
$querydati = mysqli_query($connection, $select); 

if (isset($_GET['socio'])) {$socio = "Socio <b>".$_GET['socio']."</b>&nbsp;(cag ".$_GET['cag'].")";} else {$socio = '';} 

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
								'nag'=> $cag
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

	$indirizzo = "&ind=".urlencode(stripslashes($indirizzoResidenza))."&cap=".$capResidenza."&com=".urlencode(stripslashes($comuneResidenza))."&prov=".$provinciaResidenza;

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


echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">MODULISTICA MUTUA</h4>
      <small class="m-2 text-success">ATTENZIONE: per accedere al sito internet www.chiantimutua.it è necessario usare un browser diverso da Internet Explorer Citrix (consigliato Microsoft Edge Citrix)</small>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="dataTable" width="60%" cellspacing="0">
		<thead>
			<tr class="table-success">
				<td align="center" style="font-size: 16px;width:48%;vertical-align:top;">
								'.$socio.'
								<br>
				</td>
			</tr>
		</thead>
		<tbody>
';	

while($datimodelli=mysqli_fetch_array($querydati)){ 

			$luogo = 'San Casciano VDP';
			     
			if ( ($datimodelli['Codice'] == 'SO07') OR ($datimodelli['Codice'] == 'SO08') )
			{$action = '&action=print';}
			else
			{$action = '';}

			if(in_array($datimodelli['Codice'], array('001','002','003','004','005','006')) )
			        {$stilecolore = ' style="color:white;" ';
			        $visibilita = '<i class="fas fa-globe fa-1x text-gray-300 col-auto" title="Modello su sito internet"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="https://www.chiantimutua.it/modulistica" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			        
			elseif(in_array($datimodelli['Codice'], array('009','010','011','013','099','901')) )
                    {$stilecolore = ' style="color:white;" ';
			         $visibilita = '<i class="fas fa-lock-open fa-1x text-gray-300 col-auto" title="Modello libero"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="modulistica/'.$datimodelli['NomeFile'].'?tessera='.$_GET['tessera'].'&modello='.$datimodelli['Codice'].'&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.$indirizzo.'" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			else    
				
			        {$stilecolore = 'class="text-warning" ';
			         $visibilita = '<i class="fas fa-lock fa-1x text-gray-300 col-auto" title="Modello con password di accesso"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="modulistica/'.$datimodelli['NomeFile'].'?tessera='.$_GET['tessera'].'&modello='.$datimodelli['Codice'].'&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.$indirizzo.'" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			
echo '								
								'.$avvertenza.'
								'.$rigamodello.'
								';


}

echo '		</tbody>
	</table>';  

// echo '<a '.$stilecolore.' href="modulistica/001_sussidi_famiglia.php?tessera='.$_GET['tessera'].'&modello=001&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.$indirizzo.'" target="_blank"><b>test 001_sussidi_famiglia</a>';

echo '	
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

?>
<br/><br>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>



