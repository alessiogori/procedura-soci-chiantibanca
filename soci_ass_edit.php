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

$dataassemblea 	= '2019-05-12';

// Aggiorno il database

if ($_GET['azione'] == 'change') {
// Presento il form

echo "<center><br>";

echo "	<fieldset style='width:800px;text-align:left;'>
			<legend>Gestione Presenza Socio - Assemblea ".$dataassemblea."</legend>
				<form action='soci_ass_edit.php' method='POST'>"; 

echo "<table width='100%' border=0>
	  <thead> 
	  <tr>
		<th class='bgcolor_datatable_alt1' align='left'>NR.SOCIO</th>
		<th class='bgcolor_datatable_alt1' align='center'>PRESENTE</th>
		<th class='bgcolor_datatable_alt1' align='left'>LOCATION</th>
		<th class='bgcolor_datatable_alt1' align='center'>HA DATO DELEGA</th>
		<th class='bgcolor_datatable_alt1' style='background-color:lightyellow;' align='center'>HA RICEVUTO DELEGA</th>
		<th class='bgcolor_datatable_alt1' style='background-color:lightyellow;' align='center'>QTA DELEGHE<br>Ricevute</th>
		<th class='bgcolor_datatable_alt1' align='center'></th>
	  </tr>
	  </thead> 
	  <tbody>";

$select_ass =	"SELECT b.*
				 FROM tab_soci_scelta as b
				 where b.prot = ".$_GET['prot'] ;

logquery ($select_ass);

$querydati = mysqli_query($connection, $select_ass);

while($datiass=mysqli_fetch_array($querydati)){ 
	
echo "<tr>   
		<td class='bgcolor_datatable_alt2' align='left'>
			<input type=text name='prot' value='".$datiass['prot']."' readonly size=8 style='background:lightgray; color:blue;'>
		</td>
		<td class='bgcolor_datatable_alt2' align='center'>
				<select name='presente' value='".$datiass['presente']."'>
					<option>".$datiass['presente']."</option>
					<option></option>
					<option>SI</option>
					<option>NO</option>
				</select>
		<td class='bgcolor_datatable_alt2' align='left'>
				<select name='location' value='".$datiass['location']."'>
					<option>".$datiass['location']."</option>
					<option></option>
					<option>Fontebecci</option>
					<option>San Casciano</option>
					<option>Pistoia</option>
				</select>
		<td class='bgcolor_datatable_alt2' align='center'>
				<select name='datodelega' value='".$datiass['datodelega']."'>
					<option>".$datiass['datodelega']."</option>
					<option></option>
					<option>SI</option>
					<option>NO</option>
				</select>
		<td class='bgcolor_datatable_alt2' align='center'>
				<select name='ricevutodelega' value='".$datiass['ricevutodelega']."'>
					<option>".$datiass['ricevutodelega']."</option>
					<option></option>
					<option>SI</option>
					<option>NO</option>
				</select>
		</td>
		<td class='bgcolor_datatable_alt2' align='center'>
				<input type=number name='qtadeleghe' value='".$datiass['qtadeleghe']."' size=2 min=0 max=1>
		</td>
		<td class='bgcolor_datatable_alt2' align='left'>
			&nbsp;&nbsp;
			<input type='submit' value='Update'>
	 </tr>
	  "; 

}

echo "</tbody></table></fieldset></form>";

echo '	<br><br>
		<center>
			<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';

	} 

else {

$prot 			= $_POST['prot'];
$presente		= $_POST['presente'];
$location		= $_POST['location'];
$datodelega		= $_POST['datodelega'];
$ricevutodelega	= $_POST['ricevutodelega'];
$qtadeleghe		= $_POST['qtadeleghe'];

$update_psw =	"UPDATE tab_soci_scelta
				 SET presente = '".$presente."' ,
				 	 location = '".$location."' ,
				 	 datodelega = '".$datodelega."' ,
				 	 ricevutodelega = '".$ricevutodelega."' ,
				 	 qtadeleghe = '".$qtadeleghe."' 
				 WHERE prot = '".$prot."'
				 ";

logquery ($update_psw);

mysqli_query($connection, $update_psw) or die(mysql_error());;

echo '<br><center>Aggiornamento avvenuto!!
					<small>non tenere conto dell\'errore riportato qui sopra</small>
			</center>'; 

echo '	<br><br>
		<center>
			<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';

}

?>