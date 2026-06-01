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


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

echo ' <br>

		<table class="table table-danger table-hover" align="center" width="80%" border=1>
	  <thead> 
		<tr><th scope="col" colspan="7" align="center"><h3>Admin Upload Area</h3></td></tr>
		<tr >
			<th scope="col">Fonte</td>
			<th scope="col">Descrizione</td>
			<th scope="col">Cadenza</td>
			<th scope="col">Tabella</td>
			<th scope="col">Ultimo Agg.to</td>
			<th scope="col">Upload</td>
		</tr>
	  </thead> 
	  	<tbody>
		';

// CONTROLLO LE ULTIME DATE DI CARICAMENTO
$queryadm = "   SELECT fonte, descrizione, tipo, cadenza, DATE_FORMAT(caricamento,'%d/%m/%Y ore %H:%i') AS caricamento, nascosto
							FROM tab_ultimo_caricamento 
							WHERE nascosto <> 'S'
							ORDER BY nascosto, tipo, cadenza";

logquery ($queryadm);

$query = mysqli_query($connection, $queryadm);
		
	while($caricamento=mysqli_fetch_array($query)){ 

		
	if(in_array($caricamento['fonte'], array('sds_sinergiareport_soci','tab_mutua')) )
		{
			$coloretext = 'style="color:yellow;" ';
			// $coloreback = 'style="background-color:#F7F0DE;" ';
		}	
	else
		{
			$coloretext = '';
		}
		/*
		if ($caricamento['tipo'] == 'ATM') {$colore = 'style="background-color:#EDFAE3;" ';} 
		if ($caricamento['tipo'] == 'Carte') {$colore = 'style="background-color:#FAEAE5;" ';} 
		if ($caricamento['tipo'] == 'InBank') {$colore = 'style="background-color:#E0EDFA;" ';} 
		if ($caricamento['tipo'] == 'POS') {$colore = 'style="background-color:#F7F0DE;" ';} 
		*/

		echo '  <tr class="table-primary" height="40">
					<td align="left" '.$coloretext.'>'.$caricamento['tipo'].'</td>
					<td align="left" '.$coloretext.'>'.$caricamento['descrizione'].'</td>
					<td align="left" '.$coloretext.'>'.$caricamento['cadenza'].'</td>
					<td align="left" '.$coloretext.'>'.$caricamento['fonte'].'</td>
					<td align="center" '.$coloretext.'>'.$caricamento['caricamento'].'</td>';

		if ($caricamento['nascosto'] == 'X')
		{
			echo '<td align="center"></td>
				</tr>';
		}	else {
			echo '<td align="center" ><a href="upload/csv2sql_'.$caricamento['fonte'].'.php" target="_blank" title="Carica file"><img src="img/su.png"></a></td>
				</tr>
			'; 
		}	
				
	}

echo '	  </tbody></table>';
