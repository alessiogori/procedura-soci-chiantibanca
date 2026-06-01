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

// Mi connetto al date(format)tabase
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

/*
if ($_POST['tipo'] == 'edit' ) 
	{

		$filiale 		= $_POST['filiale'];
		$psw 			= $_POST['psw'];

		$update_psw =	"UPDATE tab_psw
						 SET psw = '".$psw."' 
						 WHERE filiale = '".$filiale."'
						 ";

		echo $update_psw;

//		mysqli_query($connection, $update_psw) or die(mysql_error());;

	}

elseif (empty($_POST['tipo'])) {
*/
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$select_psw =	'SELECT 
					filiale, desc_filiale, email, psw
				 FROM tab_psw
				 ORDER BY filiale';

logquery ($select_psw);

$querydati = mysqli_query($connection, $select_psw);


// INTESTAZIONE TABELLA					
echo "<center><br>

		<b>Attenzione</b>: per la spedizione della mail e' necessario usare Internet Explorer Citrix ";

echo "	<fieldset style='width:500px;text-align:left;'>
			<legend>Gestione Password Filiali</legend>"; 

echo "<table width='100%'>
	  <thead> 
	  <tr>
		<th class='bgcolor_datatable_alt1' align='left'>FILIALE</th>
		<th class='bgcolor_datatable_alt1' align='left'>DENOMINAZIONE</th>
		<th class='bgcolor_datatable_alt1' align='right'>PASSWORD</th>
		<th class='bgcolor_datatable_alt1' align='center'></th>
	  </tr>
	  </thead> 
	  <tbody>
	  ";
						
	while($datipsw=mysqli_fetch_array($querydati)){ 


		echo "<tr>   
				<td class='bgcolor_datatable_alt2' align='left'>".$datipsw['filiale']."</td>
				<td class='bgcolor_datatable_alt2' align='left'>".$datipsw['desc_filiale']."</td>
				<td class='bgcolor_datatable_alt2' align='right' style='color:blue;'><b>".$datipsw['psw']."</b></td>
				<td class='bgcolor_datatable_alt2' align='left'>
					&nbsp;&nbsp;<a href='admin_psw_edit.php?azione=change&filiale=".$datipsw['filiale']."&psw=".$datipsw['psw']."&descfiliale=".$datipsw['desc_filiale']."'>
						<img src='img/edit.png' width='20' align='absmiddle'></a>
					&nbsp;
					<a href='mailto:?cc=direzione@chiantimutua.it&subject=".$datipsw['filiale']." ".$datipsw['desc_filiale']." - Password Portale ChiantiMutua&body=Buongiorno,%0D%0Aecco la password per accedere alla sezione riservata per la vostra Filiale:%0D%0A%0D%0A".$datipsw['psw']."'>
						<img src='img/ico_mail.png' width='20' align='absmiddle'></a>
				</td>
			 </tr>
			  "; 

	} 

	
echo "</tbody></table></fieldset>";

echo '	<br><br>
		<center>
			<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';


?>