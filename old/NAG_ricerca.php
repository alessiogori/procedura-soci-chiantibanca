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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// Nome Script
$NOME_SCRIPT = 'NAG_ricerca';

// Output File
$OUTPUT_FILE = $NOME_SCRIPT.'.json';

// Cancello il vecchio file e lo ri-creo
$DELETE_OLD_JSON = shell_exec('rm '.$OUTPUT_FILE);
$TOUCH_NEW_JSON = shell_exec('touch '.$OUTPUT_FILE);

// Inizializzo l'array $OUTPUT
$OUTPUT = Array();

// Estrazione dati

$select = "   SELECT idsocio, nag, concat(intestazione_a, ' ', intestazione_b) as Nominativo
							FROM sds_soci 
							ORDER BY Nominativo";

$query = mysqli_query($connection, $select);
		
	while($dati=mysqli_fetch_array($query)){ 

		$OUTPUT[] = $dati['idsocio'].' | '.$dati['nag'].' | '.$dati['Nominativo'];
	}

$OUTPUT_JSON =  json_encode($OUTPUT);

// Scrivo il contenuto della variabile nel file JSON
file_put_contents($OUTPUT_FILE, $OUTPUT_JSON);
