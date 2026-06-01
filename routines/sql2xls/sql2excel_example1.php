<?php
	/* 
	 * @version V1.0 2002/July/18 (c) Erh-Wen,Kuo (erhwenkuo@yahoo.com). All rights reserved.
	 * purpose: This is a example how to use "sql2excel" class to write mysql sql content
	 *          to excel file format and stream the output to user's browser directly.
	 */
	 
	include_once('/WEBSITES/shared/sql2xls/sql2excel.class.php');
		
	// the query string you want to show
	// Estrazione delle ditte CCIAA in ordine di Denominazione
	$query = "SELECT ID, NAT_GIUR, DENOMINAZIONE, INDIRIZZO, COMUNE, TELEFONO, ATTIVITA ".
             "FROM CCIAA ORDER BY DENOMINAZIONE";
	
	//setup parameters for initiating Sql2Excel class instance
	//modify your mysql connection parameters & database name below:
	$db_host   = $hostname;
	$db_user   = $username;
	$db_pwd    = $password;
	$db_dbname = $database;
	
	//initiating Sql2Excel class instance
	$excel=new Sql2Excel($db_host,$db_user,$db_pwd,$db_dbname);

	//Output excel file to user's browser
	$excel->ExcelOutput($query);
	echo"<h1>File Excel generato completamente!!</h1>";
?>
