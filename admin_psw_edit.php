<?php
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

// Aggiorno il database

if ($_GET['azione'] == 'change') {
// Presento il form

echo "<center><br>";

echo "	<fieldset style='width:800px;text-align:left;'>
			<legend>Gestione Password Filiali</legend>
				<form action='admin_psw_edit.php' method='POST'>"; 

echo "<table width='100%'>
	  <thead> 
	  <tr>
		<th class='bgcolor_datatable_alt1' align='left'>FILIALE</th>
		<th class='bgcolor_datatable_alt1' align='right'>PASSWORD</th>
		<th class='bgcolor_datatable_alt1' align='center'></th>
	  </tr>
	  </thead> 
	  <tbody>";

echo "<tr>   
		<td class='bgcolor_datatable_alt2' align='left'><input type='text' name='filiale' value='".$_GET['filiale']."' readonly style='background-color:#E9E9F4; border:none;'>
			".$_GET['descfiliale']."</td>
		<td class='bgcolor_datatable_alt2' align='right'><input type='text' name='psw' value='".$_GET['psw']."' size='12'></td>
		<td class='bgcolor_datatable_alt2' align='left'>
			&nbsp;&nbsp;
			<input type='submit' value='Update'>
		</td>
	 </tr>
	  "; 



echo "</tbody></table></fieldset></form>";

echo '	<br><br>
		<center>
			<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';

	} 

else {

$filiale 		= $_POST['filiale'];
$psw 			= $_POST['psw'];

$update_psw =	"UPDATE tab_psw
				 SET psw = '".$psw."' 
				 WHERE filiale = '".$filiale."'
				 ";

logquery ($update_psw);

mysqli_query($connection, $update_psw) or die(mysql_error());;

echo '<br><center>Aggiornamento avvenuto!!
					<small>non tenere conto dell\'errore riportato qui sopra</small>
			</center>'; 

echo '	<br><br>
		<center>
			<a href="admin_psw.php" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';

}

?>